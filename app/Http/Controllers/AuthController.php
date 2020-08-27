<?php

namespace App\Http\Controllers;

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

        if (!$user) {
            return response()->json([
                'message' => 'Theres no user with that email',
            ], 404);
        }

        try {
            $token = $user->login($request->password, $request->device_name);

            return response()->json(['token' => $token]);
        } catch (\Exception $error) {
            return response()->json([
                'message' => $error->__toString(),
            ], $error->status);
        }
    }

    public function logout()
    {
        auth()->logout();
    }
}
