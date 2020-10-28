<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\UserUpdate;
use App\Components\Validators\PasswordValidator;

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

        $password = $validated['password'] ?? null;

        if (PasswordValidator::validate($password, $user->password)) {
            return response()->json('Informe um novo password, não o antigo.', 422);
        }

        DB::beginTransaction();

        try {
            $user->update($validated);

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
                'message' => 'Algo deu errado, tente novamente em alguns instantes'
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
