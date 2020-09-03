<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cooperative extends Model
{
    protected $fillable = ['name', 'dap_path'];

    public function phones()
    {
        return $this->belongsToMany('App\Phone', 'cooperative_phones');
    }
  
    public function address()
    {
        return $this->hasOne('App\Address', 'address_id');
    }
}