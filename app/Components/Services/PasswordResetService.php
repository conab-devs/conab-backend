<?php

namespace App\Components\Services;

use App\PasswordReset;

class PasswordResetService
{
    private $model;

    public function __construct(PasswordReset $reset)
    {
        $this->model = $reset;
    }

    public function queryByEmail(string $email)
    {
        return $this->model->where('email', $email);
    }

    public function findByEmail(string $email)
    {
        return $this->queryByEmail($email)->first();
    }
}