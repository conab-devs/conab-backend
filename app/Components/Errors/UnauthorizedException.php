<?php

namespace App\Components\Errors;

class UnauthorizedException extends \Exception
{
    public $status = 401;

    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct("You don't have authorization to this resource", $code, $previous);
    }
}