<?php

use App\Models\User;
use Faker\Generator as Faker;

$factory->define(\App\Models\Contact::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            $rand = rand(0, 1);
            if( $rand === 0 )
                return null;
            else
                return factory(App\Models\User::class)->create()->id;
        },
        'name' => $faker->name,
        'email' => $faker->email,
        'subject' => $faker->title,
        'message' => $faker->text,
    ];
});
