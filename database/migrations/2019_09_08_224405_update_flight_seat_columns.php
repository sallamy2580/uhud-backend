<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateFlightSeatColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flight_seats', function (Blueprint $table) {
//            if (Schema::hasColumn('flight_seats', 'seat_number')) {
//                $table->integer('seat_number')->default(0)->change();
//            }
//            if (Schema::hasColumn('flight_seats', 'seat_position')) {
//                $table->integer('seat_position')->default(0)->change();
//            }
//            if (Schema::hasColumn('flight_seats', 'seat_row')) {
//                $table->integer('seat_row')->default(0)->change();
//            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('flight_seats', function (Blueprint $table) {
//            if (Schema::hasColumn('flight_seats', 'seat_number')) {
//                $table->tinyInteger('seat_number')->default(0);
//            }
//            if (Schema::hasColumn('flight_seats', 'seat_position')) {
//                $table->tinyInteger('seat_position')->default(0);
//            }
//            if (Schema::hasColumn('flight_seats', 'seat_row')) {
//                $table->tinyInteger('seat_row')->default(0);
//            }
        });
    }
}
