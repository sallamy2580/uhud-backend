<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAirlineIfFieldToFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flights', function (Blueprint $table) {
            if (!Schema::hasColumn('flights', 'airline_id')) {
                $table->unsignedInteger('airline_id')->nullable()->after('id');

                $table->foreign('airline_id')
                    ->references('id')->on('airlines')->onDelete('cascade');
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
            if (Schema::hasColumn('flights', 'airline_id')) {
                $table->dropColumn('airline_id');
            }
        });
    }
}
