<?php

namespace App\Http\Requests\Cooperative;

use App\Cooperative;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    const PHONE_NUMBER_REGEX = "/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/";

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $cooperative = Cooperative::find($this->route('cooperative'));

        return [
            'name' => [
                'bail',
                Rule::unique('cooperatives', 'name')->ignore($cooperative->id),
                'max:100'
            ],
            'phones' => 'array',
            'phones.*.number' => [
                'distinct',
                Rule::unique('phones')->whereNotIn('id', $cooperative->phones->modelKeys()),
                'regex:' . self::PHONE_NUMBER_REGEX,
                'max:15'
            ],
            'city' => 'max:100',
            'street' => 'max:100',
            'neighborhood' => 'max:100',
            'number' => 'max:10',
        ];
    }
}
