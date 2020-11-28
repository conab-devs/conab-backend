<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
    protected $fillable = ['number'];

    protected $hidden = ['created_at', 'updated_at', 'id'];

    public function cooperative()
    {
        return $this->belongsToMany('App\Cooperative', 'cooperative_phones');
    }
  
    public function users()
    {
        return $this->belongsToMany('App\User', 'user_phones');
    }
}
