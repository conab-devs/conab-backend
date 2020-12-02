<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use App\Cooperative;

class UpdateRequest extends FormRequest
{
    const CPF_REGEX = "/^[0-9]{3}\.[0-9]{3}\.[0-9]{3}\-[0-9]{2}/";
    const PHONE_NUMBER_REGEX = "/^\([0-9]{2}\) [0-9]{5}\-[0-9]{4}/";

    public function authorize()
    {
        $path = $this->path();
        $COOP_ADMIN_PATH_REGEX = "/^api\/cooperatives\/\d+\/admins\/\d+/";

        if (preg_match($COOP_ADMIN_PATH_REGEX, $path)) {
            $cooperative = Cooperative::findOrFail($this->route('cooperative'));
            $admin = $cooperative->admins()
                ->where('id', $this->route('id'))
                ->first();
            return Gate::allows('manage-cooperative-admin', $admin);
        }

        return true;
    }

    public function rules()
    {
        $user = $this->user();

        return [
            'name' => 'string',
            'email' => 'string|email|unique:users,email',
            'cpf' => [
                'regex:' . self::CPF_REGEX,
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'string',
            'new_password' => 'string|required_with:password',
            'phones' => 'array',
            'phones.*.number' => [
                'string',
                'distinct',
                'regex:' . self::PHONE_NUMBER_REGEX,
                Rule::unique('phones')->where(function ($query) use ($user) {
                    $phonesId = [];
                    foreach ($user->phones as $phone) {
                        array_push($phonesId, $phone->id);
                    }
                    return $query->whereNotIn('id', $phonesId)->get('number');
                })
            ],
        ];
    }
}
