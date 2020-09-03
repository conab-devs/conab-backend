<?php

namespace App\Components;

use Illuminate\Support\Str;

class TokenGenerator
{
    public function generate()
    {
        return Str::random(80);
    }
}