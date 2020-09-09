<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Components\ForgotPasswordHandler;
use App\Components\AuthHandler;
use App\Components\Traits\HttpResponse;
use App\User;

class AuthController extends Controller
{
    use HttpResponse;

    private $status = 500;

    public function login(Request $request, AuthHandler $handler)
    {
        $requestContent = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'device_name' => 'required',
        ])->validate();

        try {    
            $responseContent = $handler->authenticate($requestContent);

            return response()->json($responseContent);
        } catch (\Exception $error) {
            return $this->respondWithError($error);
        }
    }

    public function sendResetPasswordRequest(Request $request, ForgotPasswordHandler $handler)
    {
        $requestContent = Validator::make($request->all(), [
            'email' => 'required|email'
        ])->validate();

        try {
            User::where('email', $requestContent['email'])->firstOrFail();
    
            $handler->sendResetRequest($requestContent['email']);

            return response()->json([
                'message' => 'The reset token was sent to your email'
            ]);
        } catch (\Exception $error) {            
            return $this->respondWithError($error);
        }
    }

    public function resetPassword(Request $request, ForgotPasswordHandler $handler)
    {
        $requestContent = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'token' => 'required'
        ])->validate();

        try {

            $handler->resetPassword($requestContent);

            return response()->json(['message' => 'The password was reset sucessfully']);
        } catch (\Exception $error) {
            return $this->respondWithError($error);
        }
    }
}
