<?php

namespace App\Components\Services;

use App\Components\Errors\UnprocessableEntityException;
use App\User;

class UserService
{
    private $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function findByEmail(string $email)
    {
        $user = $this->model->where('email', $email)->first();
    
        if (! $user) {
            throw new UnprocessableEntityException();
        }

        return $user;
    }
}
