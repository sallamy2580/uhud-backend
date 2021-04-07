<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('hotel_id');
            $table->string('name');
            $table->tinyInteger('room_type');
            $table->text('description')->nullable();
            $table->string('view')->nullable();
            $table->string('extera')->nullable();
            $table->text('main_img_base64')->nullable();
            $table->string('main_img_url')->nullable();
            $table->timestamps();

            $table->foreign('hotel_id')
                ->references('id')->on('hotels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotel_rooms');
    }
}
