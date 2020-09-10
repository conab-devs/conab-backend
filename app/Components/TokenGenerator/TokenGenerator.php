<?php

namespace App\Components\TokenGenerator;

interface TokenGenerator
{
    public function generate(array $credentials = null);
}