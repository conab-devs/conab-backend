<?php

namespace App\Http\Controllers;

use App\Components\AuthHandler;
use App\Components\ForgotPasswordHandler;
use App\Components\Traits\HttpResponse;
use App\Http\Requests\Login;
use App\Http\Requests\ResetPassword;
use App\Http\Requests\ResetRequest;
use App\User;

class AuthController extends Controller
{
    use HttpResponse;

    public function login(Login $request, AuthHandler $handler)
    {
        $user = User::where('email', $request->input('email'))->first();
        $responseContent = $handler->authenticate($request->only([
            'email', 'password'
        ]));

        return response()->json([
            'token' => $responseContent['token'],
            'user' => $user,
        ]);
    }

    public function sendResetPasswordRequest(ResetRequest $request,
                                            ForgotPasswordHandler $handler) {
        User::where('email', $request->input('email'))->firstOrFail();
        $handler->sendResetRequest($request->input('email'));
        return response()->json([
            'message' => 'The reset token was sent to your email',
        ]);
    }

    public function resetPassword(ResetPassword $request,
                                 ForgotPasswordHandler $handler) {
        $handler->resetPassword($request->all());
        return response()->json(['message' => 'The password was reset sucessfully']);
    }
}
