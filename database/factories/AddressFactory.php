<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Address;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'street' => $faker->streetName,
        'neighborhood' => $faker->streetName,
        'city' => $faker->city,
        'number' => $faker->buildingNumber,
    ];
});