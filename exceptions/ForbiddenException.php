<?php

namespace app\exceptions;

class ForbiddenException extends \Exception
{
    protected $code = 403;
    protected $message = "you Don't have access to this Page";
}
