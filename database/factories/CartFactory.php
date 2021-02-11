<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cart;
use App\Order;
use Faker\Generator as Faker;

$factory->define(Cart::class, function (Faker $faker) {
    return [
        'status' => Cart::STATUS_OPEN,
        'discount' => 0.0,
        'order_id' => factory(Order::class),
    ];
});
