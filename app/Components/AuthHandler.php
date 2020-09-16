<?php

namespace App\Components;

use App\Components\Errors\ServerError;
use App\Components\Errors\UnauthorizedException;
use App\Components\TokenGenerator\TokenGenerator;

class AuthHandler
{
    private $generator;

    public function __construct(TokenGenerator $tokenGenerator)
    {
        $this->generator = $tokenGenerator;
    }

    public function authenticate($request)
    {
        $keysInArgumentOrder = ['email', 'password'];

        foreach ($keysInArgumentOrder as $argumentKey) {
            if (!array_key_exists($argumentKey, $request)) {
                throw new ServerError();
            }
        }

        if (! $token = $this->generator->generate($request)) {
            throw new UnauthorizedException();
        }

        return ['token' => $token];
    }
}
