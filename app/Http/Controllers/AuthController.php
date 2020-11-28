<?php

namespace App\Http\Controllers;

use App\Components\Auth\AuthHandler;
use App\Components\Auth\ForgotPasswordHandler;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendResetPasswordRequest;
use App\User;

class AuthController extends Controller
{
    /**
     * @param LoginRequest $request
     * @param AuthHandler $handler
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ServerError
     * @throws \App\Exceptions\UnauthorizedException
     */
    public function login(LoginRequest $request, AuthHandler $handler)
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

    /**
     * @param SendResetPasswordRequest $request
     * @param ForgotPasswordHandler $handler
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetPasswordRequest(SendResetPasswordRequest $request, ForgotPasswordHandler $handler)
    {
        User::where('email', $request->input('email'))->firstOrFail();
        $handler->sendResetRequest($request->input('email'));
        return response()->json([
            'message' => 'The reset token was sent to your email',
        ]);
    }

    /**
     * @param ResetPasswordRequest $request
     * @param ForgotPasswordHandler $handler
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ServerError
     * @throws \App\Exceptions\UnauthorizedException
     */
    public function resetPassword(ResetPasswordRequest $request, ForgotPasswordHandler $handler)
    {
        $handler->resetPassword($request->all());
        return response()->json(['message' => 'The password was reset sucessfully']);
    }
}
