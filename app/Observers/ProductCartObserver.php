<?php

namespace App\Observers;

use App\ProductCart;

class ProductCartObserver
{
    /**
     * Handle the product cart "created" event.
     *
     * @param  \App\ProductCart  $productCart
     * @return void
     */
    public function created(ProductCart $productCart)
    {
        $productCart->cart->increment('total_price', $productCart->price * $productCart->amount);
    }

    /**
     * Handle the product cart "updated" event.
     *
     * @param  \App\ProductCart  $productCart
     * @return void
     */
    public function updated(ProductCart $productCart)
    {
        $productCart->cart->increment('total_price', $productCart->price * $productCart->amount);
    }

    /**
     * Handle the product cart "deleted" event.
     *
     * @param  \App\ProductCart  $productCart
     * @return void
     */
    public function deleted(ProductCart $productCart)
    {
        $productCart->cart->decrement('total_price', $productCart->price * $productCart->amount);
    }

    /**
     * Handle the product cart "restored" event.
     *
     * @param  \App\ProductCart  $productCart
     * @return void
     */
    public function restored(ProductCart $productCart)
    {
        //
    }

    /**
     * Handle the product cart "force deleted" event.
     *
     * @param  \App\ProductCart  $productCart
     * @return void
     */
    public function forceDeleted(ProductCart $productCart)
    {
        //
    }
}
