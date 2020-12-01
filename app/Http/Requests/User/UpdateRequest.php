<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = $this->user();

        return [
            'name' => 'string',
            'email' => 'string|email|unique:users,email',
            'cpf' => [
                'regex:/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'string',
            'new_password' => 'string|required_with:password',
            'phones' => 'array',
            'phones.*.number' => [
                'string',
                'distinct',
                'regex:/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/',
                Rule::unique('phones')->where(function ($query) use ($user) {
                    $phonesId = [];
                    foreach ($user->phones as $phone) {
                        array_push($phonesId, $phone->id);
                    }
                    return $query->whereNotIn('id', $phonesId)->get('number');
                })
            ],
        ];
    }
}
