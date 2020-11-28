<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'photo_path',
        'estimated_delivery_time',
        'category_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function getPhotoPathAttribute()
    {
        return App::environment('testing')
            || App::environment('production')
            || $this->attributes['photo_path'] === null
            ? $this->attributes['photo_path']
            : Storage::url($this->attributes['photo_path']);
    }

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function cooperative()
    {
        return $this->belongsTo('App\Cooperative');
    }
}
