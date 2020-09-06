<?php

namespace App\Http\Controllers;

use App\Components\AuthHandler;
use App\Components\Services\UserService;
use App\Components\TokenGenerator;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Components\ForgotPasswordHandler;
use App\Components\Services\PasswordResetService;
use App\PasswordReset;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'device_name' => 'required',
        ])->validate();

        try {
            $service = new UserService(new User());
            $authHandler = new AuthHandler($service, new TokenGenerator());
            
            $responseContent = $authHandler->authenticate($validated);

            return response()->json($responseContent);
        } catch (\Exception $error) {
            return response()->json([
                'message' => $error->getMessage(),
            ], $error->status);
        }
    }

    public function sendResetPasswordRequest(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email'
        ])->validate();

        $service = new PasswordResetService(new PasswordReset());
        $handler = new ForgotPasswordHandler($service, new TokenGenerator());
        
        try {
            $handler->sendResetRequest($validated['email']);
            return response()->json('The reset token was sent to your email');
        } catch (\Exception $error) {
            $status = 500;
            
            if ($error instanceof \App\Components\Errors\CustomException) {
                $status = $error->status;
            }
            return response()->json([
                'message' => $error->getMessage()
            ], $status);
        }
    }
}
