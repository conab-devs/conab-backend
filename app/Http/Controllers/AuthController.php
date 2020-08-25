<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'device_name' => 'required',
        ]);

        $user = User::where('email',$request->email)->first();

        if (! $user) {
            return response()->json(404, [
                'message' => 'Theres no user with that email'
            ]);
        }
        try {   
        $token = $user->login($request->password, $request->device_name)->plainTextToken;

        return response()->json(['token' => $token]);
        } catch (\Exception $error) {
            return response()->json([
                'message' => $error->__toString()
            ], $error->status);
        }
    }
}
