<?php

namespace App\Components\Services;

use App\PasswordReset;
use App\Components\TokenGenerator;

class PasswordResetService
{
    private $model;

    public function __construct(PasswordReset $reset, TokenGenerator $tokenGenerator)
    {
        $this->model = $reset;
        $this->generator = $tokenGenerator;
    }

    public function queryByEmail(string $email)
    {
        return $this->model->where('email', $email);
    }

    public function findByEmail(string $email)
    {
        return $this->queryByEmail($email)->first();
    }

    public function storePasswordResetRequest(string $email)
    {
        $token = $this->generator->generate();

        $this->model->fill(['email' => $email, 'token' => $token]);
        return $this->model->save();
    }
}