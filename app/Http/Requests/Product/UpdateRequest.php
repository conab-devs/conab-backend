<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\FormRequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateRequest extends FormRequest
{
    use FormRequestTrait;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('manage-product', $this->route('product'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|max:255',
            'price' => 'numeric|between:0,99999999.99',
            'photo_path' => 'image',
            'estimated_delivery_time' => 'integer',
            'category_id' => 'exists:App\Category,id',
        ];
    }
}
