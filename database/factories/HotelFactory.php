<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Hotel::class, function (Faker $faker) {
    return [
        'creator' => function () {
            return \App\Models\User::all()->random()->id;
        },
        'name' => $faker->title,
        'description' => $faker->text,
        'address' => $faker->address,
        'lat' => $faker->latitude,
        'lng' => $faker->longitude,
        'accomodation' => function () {
            return rand(1,6);
        },
        'main_image_base64' => null,
        'main_image_url' => 'https://www.pexels.com/photo/architecture-hall-hallway-interior-design-53464',
        'price' => function () {
            return rand(300,15000);
        },
    ];
});
