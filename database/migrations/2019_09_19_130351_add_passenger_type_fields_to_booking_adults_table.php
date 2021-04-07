<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPassengerTypeFieldsToBookingAdultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('booking_adults', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_adults', 'passenger_type')) {
                $table->tinyInteger('passenger_type')->default(1)->after('birth_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('booking_adults', function (Blueprint $table) {
            if (Schema::hasColumn('booking_adults', 'passenger_type')) {
                $table->dropColumn('');
            }
        });
    }
}
