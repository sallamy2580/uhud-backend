<?php

use App\Models\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

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

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'full_name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'phone' => $faker->unique()->phoneNumber,
        'username'=>$faker->unique()->userName,
        'role'=>$faker->randomElement([0,1,2,5]),
        'status'=>$faker->randomElement([1,2,3,4]),
        'email_verified_at' => now(),
        'password' => '123456', // secret
        'remember_token' => Str::random(10),
    ];
});
