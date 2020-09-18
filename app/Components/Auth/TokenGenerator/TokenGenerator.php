<?php

namespace App\Components\Auth\TokenGenerator;

interface TokenGenerator
{
    public function generate(array $credentials = null);
}