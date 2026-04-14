<?php

use app\controller\PagesController;
use app\models\Users;
use app\Router;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$config = [
    'userClass' => Users::class,
    'db' => [
        'dsn'      => $_ENV['DB_DSN'],
        'user'     => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ],
];

// Router is constructed first so the session and DB are fully ready.
// PagesController is constructed after so any middleware that might depend
// on the router's state in the future is safe.
//___________________________________________________________________________

$router = new Router($config);
$pages = new PagesController();

$router->get('/', [$pages, 'login']);
$router->post('/', [$pages, 'login']);
$router->get('/register', [$pages, 'register']);
$router->post('/register', [$pages, 'register']);
$router->get('/home', [$pages, 'home']);
$router->post('/logout', [$pages, 'logout']);
$router->get('/contact',  [$pages, 'contact']);
$router->post('/contact', [$pages, 'contact']);
$router->get('/auth/google', [$pages, 'googleRedirect']);
$router->get('/auth/google/callback', [$pages, 'googleCallback']);
$router->get('/forgot-password',   [$pages, 'forgotPassword']);
$router->post('/forgot-password',  [$pages, 'forgotPassword']);
$router->get('/reset-password',    [$pages, 'resetPassword']);
$router->post('/reset-password',   [$pages, 'resetPassword']);

try {
    $router->resolve();
} catch (\app\exceptions\ForbiddenException $e) {
    http_response_code(403);
    $router->renderView('pages/403', [
        'title' => '403 Forbidden',
        'file'  => '403',
    ]);
} catch (\Exception $e) {
    error_log('Unhandled exception: ' . $e->getMessage());
    http_response_code(500);
    $router->renderView('pages/500', [
        'title' => '500 — Server Error',
        'file'  => '500',
    ]);
}