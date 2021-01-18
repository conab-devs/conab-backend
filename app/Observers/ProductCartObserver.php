<?php

namespace App\Observers;

use App\ProductCart;

class ProductCartObserver
{
    public function created(ProductCart $productCart)
    {
        $productCart->cart->increment('total_price', $productCart->price * $productCart->amount);
    }

    public function updated(ProductCart $productCart)
    {
        $productCart->cart->increment('total_price', $productCart->price * $productCart->amount);
    }

    public function deleted(ProductCart $productCart)
    {
        $productCart->cart->decrement('total_price', $productCart->price * $productCart->amount);
    }
}
