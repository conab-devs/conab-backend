<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'cpf', 'user_type'
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
        }
    }

    public function getPasswordAttribute()
    {
        return $this->attributes['password'];
    }

    public function getProfilePictureAttribute()
    {
        if (App::environment('testing')
            || $this->attributes['profile_picture'] === null
        ) {
            return $this->attributes['profile_picture'];
        }

        if (App::environment('production')) {
            return $this->attributes['profile_picture'];
        }

        return Storage::url($this->attributes['profile_picture']);
    }

    public function cooperative()
    {
        return $this->belongsTo('App\Cooperative');
    }

    public function phones()
    {
        return $this->belongsToMany('App\Phone', 'user_phones');
    }

    public function addresses()
    {
        return $this->belongsToMany('App\Address', 'user_addresses');
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
