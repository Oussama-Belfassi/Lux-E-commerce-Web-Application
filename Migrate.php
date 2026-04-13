<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\Database;

// DO NOT load .env on Railway — variables are injected directly by the platform
// Only load .env if we are running locally
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createUnsafeMutable(__DIR__);
    $dotenv->safeLoad();
}

$dsn      = getenv('DB_DSN')      ?: ($_ENV['DB_DSN']      ?? '');
$user     = getenv('DB_USER')     ?: ($_ENV['DB_USER']      ?? '');
$password = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD']  ?? '');

echo "DSN: "  . ($dsn  ?: 'NOT SET') . PHP_EOL;
echo "USER: " . ($user ?: 'NOT SET') . PHP_EOL;
echo "PASS: " . ($password ? 'SET' : 'NOT SET') . PHP_EOL;

if (empty($dsn)) {
    echo "ERROR: DB_DSN is not set." . PHP_EOL;
    exit(1);
}

$db = new Database([
    'dsn'      => $dsn,
    'user'     => $user,
    'password' => $password,
]);

$db->applyMigrations();