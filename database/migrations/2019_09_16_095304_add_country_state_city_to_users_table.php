<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCountryStateCityToUsersTable extends Migration
{


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'country_id')) {
                $table->unsignedInteger('country_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'state_id')) {
                $table->unsignedInteger('state_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('users', 'city_id')) {
                $table->unsignedInteger('city_id')->nullable()->after('id');
            }

            if (Schema::hasColumn('users', 'country_id')) {
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
            }

            if (Schema::hasColumn('users', 'state_id')) {
                $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');
            }

            if (Schema::hasColumn('users', 'city_id')) {
                $table->foreign('city_id')->references('id')->on('cities')->onDelete('set null');
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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'country_id')) {
                $table->dropForeign('users_country_id_foreign');
                $table->dropIndex('users_country_id_foreign');
                $table->dropColumn('country_id');
            }
            if (Schema::hasColumn('users', 'state_id')) {
                $table->dropForeign('users_state_id_foreign');
                $table->dropIndex('users_state_id_foreign');
                $table->dropColumn('state_id');
            }
            if (Schema::hasColumn('users', 'city_id')) {
                $table->dropForeign('users_city_id_foreign');
                $table->dropIndex('users_city_id_foreign');
                $table->dropColumn('city_id');
            }
        });
    }
}
