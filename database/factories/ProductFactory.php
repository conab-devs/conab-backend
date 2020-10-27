<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'price' => $faker->randomFloat(2, 0, 100),
        'photo_path' => 'uploads/kgmxqzFk0jaZyySVIKyb1piZZDxlhsTKIpSh6MBt.png',
        'estimated_delivery_time' => $faker->randomDigitNot(0),
    ];
});
