<?php

namespace App\Http\Controllers;

use App\Components\Errors\UnauthorizedException;
use App\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        try {
            if (!$user) {
                throw new UnauthorizedException();
            }

            $token = $user->login($request->password, $request->device_name);

            return response()->json(['token' => $token, 'user' => $user]);
        } catch (\Exception $error) {
            return response()->json([
                'message' => $error->getMessage(),
            ], $error->status);
        }
    }

    public function logout()
    {
        auth()->logout();
    }
}