<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Http\Requests\User\StoreRequest;
use App\Components\Validators\UpdateUser;

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

    public function update(Request $request)
    {
        $admin = User::with('phones')->findOrFail(Auth::id());

        $validator = new UpdateUser();
        $data = $validator->execute($request, $admin);

        $admin->name = $data['name'] ?? $admin->name;
        $admin->email = $data['email'] ?? $admin->email;
        $admin->cpf = $data['cpf'] ?? $admin->cpf;

        if (!empty($data['password'])) {
            if (!Hash::check($data['password'], $admin->password)) {
                return response()->json('', 400);
            }
            $admin->password = $data['new_password'];
        }

        $admin->save();

        if (!empty($data['phones'])) {
            $admin->phones()->delete();
            $admin->phones()->createMany($data['phones']);
        }

        return response()->json($admin->refresh(), 200);
    }
}
