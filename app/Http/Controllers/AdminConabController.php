<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminConabController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $users = User::where('user_type', 'ADMIN_CONAB')->where('id', '<>', $user->id)->get();
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $data = $request->except('phones');
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'cpf' => 'required|regex:/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/',
            'phones' => 'required|array'
        ]);

        $formattedPhones = array_map(function ($value) {
            return [ 'number' => $value ];
        }, $data['phones']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['cpf'],
            'cpf' => $data['cpf'],
            'user_type' => 'ADMIN_CONAB'
        ]);
        $user->save();

        $phones = $user->phones()->createMany($formattedPhones);
        $onlyNumbers = array_map(function ($phone) {
            return $phone->number;
        }, $phones);

        return response([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'cpf' => $user->cpf,
            'phones' => $onlyNumbers
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
