<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStore;
use App\Http\Requests\UserUpdate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function show()
    {
        return response(auth()->user());
    }

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

    public function update(UserUpdate $request)
    {
        $validated = $request->validated();
        $user = auth()->user();

        DB::beginTransaction();

        try {
            if (!empty($validated['password'])) {
                if (!Hash::check($validated['password'], $user->password)) {
                    return response('', 400);
                }
                $user->password = $validated['new_password'];
                $user->save();
            }

            $user->update($request->except('password', 'new_password'));

            $relationships_keys = collect(['addresses', 'phones']);
            $relationships_keys->each(function ($relationship) use ($user, $validated) {
                if (isset($validated[$relationship])) {
                    $user->{$relationship}()->delete();
                    $user->{$relationship}()->createMany($validated[$relationship]);
                }
            });

            DB::commit();

            $user->load('addresses', 'phones');

            return response()->json($user);
        } catch (\Exception $err) {
            DB::rollback();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes',
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

        return response('', 204);
    }
}
