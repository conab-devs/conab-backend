<?php

namespace App\Http\Requests\Cooperative;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    const PHONE_NUMBER_REGEX = "/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/";

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'bail|required|unique:cooperatives|max:100',
            'dap_path' => 'required|mimetypes:application/pdf',
            'phones' => 'required|array',
            'phones.*.number' => 'required|distinct|regex:'
                . self::PHONE_NUMBER_REGEX . '|unique:phones,number|max:15',
            'city' => 'required|max:100',
            'street' => 'required|max:100',
            'neighborhood' => 'required|max:100',
            'number' => 'required|max:10',
        ];
    }
}
