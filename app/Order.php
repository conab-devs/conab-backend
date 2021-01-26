<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Order",
 *     type="object",
 *     @OA\Property(
 *        property="id",
 *        type="integer",
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
class Order extends Model
{
    protected $fillable = [
        'user_id',
        'is_closed'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function carts()
    {
        return $this->hasMany('App\Cart', 'order_id');
    }
}
