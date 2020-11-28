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
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $user = auth()->user();
        return response()->json($user->load('phones'));
    }

    /**
     * @param StoreRequest $request
     * @param UploadHandler $uploader
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, UploadHandler $uploader)
    {
        $user = User::create($request->validated());

        $user->phones()->create(['number' => $request->input('phones')]);

        if ($request->hasFile('avatar') && ($avatar = $request->file('avatar'))->isValid()) {
            $user->profile_picture = $uploader->upload($avatar);
            $user->save();
        }

        $user->load('phones');

        return response()->json($user, 201);
    }


    /**
     * @param UpdateRequest $request
     * @param UploadHandler $uploader
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, UploadHandler $uploader)
    {
        $validated = $request->validated();
        $user = auth()->user();

        DB::beginTransaction();

        try {
            if (!empty($validated['password'])) {
                if (!Hash::check($validated['password'], $user->password)) {
                    return response()->json('Senha Inválida', 400);
                }
                $user->password = $validated['new_password'];
                $user->save();
            }

            if ($request->hasFile('avatar') && ($avatar = $request->file('avatar'))->isValid()) {
                $user->profile_picture = $uploader->upload($avatar);
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


    /**
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(\App\User $user)
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
