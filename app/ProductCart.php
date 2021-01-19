<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    protected $hidden = ['created_at', 'updated_at'];

    public function setAmountAttribute($value)
    {
        if (is_numeric($value) && $this->unit_of_measure === 'kg') {
            $value = intval($value);
        }

        $this->attributes['amount'] = $value;
    }

    public function cart()
    {
        return $this->belongsTo('App\Cart', 'cart_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Product', 'product_id')
            ->without('cooperative');
    }
}
