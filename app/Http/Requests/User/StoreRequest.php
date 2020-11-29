<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    const CPF_REGEX = "/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/";
    const PHONE_NUMBER_REGEX = "/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/";

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|regex:' . self::CPF_REGEX . '|unique:users,cpf',
            'phones' => 'required|array',
            'phones.*.number' => 'required|string|regex:'
                . self::PHONE_NUMBER_REGEX . '|distinct|unique:phones,number',
        ];
    }
}
