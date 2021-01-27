<?php

namespace App\Observers;

use App\Cart;
use App\Order;
use App\ProductCart;

class ProductCartObserver
{
    public function creating(ProductCart $productCart)
    {
        $order_id = $productCart->order_id;

        $product_cart = Order::findOrFail($order_id)->product_carts->first(function ($product_cart, $key) use ($productCart) {
            return $product_cart->product->cooperative->id == $productCart->product->cooperative->id;
        });

        $cart = new Cart();


        if ($product_cart === null) {
            ($cart->fill([
                'order_id' => $order_id,
                'status' => Cart::STATUS_OPEN
            ]))->saveOrFail();
        } else {
            $cart = $product_cart->cart;
        }

        $productCart->cart_id = $cart->id;
    }
}
