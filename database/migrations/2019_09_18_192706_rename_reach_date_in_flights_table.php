<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameReachDateInFlightsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('flights', function (Blueprint $table) {
            if (Schema::hasColumn('flights', 'reach_date')) {
                $table->renameColumn('reach_date', 'return_date');
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
            if (Schema::hasColumn('flights', 'return_date')) {
                $table->renameColumn('return_date', 'reach_date');
            }
        });
    }
}
