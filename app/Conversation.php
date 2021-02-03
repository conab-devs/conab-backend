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
        $this->belongsTo('App\Cart');
    }
}
