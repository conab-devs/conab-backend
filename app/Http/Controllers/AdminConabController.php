<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Components\Validators\UpdateUser;

class AdminConabController extends Controller
{
    public function __construct()
    {
        $this->middleware('only-admin-conab');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $admins = User::with('phones')->where([
            ['user_type', '=', 'ADMIN_CONAB'],
            ['id', '<>', $user->id]
        ])->paginate(5);
        return response($admins, 200);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\Response
     */
     public function show($id)
     {
         $admin = User::with('phones')->findOrFail($id);
        return response($admin, 200);
     }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'cpf' => 'required|regex:/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/|unique:users,cpf',
            'phones' => 'required|array',
            'phones.*.number' => 'required|string|regex:/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/|distinct|unique:phones,number'
        ])->validate();

        $user = new User();
        $user->fill($data);
        $user->password = $data['cpf'];
        $user->user_type = 'ADMIN_CONAB';
        $user->save();
        $phones = $user->phones()->createMany($data['phones']);
        $userAndPhones = array_merge($user->toArray(), [ 'phones' => $phones ]);

        return response($userAndPhones, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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
                return response('', 400);
            }
            $admin->password = $data['new_password'];
        }

        $admin->save();

        if (!empty($data['phones'])) {
            $admin->phones()->delete();
            $admin->phones()->createMany($data['phones']);
        }

        return response($admin->refresh(), 200);
    }
}
