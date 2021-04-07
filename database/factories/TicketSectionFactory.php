<?php

use App\Models\Ticket;
use App\Models\User;
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

$factory->define(App\Models\TicketSection::class, function (Faker $faker) {
    return [
        'ticket_id' => function () {
            $tickets = App\Models\Ticket::all();
            return $tickets->random()->id;
        },
        'user_id' => function () {
            $users = App\Models\User::all();
            return $users->random()->id;
        },
        'body' => $faker->text,
        'user_role' => $faker->randomElement([0,1, 2, 5]),
        'ticket_img_url' => $faker->randomElement([null, '', '','']),
    ];
});
