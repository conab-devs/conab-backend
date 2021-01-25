<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'price' => $faker->randomFloat(2, 0, 100),
        'photo_path' => UploadedFile::fake()->image($faker->sha256().'.png', 400, 400)->store('uploads'),
        'estimated_delivery_time' => $faker->randomDigitNot(0),
        'category_id' => factory(\App\Category::class),
        'cooperative_id' => factory(\App\Cooperative::class),
    ];
});
