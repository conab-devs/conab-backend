<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'cpf' => 'required|regex:/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/|unique:users,cpf',
            'phones' => 'required|array',
            'phones.*.number' => 'required|string|regex:/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/|distinct|unique:phones,number',
            'addresses' => 'required|array',
            'addresses.*.street' => 'required|string',
            'addresses.*.neighborhood' => 'required|string',
            'addresses.*.city' => 'required|string',
            'addresses.*.number' => 'required|string'
        ]);

        $user = \App\User::create($validated);
        $user->phones()->createMany($validated['phones']);
        $user->addresses()->createMany($validated['addresses']);
        $user->load('phones', 'addresses');

        return response()->json($user, 201);
    }

    public function destroy(\App\User $user)
    {
        if (Gate::denies('destroy-user', $user)) {
            return response()->json('Você não tem autorização a este recurso', 401);
        }

        $user->phones()->delete();
        $user->delete();
    }
}
