<?php

namespace App\Components;

use App\Components\Repositorys\PasswordResetRepository;
use App\Components\Repositorys\UserRepository;
use App\Components\Errors\ServerError;
use App\Components\Errors\UnauthorizedException;
use App\Mail\ResetMail;
use TokenGenerator;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordHandler
{
    private $service;
    private $generator;
    private $userService;

    public function __construct(PasswordResetRepository $service, $generator, UserRepository $userService)
    {
        $this->service = $service;
        $this->generator = $generator;
        $this->userService = $userService;
    }

    public function sendResetRequest(string $email)
    {
        $token = $this->generateToken($email);
        Mail::to($email)->send(new ResetMail($token));
    }

    public function resetPassword($info)
    {
        $query = $this->service->find([
            'email' => $info['email'], 
            'token' => $info['token']
        ]);

        if (! $query->count()) {
            throw new UnauthorizedException();
        }

        $user = $this->userService->findByEmail($info['email']);
        $user->update(['password' => $info['password']]);

        $resetRequest = $query->first();
        $resetRequest->delete();
    }
    
    public function generateToken(string $email)
    {
        $passwordRequest = $this->service->findByEmail($email);

        if ($passwordRequest) {
            return $passwordRequest->token;
        }

        $token = $this->generator->generate();

        if (! $this->service->storePasswordResetRequest($email, $token)) {
            throw new ServerError();
        }
        
        return $token;
    }
}