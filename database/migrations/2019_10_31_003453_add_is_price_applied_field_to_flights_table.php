<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsPriceAppliedFieldToFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flights', function (Blueprint $table) {
          if (!Schema::hasColumn('flights', 'is_price_applied')) {
            $table->tinyInteger('is_price_applied')->default(0)->after('seat_availability');
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
        Schema::table('flights', function (Blueprint $table) {
          if (Schema::hasColumn('flights', 'is_price_applied')) {
            $table->dropColumn('is_price_applied');
          }
        });
    }
}
