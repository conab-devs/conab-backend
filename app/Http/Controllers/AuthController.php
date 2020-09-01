<?php

namespace App\Http\Controllers;

use App\Components\Errors\UnauthorizedException;
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

        $user = User::where('email', $validated['email'])->first();

        try {
            if (!$user) {
                throw new UnauthorizedException();
            }

            $token = $user->login($validated['password'], $validated['device_name']);

            return response()->json(['token' => $token, 'user' => $user]);
        } catch (\Exception $error) {
            return response()->json([
                'message' => $error->getMessage(),
            ], $error->status);
        }
    }
}
