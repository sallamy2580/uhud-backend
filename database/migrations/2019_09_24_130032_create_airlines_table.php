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
        Schema::create('airlines', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('creator')->nullable();
            $table->unsignedInteger('country_id')->nullable();
            $table->string('name',80);
            $table->longText('logo_base64');
            $table->string('logo_url',150);
            $table->double('rate_avg');
            $table->timestamps();

            $table->foreign('creator')
                ->references('id')->on('users')->onDelete('set null');
            $table->foreign('country_id')
                ->references('id')->on('countries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airlines');
    }
}
