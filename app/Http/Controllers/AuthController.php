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
use App\Components\Traits\HttpResponse;
use App\PasswordReset;
use App\User;

class AuthController extends Controller
{
    use HttpResponse;

    private $status = 500;

    public function login(Request $request)
    {
        $requestContent = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'device_name' => 'required',
        ])->validate();

        try {
            $service = new UserService(new User());
            $handler = new AuthHandler($service, new TokenGenerator());
            
            $responseContent = $handler->authenticate($requestContent);

            return response()->json($responseContent);
        } catch (\Exception $error) {
            return $this->respondWithError($error);
        }
    }

    public function sendResetPasswordRequest(Request $request)
    {
        $requestContent = Validator::make($request->all(), [
            'email' => 'required|email'
        ])->validate();

        $user = User::where('email', $requestContent['email'])->first();
        
        try {
            if (! $user) {
                throw new UnauthorizedException();
            }
    
            $handler = $this->makeForgotPasswordHandler();
            $handler->sendResetRequest($requestContent['email']);

            return response()->json([
                'message' => 'The reset token was sent to your email'
            ]);
        } catch (\Exception $error) {            
            return $this->respondWithError($error);
        }
    }

    public function resetPassword(Request $request)
    {
        $requestContent = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'token' => 'required'
        ])->validate();

        try {
            $handler = $this->makeForgotPasswordHandler();
            $handler->resetPassword($requestContent);

            return response()->json(['message' => 'The password was reset sucessfully']);
        } catch (\Exception $error) {
            return $this->respondWithError($error);
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
