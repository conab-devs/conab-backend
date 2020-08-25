<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cooperative extends Model
{
    protected $fillable = [
        'name', 'dap_path'
    ];

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function phones()
    {
        return $this->belongsToMany(Phone::class);
    }
}
