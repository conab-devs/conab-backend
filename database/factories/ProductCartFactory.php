<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ProductCart;
use App\Product;
use App\Cart;
use Faker\Generator as Faker;

$factory->define(ProductCart::class, function (Faker $faker) {
    return [
        'amount' => 1,
        'price' => 1.1,
        'unit_of_measure' => 'kg',
        'product_id' => factory(Product::class),
        'cart_id' => factory(Cart::class),
    ];
});
