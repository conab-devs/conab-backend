<?php

namespace App\Http\Controllers;

use App\Components\Auth\AuthHandler;
use App\Components\Auth\ForgotPasswordHandler;
use App\Http\Requests\Auth\Login;
use App\Http\Requests\Auth\ResetPassword;
use App\Http\Requests\Auth\ResetRequest;
use App\User;

class AuthController extends Controller
{
    public function login(Login $request, AuthHandler $handler)
    {
        $user = User::where('email', $request->input('email'))->first();
        $responseContent = $handler->authenticate($request->only([
            'email', 'password'
        ]));

        return response()->json([
            'token' => $responseContent['token'],
            'user' => $user->load('phones'),
        ]);
    }

    public function sendResetPasswordRequest(ResetRequest $request,
                                            ForgotPasswordHandler $handler)
    {
        User::where('email', $request->input('email'))->firstOrFail();
        $handler->sendResetRequest($request->input('email'));
        return response()->json([
            'message' => 'The reset token was sent to your email',
        ]);
    }

    public function resetPassword(ResetPassword $request,
                                 ForgotPasswordHandler $handler)
    {
        $handler->resetPassword($request->all());
        return response()->json(['message' => 'The password was reset sucessfully']);
    }
}
