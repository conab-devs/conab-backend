<?php

namespace App\Components\Errors;

class InvalidFieldException extends \Exception
{
    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct('Invalid field', $code, $previous);
    }

    public function __toString()
    {
        return $this->message;
    }
}