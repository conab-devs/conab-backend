<?php

namespace App\Components\Repositorys;

use App\PasswordReset;
use App\Components\TokenGenerator;

class PasswordResetRepository
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
        return $this->model->where($params);
    }

    public function findByEmail(string $email)
    {
        return $this->queryByEmail($email)->first();
    }

}