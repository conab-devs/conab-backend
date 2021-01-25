<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Cart",
 *     type="object",
 *     @OA\Property(
 *        property="id",
 *        type="integer",
 *    ),
 *    @OA\Property(
 *        property="total_price",
 *        type="number",
 *        description="Preço total dos items dentro do carrinho de compras"
 *    ),
 *    @OA\Property(
 *        property="closed_at",
 *        type="string",
 *        format="date-time"
 *    ),
 *    @OA\Property(
 *        property="is_closed",
 *        type="boolean"
 *    ),
 *    @OA\Property(
 *        property="user_id",
 *        type="integer",
 *        description="Id do usuário dono do carrinho de compras"
 *    )
 * )
 */
class Cart extends Model
{
    protected $table = 'carts';

    protected $fillable = [
        'user_id',
        'is_closed'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function product_carts()
    {
        return $this->hasMany('App\ProductCart', 'cart_id');
    }
}
