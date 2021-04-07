<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFieldsInHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('hotels', 'en_name')) {
                $table->string('en_name')->nullable()->after('creator');
            }
            if (!Schema::hasColumn('hotels', 'price_double')) {
                $table->string('price_double')->default(0)->after('price');
            }
            if (!Schema::hasColumn('hotels', 'price_triple')) {
                $table->string('price_triple')->default(0)->after('price_double');
            }
            if (!Schema::hasColumn('hotels', 'price_quad')) {
                $table->string('price_quad')->default(0)->after('price_triple');
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
        Schema::table('hotels', function (Blueprint $table) {
            if (Schema::hasColumn('hotels', 'en_name')) {
                $table->dropColumn('en_name');
            }
            if (Schema::hasColumn('hotels', 'price_double')) {
                $table->dropColumn('price_double');
            }
            if (Schema::dropColumn('hotels', 'price_triple')) {
                $table->string('price_triple');
            }
            if (Schema::dropColumn('hotels', 'price_quad')) {
                $table->string('price_quad');
            }
        });
    }
}
