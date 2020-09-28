<?php

namespace App\Http\Controllers;

use App\Cooperative;
use App\Http\Requests\AdminCooperativeStore;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CooperativeAdminController extends Controller
{
    public function index(Cooperative $cooperative)
    {
        if (Gate::denies('manage-cooperative-admin')) {
            return response()->json('Você não tem autorização a este recurso', 401);
        }
        return response()->json(
            $cooperative->admins()->with('phones')->paginate(5), 200
        );
    }

    public function show(Cooperative $cooperative, $id)
    {
        $admin = $cooperative->admins()
            ->with('phones')
            ->where('id', $id)
            ->first();

        if (Gate::denies('manage-cooperative-admin', $admin)) {
            return response()->json('Você não tem autorização a este recurso', 401);
        }

        if (!$admin) {
            return response()->json(
                'Administrador da cooperativa não encontrado', 404
            );
        }

        return response()->json($admin);
    }

    public function store(AdminCooperativeStore $request, Cooperative $cooperative)
    {
        if (Gate::denies('manage-cooperative-admin')) {
            return response()->json('Você não tem autorização a este recurso', 401);
        }

        $adminInformations = array_merge($request->validated(), [
            'password' => $request->cpf,
            'user_type' => 'ADMIN_COOP',
        ]);

        DB::transaction(function () use (&$adminInformations, $cooperative) {
            $user = User::create($adminInformations);
            $cooperative->admins()->save($user);
            $user->phones()->createMany($adminInformations['phones']);
            $adminInformations = $user->loadMissing('phones');
        });

        return response()->json($adminInformations, 201);
    }

    public function update(Request $request, Cooperative $cooperative, $id)
    {
        $admin = $cooperative->admins()
            ->with('phones')
            ->where('id', $id)
            ->first();

        if (Gate::denies('manage-cooperative-admin', $admin)) {
            return response()->json('Você não tem autorização a este recurso', 401);
        }

        $data = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'string|email',
            'cpf' => [
                'regex:/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/',
                Rule::unique('users')->ignore($admin->id),
            ],
            'password' => 'string',
            'new_password' => 'string|required_with:password',
            'phones.*.number' => [
                'string',
                'distinct',
                'regex:/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/',
                Rule::unique('phones')->where(function ($query) use ($admin) {
                    $phonesId = [];
                    foreach ($admin->phones as $phone) {
                        array_push($phonesId, $phone->id);
                    }
                    return $query->whereNotIn('id', $phonesId)->get('number');
                })
            ],
        ])->validate();

        DB::transaction(function () use (&$admin, $data, $request) {
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
        });

        return response($admin->refresh(), 200);
    }
}
