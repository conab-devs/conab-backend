<?php

namespace App\Exceptions;

class CustomException extends \Exception {
    public $status = 401;
}