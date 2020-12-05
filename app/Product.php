<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(
 *        property="name",
 *        type="string",
 *        description="Nome do produto"
 *    ),
 *     @OA\Property(
 *        property="price",
 *        type="float",
 *        description="Preço do produto"
 *    ),
 *     @OA\Property(
 *        property="photo_path",
 *        type="string",
 *        description="Foto do produto"
 *    ),
 *    @OA\Property(
 *        property="estimated_delivery_time",
 *        type="date-time",
 *        description="Tempo estimado de entrega do produto em dias"
 *    ),
 *    @OA\Property(
 *        property="category_id",
 *        type="integer",
 *        description="Id da categoria que o produto pertence"
 *    ),
 *    @OA\Property(
 *        property="cooperative_id",
 *        type="integer",
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
