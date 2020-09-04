<?php

namespace App\Components;

use Illuminate\Support\Str;
use Illuminate\Support\Facade\Auth;

class TokenGenerator
{
    public function generate()
    {
        return Str::random(80);
    }

    public function generateJwt($credentials)
    {
        Auth::attempt($credentials);
    }
}