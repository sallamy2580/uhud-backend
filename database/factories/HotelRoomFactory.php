<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\HotelRoom::class, function (Faker $faker) {
    return [
        'hotel_id' => function () {
            return \App\Models\Hotel::all()->random()->id;
        },
        'name' =>  $faker->name,
        'room_type' => function () {
            return rand(1,10);
        },
        'description' => $faker->text,
        'view' => function () {
            $views = [
                'see',
                'city',
                'river',
                'mountain',
                'tree'
            ];
            return $views[rand(0,4)];
        },
        'extera' => null,
        'main_img_base64' => null,
        'main_img_url' => null,
    ];
});
