<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Conversation",
 *     type="object",
 *     @OA\Property(
 *        property="id",
 *        type="integer",
 *    ),
 *    @OA\Property(
 *        property="cart_id",
 *        type="integer",
 *        description="Id do carrinho"
 *    )
 * )
 */
class Conversation extends Model
{
    protected $fillable = ['cart_id'];

    public function cart()
    {
        return $this->belongsTo('App\Cart');
    }

    public function cooperative()
    {
        return $this->cart()->product_carts()[0]->product()->cooperative();
    }

    public function user()
    {
        return $this->belongsTo('App\Cart')->order()->user();
    }
}
