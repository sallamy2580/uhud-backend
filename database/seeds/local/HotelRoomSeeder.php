<?php
namespace Seeds\Local;

use Illuminate\Database\Seeder;

class HotelRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\HotelRoom::class, 200)->create();
    }
}
