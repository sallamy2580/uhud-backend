<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('creator')->nullable();
            $table->string('name',100);
            $table->longText('description');
            $table->string('address',100);
            $table->string('lat',50);
            $table->string('lng',50);
            $table->tinyInteger('accomodation')->comment = '1 one star 2 two star 3 three star ... to 5 and 6';
            $table->longText('main_image_base64')->nullable();
            $table->string('main_image_url',200)->nullable();
            $table->integer('price')->default(0);
            $table->timestamps();

            $table->foreign('creator')
                ->references('id')->on('users')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hotels');
    }
}
