<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/Database.php';

use app\Database;

$dotenv = Dotenv\Dotenv::createUnsafeMutable(__DIR__);
$dotenv->safeLoad();

// TEMPORARY DEBUG — remove after fixing
echo "DSN: " . ($_ENV['DB_DSN'] ?? getenv('DB_DSN') ?? 'NOT SET') . PHP_EOL;
echo "USER: " . ($_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'NOT SET') . PHP_EOL;
echo "PASS: " . (!empty($_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD')) ? 'SET' : 'NOT SET') . PHP_EOL;

$dsn      = $_ENV['DB_DSN']      ?? getenv('DB_DSN')      ?? '';
$user     = $_ENV['DB_USER']     ?? getenv('DB_USER')      ?? '';
$password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD')  ?? '';

if (empty($dsn)) {
    echo "ERROR: DB_DSN is not set. Check your Railway PHP service variables." . PHP_EOL;
    exit(1);
}

$db = new Database([
    'dsn'      => $dsn,
    'user'     => $user,
    'password' => $password,
]);

$db->applyMigrations();