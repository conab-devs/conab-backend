<?php

namespace App\Http\Requests\Address;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'addresses' => 'required|array',
            'addresses.*.street' => 'required|string',
            'addresses.*.neighborhood' => 'required|string',
            'addresses.*.city' => 'required|string',
            'addresses.*.number' => 'required|string'
        ];
    }
}
