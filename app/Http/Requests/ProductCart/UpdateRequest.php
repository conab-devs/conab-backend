<?php

namespace App\Http\Requests\ProductCart;

use App\Http\Requests\FormRequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateRequest extends FormRequest
{
    use FormRequestTrait;

    public function authorize()
    {
        return Gate::allows('manage-product-cart', $this->route('productCart'));
    }

    public function rules()
    {
        return [
            'amount' => 'required|numeric|between:0.001,99999999.99',
        ];
    }
}
