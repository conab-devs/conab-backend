<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'street', 'neighborhood', 'city', 'number'
    ];

    public function cooperative()
    {
        return $this->hasOne(Cooperative::class);
    }

    public function user()
    {
        return $this->hasOne('App\User', 'user_addresses');
    }
}
