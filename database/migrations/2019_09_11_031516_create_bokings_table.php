<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBokingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('country_id')->nullable();
            $table->unsignedInteger('city_id')->nullable();
            $table->unsignedInteger('package_id');
            $table->integer('total_price')->default(0);
            $table->string('name',100);
            $table->string('email',255);
            $table->string('phone',80);
            $table->string('region');
            $table->tinyInteger('num_childs')->default(0);
            $table->tinyInteger('num_adults')->default(2);
            $table->text('remarks');
            $table->tinyInteger('status')->comment = '1 - pending | 2 - accepted | 3 - rejected';
            $table->timestamps();


            $table->foreign('user_id')
                ->references('id')->on('users')->onDelete('cascade');

            $table->foreign('country_id')
                ->references('id')->on('countries')->onDelete('set null');

            $table->foreign('package_id')
                ->references('id')->on('packages')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bokings');
    }
}
