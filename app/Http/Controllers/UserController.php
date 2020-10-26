<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(UserStore $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validated();
            $user = \App\User::create($validated);
            $user->phones()->createMany($validated['phones']);
            $user->addresses()->createMany($validated['addresses']);
            $user->load('phones', 'addresses');

            DB::commit();

            return response()->json($user, 201);
        } catch (\Exception $error) {
            DB::rollBack();

            return response()->json([
                "message" => "Algo deu errado, tente novamente em alguns instantes",
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'string',
            'email' => 'email|unique:users',
            'password' => 'string|min:6',
            'cpf' => 'regex:/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/|unique:users,cpf',
            'phones' => 'array',
            'phones.*.number' => 'string|regex:/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/|distinct|unique:phones,number',
            'addresses' => 'array',
            'addresses.*.street' => 'string',
            'addresses.*.neighborhood' => 'string',
            'addresses.*.city' => 'string',
            'addresses.*.number' => 'string'
        ]);

        $user = auth()->user();

        if (! empty($validated['password'])) {
            if (Hash::check($validated['password'], $user->password)) {
                return response()->json('Informe um novo password, não o antigo.', 422);
            }
        }

        $user->update($validated);

        if (! empty($validated['addresses'])) {
            $user->addresses()->delete();
            $user->addresses()->createMany($validated['addresses']);
        }

        if (! empty($validated['phones'])) {
            $user->phones()->delete();
            $user->phones()->createMany($validated['phones']);
        }

        $user->load('addresses', 'phones');

        return response()->json($user);
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
