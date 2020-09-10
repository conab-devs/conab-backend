<?php

namespace App\Components\TokenGenerator;

use Illuminate\Support\Str;
use App\Components\TokenGenerator\TokenGenerator;

class StringGenerator implements TokenGenerator
{
    public function generate(array $credentials = null)
    {
        return Str::random(80);
    }
}