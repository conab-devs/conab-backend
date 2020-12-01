<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Components\Upload\UploadHandler;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;

class UserController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return response()->json($user->load('phones'));
    }

    public function store(StoreRequest $request, UploadHandler $uploader)
    {
        $userData = $request->except('phones');

        try {
            DB::beginTransaction();

            $user = User::create($userData);
            $phoneNumber = $request->input('phones')[0];
            $user->phones()->create($phoneNumber);

            if ($request->hasFile('avatar') && ($avatar = $request->file('avatar'))->isValid()) {
                $user->profile_picture = $uploader->upload($avatar);
                $user->save();
            }

            $user->load('phones');

            DB::commit();

            return response()->json($user, 201);
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes'
            ], 500);
        }
    }

    public function update(UpdateRequest $request, UploadHandler $uploader)
    {
        $validated = $request->validated();
        $user = auth()->user();

        try {
            DB::beginTransaction();

            if (!empty($validated['password'])) {
                if (!Hash::check($validated['password'], $user->password)) {
                    return response()->json('Senha Inválida', 400);
                }
                $user->password = $validated['new_password'];
            }

            if ($request->hasFile('avatar') && ($avatar = $request->file('avatar'))->isValid()) {
                $user->profile_picture = $uploader->upload($avatar);
            }

            $user->fill($request->except('password', 'new_password'));
            $user->save();

            $user->phones()->delete();
            $user->phones()->create(['number' => $request->input('phones')]);

            $user->load('phones');

            DB::commit();

            return response()->json($user, 200);
        } catch (\Exception $err) {
            DB::rollback();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes',
            ], 500);
        }
    }

    public function destroy(User $user)
    {
        if (Gate::denies('destroy-user', $user)) {
            return response()->json('Você não tem autorização a este recurso', 401);
        }

        $user->phones()->delete();
        $user->addresses()->delete();

        $user->delete();

        return response()->json(null, 204);
    }
}
