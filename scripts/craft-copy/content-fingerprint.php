#!/usr/bin/env php
<?php

declare(strict_types=1);

use craft\db\Query;

$projectRoot = dirname(__DIR__, 2);
$bootstrap = $projectRoot . '/bootstrap.php';

if (is_file($bootstrap)) {
    require $bootstrap;
} else {
    define('CRAFT_BASE_PATH', $projectRoot);
    define('CRAFT_VENDOR_PATH', CRAFT_BASE_PATH . '/vendor');
    require_once CRAFT_VENDOR_PATH . '/autoload.php';

    if (class_exists(Dotenv\Dotenv::class) && is_file(CRAFT_BASE_PATH . '/.env')) {
        if (method_exists(Dotenv\Dotenv::class, 'createUnsafeMutable')) {
            Dotenv\Dotenv::createUnsafeMutable(CRAFT_BASE_PATH)->safeLoad();
        } else {
            Dotenv\Dotenv::create(CRAFT_BASE_PATH)->load();
        }
    }
}

/** @var craft\console\Application $app */
$app = require CRAFT_VENDOR_PATH . '/craftcms/cms/bootstrap/console.php';
$db = $app->getDb();

$tables = [
    '{{%addresses}}',
    '{{%assets}}',
    '{{%assets_sites}}',
    '{{%contentblocks}}',
    '{{%elements}}',
    '{{%elements_sites}}',
    '{{%entries}}',
    '{{%matrixblocks}}',
    '{{%relations}}',
    '{{%tags}}',
    '{{%users}}',
];

$hash = hash_init('sha256');
$counts = [];

foreach ($tables as $table) {
    $schema = $db->getTableSchema($table, true);
    if ($schema === null) {
        continue;
    }

    $name = $schema->fullName;
    $orderColumns = $schema->primaryKey;
    if ($orderColumns === []) {
        $orderColumns = array_keys($schema->columns);
    }
    $orderBy = array_fill_keys($orderColumns, SORT_ASC);

    $query = (new Query())->from($table)->orderBy($orderBy);
    $count = 0;

    hash_update($hash, "table\0{$name}\0");
    foreach ($query->each(500, $db) as $row) {
        hash_update(
            $hash,
            json_encode($row, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n"
        );
        ++$count;
    }

    $counts[$name] = $count;
}

$maxDateUpdated = (new Query())
    ->from('{{%elements}}')
    ->max('dateUpdated', $db);

echo json_encode([
    'fingerprint' => hash_final($hash),
    'maxDateUpdated' => $maxDateUpdated,
    'counts' => $counts,
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL;
