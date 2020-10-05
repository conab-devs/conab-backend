<?php

namespace App\Components\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateUser
{
    public function execute($request, $admin)
    {
        return Validator::make($request->all(), [
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
    }
}
