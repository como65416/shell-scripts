#!/usr/local/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__ . '/../')->load();

if (count($argv) < 3) {
    echo "input command error!" . PHP_EOL;
    echo "format : php db_data_dump.php database_name sql [with_title_or_not(true or 1 or yes => with_title)]" . PHP_EOL;
    echo "example : php db_data_dump.php shop \"select * from item limit 1;\" true" . PHP_EOL;
    exit;
}

$mysql_path = null;
$maybe_mysql_paths = [
    "/usr/local/bin/mysql",
    "/usr/bin/mysql",
    "/usr/local/mysql/bin/mysql",
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


$database_name = $argv[1];
$sql = $argv[2];
$with_title = (isset($argv[3]) && in_array(strtolower($argv[3]), ['true', 'yes', 1])) ? true : false;
$host = getenv('database.host');
$username = getenv('database.username');
$password = getenv('database.password');

$cmd_argvs = [];
$cmd_argvs[] = "--host=" . $host;
$cmd_argvs[] = "--user=" . $username;
$cmd_argvs[] = "--password=" . $password;
$cmd_argvs[] = $database_name;
$cmd_argvs[] = ($with_title) ? "-Be" : "-BNe";
$cmd_argvs[] = $sql;
pcntl_exec($mysql_path, $cmd_argvs);
