<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cooperative;
use Faker\Generator as Faker;

$factory->define(Cooperative::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'dap_path' => $faker->imageUrl(),
        'address_id' => factory(App\Address::class),
    ];
});
