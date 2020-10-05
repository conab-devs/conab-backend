<?php

namespace App\Exceptions;

class ServerError extends CustomException
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        $this->status = 500;
        parent::__construct($message, $code, $previous);
    }
}
