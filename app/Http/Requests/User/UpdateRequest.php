<?php

namespace App\Http\Requests\User;

use App\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'string',
            'email' => 'email|unique:users',
            'password' => 'string|min:6',
            'new_password' => 'string|min:6|required_with:password',
            'cpf' => 'regex:/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/|unique:users,cpf',
            'phones' => 'string|regex:/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/|distinct|unique:phones,number'
        ];
    }
}
