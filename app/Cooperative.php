<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cooperative extends Model
{
    //

    public function address()
    {
        return $this->hasOne('App\Address', 'address_id');
    }
}
