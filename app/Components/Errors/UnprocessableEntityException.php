<?php

namespace App\Components\Errors;

class UnprocessableEntityException extends \Exception
{
    public $status = 423;

    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct("The sent fields are invalid", $code, $previous);
    }
}