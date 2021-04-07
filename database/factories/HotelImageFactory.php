<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\HotelImage::class, function (Faker $faker) {
    return [
        'hotel_id' => function () {
            return \App\Models\Hotel::all()->random()->id;
        },
        'img_base64' => '',
        'img_url' => function () {
            $images = [
                'https://www.pexels.com/photo/evening-dubai-70441/',
                'https://www.pexels.com/photo/trees-near-swimming-pool-2096983/',
                'https://www.pexels.com/photo/alcohol-architecture-bar-beer-260922/',
                'https://www.pexels.com/photo/body-of-water-near-buildings-1134176/',
                'https://www.pexels.com/photo/aisle-architecture-building-business-277572/',
                'https://www.pexels.com/photo/aisle-architecture-building-business-277572/',
                'https://www.pexels.com/photo/aisle-architecture-building-business-277572/',
                'https://www.pexels.com/photo/aerial-photography-of-city-buildings-1334605/',
                'https://www.pexels.com/photo/aerial-photography-of-city-buildings-1334605/',
                'https://www.pexels.com/photo/kuala-lumpur-building-1580112/',
                'https://www.pexels.com/photo/kuala-lumpur-building-1580112/'

            ];
            return $images[rand(0, 10)];
        }
    ];
});
