<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('notified_by')->nullable()->comment = 'the user casue this notif or from user - who sended this notif';
            $table->unsignedInteger('owner_id')->nullable()->comment = 'to user the user must recive this notif';
            $table->tinyInteger('seen')->default(0);
            $table->tinyInteger('status')->comment = '0 deactive - 1 active - 2 banned - 3 removed';
            $table->string('type')->nullable();
            $table->string('extera')->nullable();
            $table->text('description');
            $table->text('link');
            $table->timestamps();

            $table->foreign('notified_by')
                ->references('id')->on('users')->onDelete('SET NULL');
            $table->foreign('owner_id')
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
        Schema::dropIfExists('notifications');
    }
}
