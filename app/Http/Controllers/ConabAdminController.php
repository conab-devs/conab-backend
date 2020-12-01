<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;

class ConabAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('only-conab-admins');
    }

    public function index()
    {
        $user = Auth::user();
        $admins = User::with('phones')->where([
            ['user_type', '=', 'ADMIN_CONAB'],
            ['id', '<>', $user->id]
        ])->paginate(5);
        return response()->json($admins, 200);
    }

    public function show(int $id)
    {
        $admin = User::with('phones')->findOrFail($id);
        return response()->json($admin, 200);
    }

    public function store(StoreRequest $request)
    {
        $validatedData = $request->validated();
        $userData = array_merge($validatedData, [
            'password' => $validatedData['cpf'],
            'user_type' => 'CONAB_ADMIN'
        ]);

        try {
            DB::beginTransaction();

            $user = new User();
            $user->fill($userData);
            $user->save();
            $user->phones()->createMany($validatedData['phones']);
            $user->load(['phones']);

            DB::commit();

            return response()->json($user, 201);
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes'
            ], 500);
        }
    }

    public function update(UpdateRequest $request)
    {
        $userData = $request->validated();
        $user = $request->user();

        try {
            DB::beginTransaction();

            $user->fill($userData);

            if (!empty($userData['password'])) {
                if (!Hash::check($userData['password'], $user->password)) {
                    return response()->json('Senha inválida', 400);
                }
                $user->password = $userData['new_password'];
            }

            if (!empty($userData['phones'])) {
                $user->phones()->delete();
                $user->phones()->createMany($userData['phones']);
            }

            $user->save();
            $user->load(['phones']);

            DB::commit();

            return response()->json($user, 200);
        } catch (\Exception $err) {
            DB::rollBack();
            return response()->json([
                'message' => 'Algo deu errado, tente novamente em alguns instantes'
            ], 500);
        }
    }
}
