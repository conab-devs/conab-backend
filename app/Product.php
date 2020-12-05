<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="obejct",
 *     @OA\Property(
 *        property="name",
 *        description="Nome do produto"
 *    ),
 *     @OA\Property(
 *        property="price",
 *        description="Preço do produto"
 *    ),
 *     @OA\Property(
 *        property="photo_path",
 *        description="Foto do produto"
 *    ),
 *    @OA\Property(
 *        property="estimated_delivery_time",
 *        description="Tempo estimado de entrega do produto em dias"
 *    ),
 *    @OA\Property(
 *        property="category_id",
 *        description="Id da categoria que o produto pertence"
 *    ),
 *    @OA\Property(
 *        property="cooperative_id",
 *        description="Id da cooperativa que o produto pertence"
 *    )
 * )
 */
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
