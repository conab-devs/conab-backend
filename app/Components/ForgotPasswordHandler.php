<?php

namespace App\Components;

use App\Components\Services\PasswordResetService;
use App\Components\Errors\ServerError;
use App\Mail\ResetMail;
use TokenGenerator;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordHandler
{
    private $service;
    private $generator;

    public function __construct(PasswordResetService $service, $generator)
    {
        $this->service = $service;
        $this->generator = $generator;
    }

    public function sendResetRequest(string $email)
    {
        $token = $this->generateToken($email);
        Mail::to($email)->send(new ResetMail($token));
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