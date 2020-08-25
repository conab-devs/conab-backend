<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Address;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'street' => $faker->streetName,
        'number' => $faker->randomNumber(),
        'city' => $faker->city,
        'neighborhood' => $faker->name(),
    ];
});
