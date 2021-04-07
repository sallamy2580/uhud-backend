<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlightSeatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_seats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('flight');
            $table->integer('seat_number')->default(0);
            $table->integer('seat_position')->default(0);
            $table->integer('seat_row')->default(0);
            $table->string('seat_code')->default(0);
            $table->integer('seat_ext_price')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('flight')
                ->references('id')->on('flights')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flight_seats');
    }
}
