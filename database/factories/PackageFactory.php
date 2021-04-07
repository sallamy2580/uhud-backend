<?php

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(\App\Models\Package::class, function (Faker $faker) {
    return [
        'creator' => function () {
            return factory(App\Models\User::class)->create()->id;
        },
        'airline_id' => 0,
        'hotel_id' => 0,
        'name' => $faker->name,
        'type' => 0,
        'price' => function(){
            return rand(1000,100000);
        },
        'status' => $faker->randomElement([0, 1, 2, 3, 4]),
        'code' => $faker->sha1,
        'start_date' => function () {
            return new Carbon\Carbon();
        },
        'end_date' => function () {
            $now = new Carbon\Carbon();
            return $now->addDays(rand(20,200));
        },
    ];
});
