<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAirlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('creator')->nullable();
            $table->unsignedInteger('origin_id')->nullable();
            $table->unsignedInteger('destination_id')->nullable();
            $table->string('origin_name',80)->nullable();
            $table->string('destination_name',80)->nullable();
            $table->string('name',80);
            $table->longText('logo_base64');
            $table->string('logo_url',150);
            $table->timestamp('takeoff_date')->nullable();
            $table->timestamp('reach_date')->nullable();
            $table->integer('price');
            $table->double('rate_avg');
            $table->timestamps();

            $table->foreign('creator')
                ->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flights');
    }
}
