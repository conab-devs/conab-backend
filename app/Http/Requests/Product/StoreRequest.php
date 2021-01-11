<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreRequest extends FormRequest
{
    use FormRequestTrait;

    public function authorize()
    {
        return Gate::allows('create-product');
    }

    public function rules()
    {
        return [
            'name' => 'bail|required|max:255',
            'price' => 'required|numeric|between:0,99999999.99',
            'photo_path' => 'required|image',
            'estimated_delivery_time' => 'required|integer',
            'category_id' => 'required|exists:App\Category,id',
        ];
    }
}
