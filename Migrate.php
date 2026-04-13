<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\Database;

$dotenv = Dotenv\Dotenv::createUnsafeMutable(__DIR__);
$dotenv->safeLoad();

$dsn      = $_ENV['DB_DSN']      ?? getenv('DB_DSN')      ?? '';
$user     = $_ENV['DB_USER']     ?? getenv('DB_USER')      ?? '';
$password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD')  ?? '';

echo "DSN: " . ($dsn ?: 'NOT SET') . PHP_EOL;
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