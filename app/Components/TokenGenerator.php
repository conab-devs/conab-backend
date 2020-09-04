<?php

namespace App\Components;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TokenGenerator
{
    public function generate()
    {
        return Str::random(80);
    }

    public function generateJwt($credentials)
    {
        return Auth::attempt($credentials);
    }
}