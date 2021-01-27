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
 *        property="status",
 *        type="string",
 *        description="Status do carrinho (Aberto, Aguardando Pagamento ou Aguardando Pagamento)"
 *    ),
 *    @OA\Property(
 *        property="order_id",
 *        type="integer",
 *        description="Id do pedido"
 *    )
 * )
 */
class Cart extends Model
{
    const STATUS_OPEN = 'Aberto';
    const STATUS_PENDING = 'Aguardando Pagamento';
    const STATUS_COMPLETED = 'Aguardando Pagamento';

    protected $fillable = [
        'status',
        'order_id'
    ];

    public function order()
    {
        return $this->belongsTo('App\Carts');
    }

    public function product_carts()
    {
        return $this->hasMany('App\ProductCart', 'cart_id');
    }
}
