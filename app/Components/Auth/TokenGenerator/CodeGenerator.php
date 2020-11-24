<?php

namespace App\Components\Auth\TokenGenerator;

use App\Components\Auth\TokenGenerator\TokenGenerator;

class CodeGenerator implements TokenGenerator
{
    public function generate(array $credentials = null)
    {
        return mt_rand(100000, 999999);
    }
}
