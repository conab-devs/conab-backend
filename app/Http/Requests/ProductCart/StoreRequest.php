<?php

namespace App\Http\Requests\ProductCart;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    use FormRequestTrait;

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'product_id' => 'required|integer|exists:product,id',
            'price' => 'required|numeric|between:0,99999999.99',
            'amount' => 'required|numeric|between:0,99999999.99',
            'unit_of_measure' => [
                'required',
                'string',
                Rule::in('kg', 'unit'),
            ],
        ];
    }
}
