<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Database.php'; // add this line

use app\Database;

$dotenv = Dotenv\Dotenv::createUnsafeMutable(__DIR__);
$dotenv->safeLoad();

$config = [
    'dsn'      => $_ENV['DB_DSN']      ?? getenv('DB_DSN'),
    'user'     => $_ENV['DB_USER']     ?? getenv('DB_USER'),
    'password' => $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD'),
];

$db = new Database($config);
$db->applyMigrations();