<?php

use App\Models\Ticket;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\Ticket::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(App\Models\User::class)->create()->id;
        },
        'subject' => $faker->title,
        'body' => $faker->text,
        'status' => $faker->randomElement([1, 2, 3]),
        'ticket_img_url' => $faker->randomElement([null, '', '','']),
    ];
});
