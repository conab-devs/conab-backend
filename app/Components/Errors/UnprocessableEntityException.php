<?php

namespace App\Components\Errors;
namespace App\Components\Errors\CustomException;

class UnprocessableEntityException extends CustomException
{
    public $status = 422;

    public function __construct($message = '', $code = 0, \Exception $previous = null)
    {
        parent::__construct("The sent fields are invalid", $code, $previous);
    }
}