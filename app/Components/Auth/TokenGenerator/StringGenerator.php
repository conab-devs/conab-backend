<?php

namespace App\Components\Auth\TokenGenerator;

use Illuminate\Support\Str;
use App\Components\Auth\TokenGenerator\TokenGenerator;

class StringGenerator implements TokenGenerator
{
    public function generate(array $credentials = null)
    {
        return Str::random(80);
    }
}