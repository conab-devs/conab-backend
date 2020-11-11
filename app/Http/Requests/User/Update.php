<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string',
            'email' => 'email|unique:users',
            'password' => 'string|min:6',
            'new_password' => 'string|min:6|required_with:password',
            'cpf' => 'regex:/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/|unique:users,cpf',
            'phones' => 'array',
            'phones.*.number' => 'string|regex:/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/|distinct|unique:phones,number',
            'addresses' => 'array',
            'addresses.*.street' => 'string',
            'addresses.*.neighborhood' => 'string',
            'addresses.*.city' => 'string',
            'addresses.*.number' => 'string',
        ];
    }
}
