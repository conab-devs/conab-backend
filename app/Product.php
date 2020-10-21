<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'photo_path',
        'estimated_delivery_time',
        'category_id',
        'cooperative_id'
    ];

    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function cooperative()
    {
        return $this->belongsTo('App\Cooperative');
    }
}
