<?php

namespace App\Http\Requests\ProductCart;

use App\Http\Requests\FormRequestTrait;
use Illuminate\Foundation\Http\FormRequest;

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
            'product_id' => 'required|integer|exists:products,id',
            'amount' => 'required|numeric|between:0,99999999.99',
        ];
    }
}
