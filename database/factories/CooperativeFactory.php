<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Cooperative;
use App\Address;
use Faker\Generator as Faker;

$factory->define(Cooperative::class, function (Faker $faker) {
    return [
        'name' => $faker->name(),
        'dap_path' => $faker->file()
    ];
});

$factory->afterCreating(Cooperative::class, function (Cooperative $cooperative, Faker $faker) {
    $cooperative->address()->save(factory(Address::class)->make());
});
