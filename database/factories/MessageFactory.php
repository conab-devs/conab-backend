<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Message;
use Faker\Generator as Faker;

$factory->define(Message::class, function (Faker $faker) {
    return [
        'content' => $faker->sentence,
        'user_id' => factory(\App\User::class),
        'cooperative_id' => factory(\App\Cooperative::class),
    ];
});
