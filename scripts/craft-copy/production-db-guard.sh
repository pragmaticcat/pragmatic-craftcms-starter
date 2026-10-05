#!/usr/bin/env bash

set -euo pipefail

readonly ACTION="${1:-}"
readonly STAGE="${2:-production}"
readonly PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
readonly CONFIG_FILE="${PROJECT_ROOT}/config/craft-copy/fortrabbit.${STAGE}.yaml"
readonly FINGERPRINT_SCRIPT="scripts/craft-copy/content-fingerprint.php"
readonly STATE_FILE="${PROJECT_ROOT}/storage/runtime/craft-copy-${STAGE}-production.json"

fail() {
    printf 'ERROR: %s\n' "$*" >&2
    exit 1
}

read_fingerprint() {
    php -r '
        $data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($data["fingerprint"]) || !is_string($data["fingerprint"])) {
            throw new RuntimeException("Invalid fingerprint data");
        }
        echo $data["fingerprint"];
    ' "$1"
}

read_updated_at() {
    php -r '
        $data = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);
        echo $data["maxDateUpdated"] ?? "unknown";
    ' "$1"
}

read_ssh_remote() {
    php -r '
        require $argv[1] . "/vendor/autoload.php";
        $config = Symfony\Component\Yaml\Yaml::parseFile($argv[2]);
        if (empty($config["ssh_url"])) {
            throw new RuntimeException("ssh_url is missing from Craft Copy config");
        }
        echo $config["ssh_url"];
    ' "$PROJECT_ROOT" "$CONFIG_FILE"
}

fetch_remote_fingerprint() {
    local target_file="$1"
    local ssh_remote

    ssh_remote="$(read_ssh_remote)"
    ssh "$ssh_remote" "php ${FINGERPRINT_SCRIPT}" > "$target_file"
    read_fingerprint "$target_file" >/dev/null
}

case "$ACTION" in
    record)
        mkdir -p "$(dirname "$STATE_FILE")"
        temporary_file="${STATE_FILE}.tmp"
        php "${PROJECT_ROOT}/${FINGERPRINT_SCRIPT}" > "$temporary_file"
        read_fingerprint "$temporary_file" >/dev/null || fail "Could not create the production baseline."
        mv "$temporary_file" "$STATE_FILE"
        printf 'Saved production content baseline (%s).\n' "$(read_updated_at "$STATE_FILE")"
        ;;

    record-remote)
        [[ -f "$CONFIG_FILE" ]] || fail "Craft Copy config not found: ${CONFIG_FILE}"
        mkdir -p "$(dirname "$STATE_FILE")"
        temporary_file="${STATE_FILE}.tmp"
        trap 'rm -f "$temporary_file"' EXIT

        if ! fetch_remote_fingerprint "$temporary_file"; then
            fail "Could not read the production fingerprint. The baseline was not changed."
        fi

        mv "$temporary_file" "$STATE_FILE"
        trap - EXIT
        printf 'Saved remote production baseline (%s).\n' "$(read_updated_at "$STATE_FILE")"
        ;;

    check)
        [[ -f "$STATE_FILE" ]] || fail "No production baseline exists. Run 'ddev craft copy/db/down ${STAGE}' before any db/up."
        [[ -f "$CONFIG_FILE" ]] || fail "Craft Copy config not found: ${CONFIG_FILE}"

        remote_file="$(mktemp)"
        trap 'rm -f "$remote_file"' EXIT

        if ! fetch_remote_fingerprint "$remote_file"; then
            fail "Could not calculate the current production fingerprint. Upload has been cancelled."
        fi

        baseline="$(read_fingerprint "$STATE_FILE")" || fail "The saved production baseline is invalid."
        current="$(read_fingerprint "$remote_file")" || fail "The production fingerprint response is invalid."

        if [[ "$baseline" != "$current" ]]; then
            baseline_date="$(read_updated_at "$STATE_FILE")"
            current_date="$(read_updated_at "$remote_file")"
            fail "Production changed after your last db/down (baseline: ${baseline_date}; current: ${current_date}). The db/up has been cancelled to protect newer content."
        fi

        printf 'Production is unchanged since the last db/down (%s). Safe to continue.\n' "$(read_updated_at "$STATE_FILE")"
        ;;

    *)
        fail "Usage: $0 {record|record-remote|check} [stage]"
        ;;
esac
