<?php

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(\App\Models\Transaction::class, function (Faker $faker) {
    return [
        'user_id' =>  function () {
            return \App\Models\User::all()->random()->id;
        },
        'package_id' =>  function () {
            return \App\Models\Package::all()->random()->id;
        },
        'title' => $faker->title,
        'amount' => function () {
            return rand(2000,20000);
        },
        'status' => function () {
            return rand(0,3);
        },
        'ref' => $faker->sha1,
    ];
});
