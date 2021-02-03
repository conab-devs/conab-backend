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
 *        description="Status do carrinho (Aberto, Aguardando Pagamento ou Concluído)"
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
    const STATUS_COMPLETED = 'Concluído';

    protected $fillable = [
        'status',
        'order_id'
    ];

    protected $appends = ['total_price'];

    public function getTotalPriceAttribute()
    {
        return $this->product_carts->sum('total_price');
    }

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function product_carts()
    {
        return $this->hasMany('App\ProductCart', 'cart_id');
    }
}
