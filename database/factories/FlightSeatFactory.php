<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\FlightSeat::class, function (Faker $faker) {
    return [
        'flight' => function () {
            return \App\Models\Flight::all()->random()->id;
        },
        'seat_number' => function(){
            return rand(1,300);
        },
        'seat_position' => function(){
            return rand(1,20);
        },
        'seat_row' => function(){
            return rand(1,50);
        },
        'seat_code' => function(){
            return rand(111111,999999);
        },
        'seat_ext_price' => function(){
            return rand(50,50000);
        },
        'description' => $faker->text
    ];
});
