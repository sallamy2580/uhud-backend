<?php
namespace Seeds\Local;

use Illuminate\Database\Seeder;

class FlightSeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\FlightSeat::class, 350)->create();
    }
}
