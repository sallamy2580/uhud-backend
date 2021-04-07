<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('creator')->comment = 'the user create this package';
            $table->unsignedInteger('airline_id');
            $table->unsignedInteger('hotel_id');
            $table->string('name',100);
            $table->tinyInteger('type')->default(0);
            $table->bigInteger('price')->default(0);
            $table->tinyInteger('status')->default(1)->comment =
                '0 deactive - 1 active - 2 banned - 3 expired - 4 removed';
            $table->string('code')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();

            $table->foreign('creator')
                ->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
