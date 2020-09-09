<?php

namespace App\Http\Controllers;

use App\Http\Requests\Login;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\ResetPassword;
use App\Components\ForgotPasswordHandler;
use App\Components\AuthHandler;
use App\Components\Traits\HttpResponse;
use App\User;

class AuthController extends Controller
{
    use HttpResponse;

    private $status = 500;

    public function login(Login $request, AuthHandler $handler)
    {
        try {    
            $responseContent = $handler->authenticate($request->all());
            return response()->json($responseContent);
        } catch (\Exception $error) {
            return $this->respondWithError($error);
        }
    }

    public function sendResetPasswordRequest(ResetRequest $request, ForgotPasswordHandler $handler)
    {
        try {
            User::where('email', $request->input('email'))->firstOrFail();
            $handler->sendResetRequest($request->input('email'));
            return response()->json([
                'message' => 'The reset token was sent to your email'
            ]);
        } catch (\Exception $error) {            
            return $this->respondWithError($error);
        }
    }

    public function resetPassword(ResetPassword $request, ForgotPasswordHandler $handler)
    {
        try {
            $handler->resetPassword($request->all());
            return response()->json(['message' => 'The password was reset sucessfully']);
        } catch (\Exception $error) {
            return $this->respondWithError($error);
        }
    }
}
