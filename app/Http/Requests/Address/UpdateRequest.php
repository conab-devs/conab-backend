<?php

namespace App\Http\Requests\Address;

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
            'addresses' => 'array',
            'addresses.*.street' => 'string',
            'addresses.*.neighborhood' => 'string',
            'addresses.*.city' => 'string',
            'addresses.*.number' => 'string',
        ];
    }
}
