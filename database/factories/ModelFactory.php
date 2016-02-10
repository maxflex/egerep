<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});


$factory->define(App\Models\Client::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'phone' => '7' . str_replace(['(', ')', ' ', '-', '+'], '', $faker->phoneNumber),
        'grade' => $faker->numberBetween(9, 11),
        'address'  => $faker->address,
    ];
});


$factory->define(App\Models\Request::class, function (Faker\Generator $faker) {
    $status = ['new', 'finished'];
    return [
        'comment' => $faker->realText(),
        'status' => $status[array_rand($status)],
        'client_id' => $faker->numberBetween(1, 8),
    ];
});
