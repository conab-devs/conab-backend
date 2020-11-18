<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\Store;
use App\Http\Requests\User\Update;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return response($user->load('phones'));
    }

    public function store(Store $request)
    {
        $user = \App\User::create($request->validated());

        \App\Phone::create(['number' => $request->input('phones')]);

        $user->load('phones');

        return response()->json($user, 201);
    }

    public function update(Update $request)
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

            $user->phones()->delete();

            $user->phones()->create(['number' => $request->input('phones')]);

            $user->load('phones');

            DB::commit();

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
        $user->addresses()->delete();

        $user->delete();

        return response('', 204);
    }
}
