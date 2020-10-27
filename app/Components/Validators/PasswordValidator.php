<?php

namespace App\Components\Validators;

use Illuminate\Support\Facades\Hash;

class PasswordValidator
{
    public static function validate(?string $password, string $encryptedPassword)
    {
        return static::isPasswordUnique($password, $encryptedPassword);
    }

    private static function isPasswordUnique($password, $encryptedPassword)
    {
        if (empty($password)) {
            return false;
        }
        return Hash::check($password, $encryptedPassword);
    }
}
