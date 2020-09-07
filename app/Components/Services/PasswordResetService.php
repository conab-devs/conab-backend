<?php

namespace App\Components\Services;

use App\PasswordReset;
use App\Components\TokenGenerator;

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

    public function find(array $params)
    {
        return $this->model->where($params)->first();
    }

    public function findByEmail(string $email)
    {
        return $this->queryByEmail($email)->first();
    }

    public function storePasswordResetRequest(string $email, string $token)
    {
        $this->model->fill(['email' => $email, 'token' => $token]);
        return $this->model->save();
    }
}