<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Cooperative;
use App\Http\Requests\User\StoreRequest;
use App\Components\Validators\UpdateUser;

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

        DB::beginTransaction();

        try {
            $user = User::create($coopAdminInformation);
            $cooperative->admins()->save($user);
            $user->phones()->createMany($coopAdminInformation['phones']);
            $coopAdminInformation = $user->loadMissing('phones');

            DB::commit();
        } catch (\Exception $err) {
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes',
            ], 500);
        }

        return response()->json($coopAdminInformation, 201);
    }

    public function update(Request $request, Cooperative $cooperative, int $id)
    {
        $admin = $cooperative->admins()->with('phones')->where('id', $id)->first();

        if (Gate::denies('manage-cooperative-admin', $admin)) {
            return response()->json([
                'message' => 'Você não tem autorização a este recurso',
            ], 401);
        }

        $validator = new UpdateUser();
        $data = $validator->execute($request, $admin);

        try {
            DB::beginTransaction();

            $admin->update($request->except('password', 'new_password', 'phones'));

            if (!empty($data['password'])) {
                if (!Hash::check($data['password'], $admin->password)) {
                    return response('', 400);
                }
                $admin->password = $data['new_password'];
            }

            $admin->save();

            if (!empty($data['phones'])) {
                $admin->phones()->delete();
                $admin->phones()->createMany($data['phones']);
            }

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
