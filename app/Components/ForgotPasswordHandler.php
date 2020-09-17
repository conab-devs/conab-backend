<?php

namespace App\Components;

use App\Components\Errors\UnauthorizedException;
use App\Mail\ResetMail;
use App\Components\TokenGenerator\TokenGenerator;
use Illuminate\Support\Facades\Mail;
use App\PasswordReset;
use App\User;

class ForgotPasswordHandler
{
    private $reset;
    private $generator;
    private $user;

    public function __construct(PasswordReset $reset, TokenGenerator $generator, User $user)
    {
        $this->reset = $reset;
        $this->generator = $generator;
        $this->user = $user;
    }

    public function sendResetRequest(string $email)
    {
        $token = $this->generateToken($email);
        Mail::to($email)->send(new ResetMail($token));
    }

    public function resetPassword($info)
    {
        $query = $this->reset->where([
            'email' => $info['email'], 
            'token' => $info['token']
        ]);

        if (! $query->count()) {
            throw new UnauthorizedException('Nenhuma requisição de mudança de senha encontrada');
        }

        $user = $this->user->firstWhere('email', $info['email']);
        $user->update(['password' => $info['password']]);

        $resetRequest = $query->first();
        $resetRequest->delete();
    }
    
    public function generateToken(string $email)
    {
        $passwordRequest = $this->reset->firstWhere('email', $email);
        
        if ($passwordRequest) {
            return $passwordRequest->token;
        }

        $token = $this->generator->generate();

        $this->reset->fill(['email' => $email, 'token' => $token]);
        $this->reset->save();
        
        return $token;
    }
}