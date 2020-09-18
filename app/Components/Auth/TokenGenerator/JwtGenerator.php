<?php

namespace App\Components\Auth\TokenGenerator;

use Illuminate\Support\Facades\Auth;
use App\Components\Auth\TokenGenerator\TokenGenerator;

class JwtGenerator implements TokenGenerator
{
    public function generate(array $credentials = null)
    {
        return Auth::attempt($credentials);
    }
}