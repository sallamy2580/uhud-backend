<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\FlightSeatReserved::class, function (Faker $faker) {
    return [
        'flight_id' => function () {
            return \App\Models\Flight::all()->random()->id;
        },
        'seat_id' => function () {
            return \App\Models\FlightSeat::all()->random()->id;
        },
        'user_id' => function () {
            return \App\Models\User::all()->random()->id;
        },
        'transaction_id' => function () {
            return \App\Models\Transaction::all()->random()->id;
        },
        'description' => $faker->text,
        'extera' => '',
        'final_price' => function(){
            return rand(2000,100000);
        }
    ];
});
