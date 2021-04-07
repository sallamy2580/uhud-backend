<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPriceAppliedFieldToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
          if (!Schema::hasColumn('bookings', 'is_price_applied')) {
            $table->tinyInteger('is_price_applied')->default(0)->after('num_nights');
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
        Schema::table('bookings', function (Blueprint $table) {
          if (Schema::hasColumn('bookings', 'is_price_applied')) {
            $table->dropColumn('is_price_applied');
          }
        });
    }
}
