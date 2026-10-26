#!/usr/bin/env bash

# Shared helpers for the host-side DDEV commands that read the production DB.

read_project_env() {
    local key="$1"
    local env_file="${DDEV_APPROOT:-$(pwd -P)}/.env"

    [[ -f "$env_file" ]] || return 0

    awk -v key="$key" '
        $0 ~ "^[[:space:]]*" key "[[:space:]]*=" {
            sub("^[[:space:]]*" key "[[:space:]]*=[[:space:]]*", "")
            sub(/[[:space:]]+$/, "")
            if (($0 ~ /^".*"$/) || ($0 ~ /^\047.*\047$/)) {
                print substr($0, 2, length($0) - 2)
            } else {
                sub(/[[:space:]]+#.*$/, "")
                print
            }
            exit
        }
    ' "$env_file"
}

production_db_config() {
    PRODUCTION_SSH_URL="${PRODUCTION_SSH_URL:-$(read_project_env PRODUCTION_SSH_URL)}"
    PRODUCTION_PROJECT_PATH="${PRODUCTION_PROJECT_PATH:-$(read_project_env PRODUCTION_PROJECT_PATH)}"
    PRODUCTION_SSH_PORT="${PRODUCTION_SSH_PORT:-$(read_project_env PRODUCTION_SSH_PORT)}"
    PRODUCTION_SSH_IDENTITY_FILE="${PRODUCTION_SSH_IDENTITY_FILE:-$(read_project_env PRODUCTION_SSH_IDENTITY_FILE)}"

    if [[ -z "$PRODUCTION_SSH_URL" || -z "$PRODUCTION_PROJECT_PATH" ]]; then
        cat >&2 <<'EOF'
Production database access is not configured.
Add these values to .env:
  PRODUCTION_SSH_URL=user@production-host
  PRODUCTION_PROJECT_PATH=/absolute/path/to/the/craft-project

Optional values:
  PRODUCTION_SSH_PORT=22
  PRODUCTION_SSH_IDENTITY_FILE=/absolute/path/to/private-key
EOF
        return 1
    fi

    # Keep password prompts enabled. Key-based authentication still works too.
    PRODUCTION_SSH_OPTIONS=(-o ConnectTimeout=15)
    if [[ -n "$PRODUCTION_SSH_PORT" ]]; then
        PRODUCTION_SSH_OPTIONS+=(-p "$PRODUCTION_SSH_PORT")
    fi
    if [[ -n "$PRODUCTION_SSH_IDENTITY_FILE" ]]; then
        PRODUCTION_SSH_OPTIONS+=(-i "$PRODUCTION_SSH_IDENTITY_FILE")
    fi
}

download_production_db() {
    local destination="$1"
    local remote_dump="/tmp/${DDEV_SITENAME:-craft}-$(date '+%Y%m%d-%H%M%S')-$$.sql"
    local project_path_quoted remote_dump_quoted

    production_db_config
    command -v ssh >/dev/null 2>&1 || {
        echo "Error: ssh is required but was not found." >&2
        return 1
    }
    command -v gzip >/dev/null 2>&1 || {
        echo "Error: gzip is required but was not found." >&2
        return 1
    }

    printf -v project_path_quoted '%q' "$PRODUCTION_PROJECT_PATH"
    printf -v remote_dump_quoted '%q' "$remote_dump"

    echo "Connecting to production and downloading the database..."
    if ! ssh "${PRODUCTION_SSH_OPTIONS[@]}" "$PRODUCTION_SSH_URL" \
        "cd $project_path_quoted && test -f craft && php craft db/backup $remote_dump_quoted --overwrite=1 --interactive=0 >/dev/null && cat $remote_dump_quoted; status=\$?; rm -f $remote_dump_quoted; exit \$status" \
        | gzip -c > "$destination"; then
        rm -f "$destination"
        return 1
    fi

    gzip -t "$destination"
}
