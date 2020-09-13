#!/usr/local/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__ . '/../')->load();

$mysql_path = null;
$maybe_mysql_paths = [
    '/usr/local/bin/mysql',
    '/usr/bin/mysql',
    '/usr/local/mysql/bin/mysql',
];

foreach ($maybe_mysql_paths as $maybe_mysql_path) {
    if (is_file($maybe_mysql_path)) {
        $mysql_path = $maybe_mysql_path;
        break;
    }
}
if ($mysql_path == null) {
    exit("\e[33m" . "Mysql not found" . "\e[0m" . PHP_EOL);
}

$database_name = $argv[1] ?? '';
$host = getenv('database.host');
$username = getenv('database.username');
$password = getenv('database.password');

// execute command
$cmd_argvs = [];
$cmd_argvs[] = "--host=" . $host;
$cmd_argvs[] = "--user=" . $username;
$cmd_argvs[] = "--password=" . $password;
if (!empty($database_name)) {
    $cmd_argvs[] = $database_name;
}
pcntl_exec($mysql_path, $cmd_argvs);
