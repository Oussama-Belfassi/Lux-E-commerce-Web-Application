<?php

namespace app\middlewares;
use app\Router;

abstract class BaseMiddleware
{
    abstract public function execute(Router $router);
}
