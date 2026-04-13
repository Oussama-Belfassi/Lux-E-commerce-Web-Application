<?php

namespace app;

use app\models\Users;

class Router
{
    public array    $postUrl = [];
    public array    $getUrl = [];
    public Database $db;
    public Session  $session;
    public ?Users   $user = null;
    public string   $userClass;
    public string   $currentAction = '';

    public function __construct(array $config)
    {
        $this->db = new Database($config['db']);
        $this->session = new Session();
        $this->userClass = $config['userClass'];

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            if (class_exists($this->userClass)) {
                $this->user = ($this->userClass)::getUserById((int) $primaryValue);
            }
        }
    }

    public function post(string $url, array|callable $callback): void
    {
        $this->postUrl[$url] = $callback;
    }

    public function get(string $url, array|callable $callback): void
    {
        $this->getUrl[$url] = $callback;
    }

    public function resolve(): void
    {
        $currUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method  = $_SERVER['REQUEST_METHOD'];

        if ($method !== 'GET' && $method !== 'POST') {
            http_response_code(405);
            $this->renderView('pages/404', [
                'title' => '405 — Method Not Allowed',
                'file'  => '404',
            ]);
            return;
        }

        $callback = $method === 'GET'
            ? ($this->getUrl[$currUrl]  ?? null)
            : ($this->postUrl[$currUrl] ?? null);

        if ($callback === null) {
            http_response_code(404);
            $this->renderView('pages/404', [
                'title' => '404 — Not found',
                'file'  => '404',
            ]);
            return;
        }

        if (is_array($callback)) {
            $controller         = $callback[0];
            $controller->action = $callback[1];
            $this->currentAction = $callback[1];

            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute($this);
            }

            call_user_func([$callback[0], $callback[1]], $this);
            return;
        }

        if (!is_callable($callback)) {
            http_response_code(500);
            $this->renderView('pages/500', [
                'title' => '500 — Server Error',
                'file'  => '500',
            ]);
            return;
        }

        call_user_func($callback, $this);
    }

    public function renderView(string $view, array $params = []): void
    {
        // Reserved layout variables — these are set after param extraction so they
        // can never be overwritten by caller-supplied params.

        $flashSuccess = $this->session->getFlash('success');
        $isGuest      = $this->isGuest();
        $csrfToken    = $this->session->getCsrfToken();

        foreach ($params as $key => $value) {
            // Guard against overwriting reserved layout variables
            if (in_array($key, ['flashSuccess', 'isGuest', 'content', 'csrfToken'], true)) {
                continue;
            }
            $$key = $value;
        }

        ob_start();
        include __DIR__ . "/views/$view.php";
        $content = ob_get_clean();
        include __DIR__ . '/views/_layout.php';
    }

    public function login(Users $user): bool
    {
        // FIX: regenerate the session ID on every login to prevent session fixation —
        // an attacker who obtained the session ID before login cannot reuse it after.
        session_regenerate_id(true);
        $this->user = $user;
        $this->session->set('user', $user->id);
        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        
        
        // FIX: fully destroy the session instead of just unsetting the user key.
        // Only removing 'user' left the session ID alive in the browser cookie —
        // the middleware saw $router->user === null but the old session data
        // (including the CSRF token) was still valid, allowing re-use.
        $this->session->destroy();
    }

    public function isGuest(): bool
    {
        return $this->user === null;
    }
}
