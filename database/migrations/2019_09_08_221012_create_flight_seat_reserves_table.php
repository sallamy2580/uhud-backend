<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlightSeatReservesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flight_seat_reserves', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('flight_id');
            $table->unsignedInteger('seat_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('transaction_id')->nullable();
            $table->text('description');
            $table->string('extera');
            $table->integer('final_price');
            $table->timestamps();


            $table->foreign('flight_id')
                ->references('id')->on('flights')->onDelete('cascade');

            $table->foreign('seat_id')
                ->references('id')->on('flight_seats')->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')->onDelete('set null');

            $table->foreign('transaction_id')
                ->references('id')->on('transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flight_seat_reserves');
    }
}
