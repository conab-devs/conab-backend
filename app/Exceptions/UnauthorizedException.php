<?php

namespace App\Exceptions;

class UnauthorizedException extends CustomException
{
    public $status = 401;

    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}