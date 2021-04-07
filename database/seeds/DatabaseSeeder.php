<?php

use Illuminate\Database\Seeder;
use Seeds\Local\RefactorCountriesSeeder;
use Seeds\Local\RefactorStateSeeder;
use Seeds\Local\TransactionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if (app()->environment(['local'])) {
            $this->call([
//                Seeds\Local\UsersSeeder::class,
//                Seeds\Local\TicketsSeeder::class,
//                Seeds\Local\TicketSectionsSeeder::class,
//                Seeds\Local\ContactSeeder::class,
//                Seeds\Local\PackageSeeder::class,
//                Seeds\Local\NotificationSeeder::class,
//                Seeds\Local\CountrySeeder::class,

//                Seeds\Local\TransactionSeeder::class,
//                Seeds\Local\HotelSeeder::class,
//                Seeds\Local\HotelImageSeeder::class,
//                Seeds\Local\HotelRoomSeeder::class,

//                Seeds\Local\FlightSeeder::class,
//                Seeds\Local\FlightSeatSeeder::class,
//                Seeds\Local\FlightSeatReserveSeeder::class,
//                Seeds\Local\StateSeeder::class,
//                Seeds\Local\CitiesSeeder::class,
//                Seeds\Local\TruncateFlightsTable::class
//                Seeds\Local\RefactorCountriesSeeder::class,
//                Seeds\Local\RefactorStateSeeder::class,
//                Seeds\Local\RefactorCitiesSeeder::class
            ]);
        }
    }
}
