<?php

namespace App\Components;

use Illuminate\Support\Facades\Gate;
use App\Components\Errors\InvalidArgumentException;
use App\Components\Errors\UnauthorizedException;
use App\Components\TokenGenerator;
use App\Components\Services\UserService;
use App\User;

class AuthHandler
{
    private $service;
    private $generator;

    public function __construct(UserService $service, TokenGenerator $tokenGenerator)
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
                throw new InvalidArgumentException();
            }
        }

        return $this->attemptCredentials($request);
    }

    private function attemptCredentials($request)
    {
        $this->checkIfUserHasAccessToPlatform(
            $request['email'], $request['device_name']
        );

        $credentials = array_diff_assoc(
            $request, ['device_name' => $request['device_name']]
        );

        if (! $token = $this->generator->generateJwt($credentials)) {
            throw new UnauthorizedException();
        }

        return $token;
    }

    private function checkIfUserHasAccessToPlatform($email, $device_name)
    {
        $user = $this->service->findByEmail($email);

        if (Gate::forUser($user)->denies('login', $device_name)) {
            throw new UnauthorizedException;
        }
    }
}
