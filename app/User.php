<?php

namespace App;

use App\Components\Errors\InvalidFieldException;
use App\Components\Errors\UnauthorizedException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'user_type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function setPasswordAttribute($value)
    {
        if ($value !== null) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = null;
        }
    }

    public function getPasswordAttribute()
    {
        return $this->attributes['password'];
    }

    public function login(string $password, string $device_name)
    {
        $fields = ['email', 'password'];
        $permissions = [
            'CUSTOMER' => 'MOBILE', 
            'ADMIN_COOP' => 'MOBILE', 
            'ADMIN_CONAB' => 'WEB', 
            'SUPER_ADMIN' => 'WEB'
        ];

        foreach ($fields as $field) {
            if ($this[$field] === null) {
                throw new InvalidFieldException;
            }
        }
        
        if ($device_name !== $permissions[$this->user_type]) {
            throw new UnauthorizedException;
        }

        if (!Hash::check($password, $this->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $this->createToken($device_name);
    }

    public function logout()
    {
        return ($this->tokens()->delete()) ? true : false;
    }
}
