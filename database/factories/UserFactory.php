<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'profile_picture' => $faker->file(base_path('tmp')),
        'password' => $faker->password(),
        'cpf' => $faker->numerify('###.###.###-##'),
        'user_type' => 'CUSTOMER',
    ];
});

$factory->afterCreating(User::class, function (User $user, Faker $faker) {
    if ($user->user_type === 'ADMIN_CONAB') {
        $user->phones()->saveMany([
            factory(\App\Phone::class)->make(),
            factory(\App\Phone::class)->make(),
        ]);
    }
});
