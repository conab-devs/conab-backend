<?php

namespace App\Components;

use Illuminate\Support\Facades\Gate;
use App\Components\Errors\ServerError;
use App\Components\Errors\UnauthorizedException;
use App\Components\TokenGenerator;
use App\Components\Repositorys\UserRepository;

class AuthHandler
{
    private $service;
    private $generator;

    public function __construct(UserRepository $service, TokenGenerator $tokenGenerator)
    {
        $this->service = $service;
        $this->generator = $tokenGenerator;
    }

    public function authenticate($request)
    {
        $keysInArgumentOrder = ['email', 'password', 'device_name'];

        if (func_num_args() === 3) {
            $request = array_combine(
                $keysInArgumentOrder, func_get_args()
            );
        }

        foreach ($keysInArgumentOrder as $argumentKey) {
            if (!array_key_exists($argumentKey, $request)) {
                throw new ServerError();
            }
        }

        return $this->attemptCredentials($request);
    }

    private function attemptCredentials($request)
    {
        $user = $this->findUserIfHeIsAuthorized(
            $request['email'], $request['device_name']
        );

        $credentials = array_diff_assoc(
            $request, ['device_name' => $request['device_name']]
        );

        if (! $token = $this->generator->generateJwt($credentials)) {
            throw new UnauthorizedException();
        }

        return ['token' => $token, 'user' => $user];
    }

    private function findUserIfHeIsAuthorized($email, $device_name)
    {
        $user = $this->service->findByEmail($email);

        if (Gate::forUser($user)->denies('login', $device_name)) {
            throw new UnauthorizedException;
        }

        return $user;
    }
}
