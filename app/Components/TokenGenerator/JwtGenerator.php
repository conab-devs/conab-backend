<?php

namespace App\Components\TokenGenerator;

use Illuminate\Support\Facades\Auth;
use App\Components\TokenGenerator\TokenGenerator;

class JwtGenerator implements TokenGenerator
{
    public function generate(array $credentials = null)
    {
        return Auth::attempt($credentials);
    }
}