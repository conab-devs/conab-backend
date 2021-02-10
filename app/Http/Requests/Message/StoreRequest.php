<?php

namespace App\Http\Requests\Message;

use App\Http\Requests\FormRequestTrait;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    use FormRequestTrait;

    public function authorize()
    {
        return false;
    }

    public function rules()
    {
        return [
            'content' => 'required|string',
            'source_id' => 'required|integer|exists:users,id',
            'destination_id' => 'nullable|integer|exists:users,id',
            'cooperative_id' => 'nullable|integer|exists:cooperatives,id'
        ];
    }
}
