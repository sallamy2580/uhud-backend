<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHotelFlightForiegnToPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'airline_id')) {
                $table->dropColumn('airline_id');
            }

            if (Schema::hasColumn('packages', 'hotel_id')) {
                $table->dropColumn('hotel_id');
            }

            if (!Schema::hasColumn('packages', 'airline_id')) {
                $table->unsignedInteger('airline_id')->nullable()->after('creator');
            }

            if (!Schema::hasColumn('packages', 'hotel_id')) {
                $table->unsignedInteger('hotel_id')->nullable()->after('airline_id');
            }

            if (Schema::hasColumn('packages', 'airline_id')) {
                $table->foreign('airline_id')->references('id')->on('flights')->onDelete('set null');
            }

            if (Schema::hasColumn('packages', 'hotel_id')) {
                $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('set null');
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
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'airline_id')) {
                $table->dropForeign('packages_airline_id_foreign');
                $table->dropIndex('packages_airline_id_foreign');
            }
            if (Schema::hasColumn('packages', 'hotel_id')) {
                $table->dropForeign('packages_hotel_id_foreign');
                $table->dropIndex('packages_hotel_id_foreign');
            }
        });
    }
}
