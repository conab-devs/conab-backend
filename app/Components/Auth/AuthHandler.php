<?php

namespace App\Components\Auth;

use App\Exceptions\ServerError;
use App\Exceptions\UnauthorizedException;
use App\Components\Auth\TokenGenerator\TokenGenerator;

class AuthHandler
{
    private $generator;

    public function __construct(TokenGenerator $tokenGenerator)
    {
        $this->generator = $tokenGenerator;
    }

    public function authenticate($request)
    {
        $arguments = ['email', 'password'];

        foreach ($arguments as $argument) {
            if (!array_key_exists($argument, $request)) {
                throw new ServerError("Ops, ocorreu um erro no servidor.");
            }
        }

        if (! $token = $this->generator->generate($request)) {
            throw new UnauthorizedException('Credenciais inválidas, tente novamente.');
        }

        return ['token' => $token];
    }
}
