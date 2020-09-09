<?php

namespace App\Components\Repositorys;

use App\Components\Errors\UnprocessableEntityException;
use App\User;

class UserRepository
{
    private $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function findByEmail(string $email)
    {
        return $this->model->where('email', $email)->first();

    }
}
