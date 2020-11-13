<?php

namespace App\Components\Auth;

use App\Exceptions\ServerError;
use App\Exceptions\UnauthorizedException;
use App\Components\Auth\TokenGenerator\TokenGenerator;
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
        $code = $this->generateToken($email);
        Mail::to($email)->send(new ResetMail($code));
    }

    public function resetPassword($request)
    {
        $arguments = ['email', 'code'];

        foreach ($arguments as $argument) {
            if (!array_key_exists($argument, $request)) {
                throw new ServerError("Ops, ocorreu um erro no servidor.");
            }
        }

        $query = $this->reset->where([
            'email' => $request['email'],
            'code' => $request['code'],
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
            return $passwordRequest->code;
        }

        $code = $this->generator->generate();

        $this->reset->fill(['email' => $email, 'code' => $code]);
        $this->reset->save();

        return $code;
    }
}
