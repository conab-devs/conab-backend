<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Cooperative extends Model
{
    protected $fillable = ['name', 'dap_path'];

    protected $appends = ['dap_url'];

    protected $hidden = ['dap_path'];

    public function phones()
    {
        return $this->belongsToMany('App\Phone', 'cooperative_phones');
    }

    public function address()
    {
        return $this->belongsTo('App\Address', 'address_id');
    }

    public function admins()
    {
        return $this->hasMany('App\User', 'cooperative_id');
    }

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function getDapUrlAttribute() {
        if (App::environment(['local','testing'])) {
            return Storage::url($this->attributes['dap_path']);
        }

        return $this->attributes['dap_path'];
    }
}
