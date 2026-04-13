<?php

namespace app\middlewares;

use app\exceptions\ForbiddenException;
use app\Router;

class AuthMiddleware extends BaseMiddleware
{
    public array $actions;

    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    public function execute(Router $router): void
    {
        $isGuest = $router->isGuest();

        if ($isGuest) {
            if (empty($this->actions)) {
                throw new ForbiddenException();
            }

            $currentAction = strtolower($router->currentAction);
            $protectedActions = array_map('strtolower', $this->actions);

            if (in_array($currentAction, $protectedActions, true)) {
                throw new ForbiddenException();
            }
        }
    }
}
