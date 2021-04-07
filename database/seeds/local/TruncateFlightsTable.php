<?php
namespace Seeds\Local;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TruncateFlightsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

//        DB::statement('ALTER TABLE flight_seat_reserves DROP FOREIGN KEY flight_seat_reserves_flight_id_foreign');
//        DB::statement('TRUNCATE TABLE flight_seat_reserves');
//        DB::statement("ALTER TABLE flight_seat_reserves ADD CONSTRAINT flight_seat_reserves_flight_id_foreign FOREIGN KEY (flight_id) REFERENCES flights(id)");

//        DB::statement('ALTER TABLE flight_seat_reserves DROP FOREIGN KEY flight_seat_reserves_seat_id_foreign');
//        DB::statement('TRUNCATE TABLE flight_seat_reserves');
//        DB::statement("ALTER TABLE flight_seat_reserves ADD CONSTRAINT flight_seat_reserves_seat_id_foreign FOREIGN KEY (seat_id) REFERENCES flight_seats(id)");

//        DB::statement('ALTER TABLE flight_seats DROP FOREIGN KEY flight_seats_flight_foreign');
//        DB::statement('TRUNCATE TABLE flight_seats');
//        DB::statement("ALTER TABLE flight_seats ADD CONSTRAINT flight_seats_flight_foreign FOREIGN KEY (flight) REFERENCES flights(id)");

//        DB::statement('ALTER TABLE flights DROP FOREIGN KEY flights_creator_foreign');
//        DB::statement('TRUNCATE TABLE flights');
//        DB::statement("ALTER TABLE flights ADD CONSTRAINT flights_creator_foreign FOREIGN KEY (creator) REFERENCES users(id)");
    }
}
