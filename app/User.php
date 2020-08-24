<?php

namespace App;

use App\Components\Errors\InvalidFieldException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Components\Errors\UnauthorizedException;


class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'user_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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
        
        foreach ($fields as $field) {
            if ($this[$field] === null) {
                throw new InvalidFieldException;
            }
        }

        if ($device_name === 'WEB' && $this->user_type !== 'ADMIN_CONAB') {
            throw new UnauthorizedException;
        }

        if (!Hash::check($password, $this->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
    }
}
