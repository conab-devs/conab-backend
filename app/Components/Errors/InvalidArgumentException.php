<?php

namespace App\Components\Errors;

class InvalidArgumentException extends \Exception
{
    public $status = 500;

    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct('Server Error', $code, $previous);
    }
}