<?php

namespace App;

use App\Components\Errors\InvalidFieldException;
use App\Components\Errors\UnauthorizedException;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
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
            $this->attributes['password'] = bcrypt($value);
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

        if (! $token = auth()->attempt(['email' => $this->email, 'password' => $password])) {
            throw new UnauthorizedException;
        }

        return $token;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
