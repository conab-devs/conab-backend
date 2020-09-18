<?php

namespace App\Components;

use App\Exceptions\ServerError;
use App\Exceptions\UnauthorizedException;
use App\Components\TokenGenerator\TokenGenerator;
use App\Mail\ResetMail;
use App\PasswordReset;
use App\User;
use Illuminate\Support\Facades\Mail;

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
        Mail::to($email)->send(new ResetMail($token, $email));
    }

    public function resetPassword($request)
    {
        $arguments = ['email', 'token'];

        foreach ($arguments as $argument) {
            if (!array_key_exists($argument, $request)) {
                throw new ServerError("Ops, ocorreu um erro no servidor.");
            }
        }

        $query = $this->reset->where([
            'email' => $request['email'],
            'token' => $request['token'],
        ]);

        if (!$query->count()) {
            throw new UnauthorizedException('Nenhuma requisição de mudança de senha encontrada');
        }

        $user = $this->user->firstWhere('email', $request['email']);
        $user->update(['password' => $request['password']]);

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
