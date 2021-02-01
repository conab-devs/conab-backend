<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ProductCart",
 *     type="object",
 *     @OA\Property(
 *        property="id",
 *        type="integer",
 *    ),
 *    @OA\Property(
 *        property="amount",
 *        type="number",
 *        description="Quantidade do produto no carrinho"
 *    ),
 *     @OA\Property(
 *        property="price",
 *        type="number",
 *        description="Preço do produto no carrinho"
 *    ),
 *     @OA\Property(
 *        property="delivered_at",
 *        type="string",
 *        format="date-time"
 *    ),
 *    @OA\Property(
 *        property="unit_of_measure",
 *        type="string",
 *        description="Unidade de medida do Produto (kg ou unit)"
 *    ),
 *    @OA\Property(
 *        property="product_id",
 *        type="integer",
 *        description="Id do produto"
 *    ),
 *    @OA\Property(
 *        property="cart_id",
 *        type="integer",
 *        description="Id do carrinho"
 *    )
 * )
 */
class ProductCart extends Model
{
    protected $fillable = [
        'product_id',
        'cart_id',
        'price',
        'amount',
        'unit_of_measure',
        'delivered_at'
    ];

    public $order_id;

    protected $guarded = ['order_id'];

    protected $appends = ['total_price'];

    protected $hidden = ['created_at', 'updated_at'];

    public function setAmountAttribute($value)
    {
        if (is_numeric($value) && $this->unit_of_measure === 'kg') {
            $value = intval($value);
        }

        $this->attributes['amount'] = $value;
    }

    public function getTotalPriceAttribute()
    {
        return $this->amount * $this->price;
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id')
            ->without('cooperative');
    }

    public function cart()
    {
        return $this->belongsTo('App\Cart', 'cart_id');
    }
}
