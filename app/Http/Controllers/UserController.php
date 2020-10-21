<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStore;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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

    public function destroy(\App\User $user)
    {
        if (Gate::denies('destroy-user', $user)) {
            return response()->json('Você não tem autorização a este recurso', 401);
        }

        $user->phones()->delete();
        $user->delete();
    }
}
