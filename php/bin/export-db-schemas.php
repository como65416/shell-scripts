#!/usr/local/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__ . '/../')->load();

$mysql_path = null;
$maybe_mysql_paths = [
    '/usr/local/bin/mysqldump',
    '/usr/bin/mysqldump',
    '/usr/local/mysql/bin/mysqldump',
];

foreach ($maybe_mysql_paths as $maybe_mysql_path) {
    if (is_file($maybe_mysql_path)) {
        $mysql_path = $maybe_mysql_path;
        break;
    }
}
if ($mysql_path == null) {
    exit("\e[33m" . "Mysqldump not found" . "\e[0m" . PHP_EOL);
}

$database_name = $argv[1] ?? '';
$host = getenv('database.host');
$username = getenv('database.username');
$password = getenv('database.password');

// execute command
$cmd_argvs = [];
$cmd_argvs[] = "-p";
$cmd_argvs[] = "-B";
$cmd_argvs[] = "--no-data";
$cmd_argvs[] = "--host=" . $host;
$cmd_argvs[] = "--user=" . $username;
$cmd_argvs[] = "--password=" . $password;
if (!empty($database_name)) {
    $cmd_argvs[] = $database_name;
}
$content = shell_exec($mysql_path . ' ' . implode(' ', $cmd_argvs));

// remove comment
$content = preg_replace('/\/\*.*?\*\//', '', $content);
$content = preg_replace('/^;$/m', '', $content);
$content = preg_replace('/^--.*?$/m', "\n", $content);

// remove start and end newline
$content = preg_replace('/^[\n]+/', '', $content);
$content = preg_replace('/[\n]+$/', '', $content);

// remove redundant sql
$content = preg_replace('/ AUTO_INCREMENT=[0-9]+ /', ' ', $content);

// remove multiple newline
for ($i = 0; $i < 10; $i++) {
    $content = preg_replace('/\n\n\n/', "\n\n", $content);
}

echo $content . PHP_EOL;
