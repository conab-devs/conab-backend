<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Cooperative;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;

class CooperativeAdminController extends Controller
{
    public function index(Cooperative $cooperative)
    {
        if (Gate::denies('manage-cooperative-admin')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }
        return response()->json(
            $cooperative->admins()->with('phones')->paginate(5),
            200
        );
    }

    public function show(Cooperative $cooperative, $id)
    {
        $admin = $cooperative->admins()
            ->with('phones')
            ->where('id', $id)
            ->firstOrFail();

        if (Gate::denies('manage-cooperative-admin', $admin)) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        return response()->json($admin);
    }

    public function store(StoreRequest $request, Cooperative $cooperative)
    {
        if (Gate::denies('manage-cooperative-admin')) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $coopAdminInformation = array_merge($request->validated(), [
            'password' => $request->cpf,
            'user_type' => 'ADMIN_COOP',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create($coopAdminInformation);
            $cooperative->admins()->save($user);
            $user->phones()->createMany($coopAdminInformation['phones']);
            $user->load(['phones']);

            DB::commit();

            return response()->json($user, 201);
        } catch (\Exception $err) {
            DB::rollback();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes',
            ], 500);
        }
    }

    public function update(UpdateRequest $request)
    {
        $admin = $request->user();
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            $admin->fill($validatedData);

            if (!empty($validatedData['password'])) {
                if (!Hash::check($validatedData['password'], $admin->password)) {
                    return response()->json('Senha inválida', 400);
                }
                $admin->password = $validatedData['new_password'];
            }

            if (!empty($validatedData['phones'])) {
                $admin->phones()->delete();
                $admin->phones()->createMany($validatedData['phones']);
            }

            $admin->save();

            DB::commit();

            return response()->json($admin);
        } catch (\Exception $error) {
            DB::rollback();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes'
            ], 500);
        }
    }
}
