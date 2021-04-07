<?php

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(\App\Models\Notification::class, function (Faker $faker) {
    return [
        'notified_by' => function () {
            return \App\User::all()->random()->id;
        },
        'owner_id' => function () {
            return \App\User::all()->random()->id;
        },
        'seen' => function () {
            return rand(0,1);
        },
        'status' => function () {
            return rand(0,3);
        },
        'type' => null,
        'extera' => null,
        'description' => $faker->text,
        'link' => $faker->url,
    ];
});
