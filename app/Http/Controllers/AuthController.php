<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Components\ForgotPasswordHandler;
use App\Components\Services\PasswordResetService;
use App\Components\Errors\UnauthorizedException;
use App\Components\AuthHandler;
use App\Components\Services\UserService;
use App\Components\TokenGenerator;
use App\PasswordReset;
use App\User;

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
            $status = 500;
            
            if ($error instanceof \App\Components\Errors\CustomException) {
                $status = $error->status;
            }
            return response()->json([
                'message' => $error->getMessage()
            ], $status);
        }
    }

    public function sendResetPasswordRequest(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email'
        ])->validate();

        $user = User::where('email', $validated['email'])->first();
        
        try {
            if (! $user) {
                throw new UnauthorizedException();
            }
    
            $handler = $this->makeForgotPasswordHandler();
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

    public function resetPassword(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'token' => 'required'
        ])->validate();

        try {
            $handler = $this->makeForgotPasswordHandler();
            $handler->resetPassword($validated);

            return response()->json('The password was reset sucessfully');
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

    private function makeForgotPasswordHandler()
    {
        $passwordService = new PasswordResetService(new PasswordReset());
        $userService = new UserService(new User());
        
        return new ForgotPasswordHandler(
            $passwordService, new TokenGenerator(), $userService
        );
    }
}
