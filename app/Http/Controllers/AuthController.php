<?php

namespace App\Http\Controllers;

use App\Components\AuthHandler;
use App\Components\Services\UserService;
use App\Components\TokenGenerator;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

            return response()->json([
                'token' => $responseContent['token'],
                'user' => $responseContent['user']->load('phones')
            ]);
        } catch (\Exception $error) {
            return response()->json([
                'message' => $error->getMessage(),
            ], $error->status);
        }
    }
}
