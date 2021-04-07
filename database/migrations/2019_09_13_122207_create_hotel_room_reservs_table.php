<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelRoomReservsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_room_reservs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('hotel_id');
            $table->unsignedInteger('room_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('transaction_id')->nullable();
            $table->text('description');
            $table->string('extera');
            $table->integer('final_price');
            $table->timestamps();


            $table->foreign('hotel_id')
                ->references('id')->on('hotels')->onDelete('cascade');

            $table->foreign('room_id')
                ->references('id')->on('hotel_rooms')->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')->onDelete('no action');

            $table->foreign('transaction_id')
                ->references('id')->on('transactions')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_room_reservs');
    }
}
