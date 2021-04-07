<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\Flight::class, function (Faker $faker) {
    return [
        'creator' => function () {
            return \App\User::all()->random()->id;
        },
        'origin_id' => null,
        'destination_id' => null,
        'origin_name' => $faker->city,
        'destination_name' => $faker->city,
        'name' => $faker->name,
        'logo_base64' => '',
        'logo_url' => 'https://www.pexels.com/photo/illuminated-buildings-in-the-city-2880607/',
        'takeoff_date' => function(){
            $now = new \Carbon\Carbon();
            return $now->addDays(10,20);
        },
        'reach_date' => function(){
            $now = new \Carbon\Carbon();
            return $now->addDays(21,30);
        },
        'price' => function(){
            return rand(10000,50000);
        },
        'rate_avg' => function(){
            return rand(1,5);
        }
    ];
});
