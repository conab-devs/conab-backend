<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Offer",
 *     type="object",
 *     @OA\Property(
 *        property="id",
 *        type="integer",
 *    ),
 *    @OA\Property(
 *        property="expired_at",
 *        type="integer",
 *        description="Data de expiração da oferta"
 *    ),
 *    @OA\Property(
 *        property="discount",
 *        type="integer",
 *        description="Valor de desconto"
 *    ),
 *    @OA\Property(
 *        property="cart_id",
 *        type="integer",
 *        description="Id do carrinho"
 *    ),
 * )
 */
class Offer extends Model
{
    protected $fillable = ['cart_id'];

    public function cart()
    {
        return $this->belongsTo('App\Cart');
    }
}
