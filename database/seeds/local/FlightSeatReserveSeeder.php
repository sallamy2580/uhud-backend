<?php
namespace Seeds\Local;

use Illuminate\Database\Seeder;

class FlightSeatReserveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\FlightSeatReserved::class, 350)->create();
    }
}
