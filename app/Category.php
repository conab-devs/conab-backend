<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="obejct",
 *     @OA\Property(
 *        property="name",
 *        description="Nome da categoria"
 *    ),
 *    @OA\Property(
 *        property="description",
 *        description="Descrição da categoria"
 *    )
 * )
 */
class Category extends Model
{
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany('App\Product');
    }
}
