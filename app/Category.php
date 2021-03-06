<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     @OA\Property(
 *        property="id",
 *        type="integer",
 *        description="Id da categoria"
 *    ),
 *     @OA\Property(
 *        property="name",
 *        type="string",
 *        description="Nome da categoria"
 *    ),
 *    @OA\Property(
 *        property="description",
 *        type="string",
 *        description="Descrição da categoria"
 *    )
 * )
 *
 * @OA\Schema(
 *     schema="CategoryRequest",
 *     type="object",
 *     @OA\Property(
 *        property="name",
 *        type="string",
 *        description="Nome da categoria"
 *    ),
 *    @OA\Property(
 *        property="description",
 *        type="string",
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
