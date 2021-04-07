<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('user_id');
            $table->longText('body',15000);
            $table->tinyInteger('user_role')->default(1)->comment = '0 or 1 for it admin 2 for agent 5 for user';
            $table->longText('ticket_img')->nullable()->comment = 'for base64 encode string';
            $table->string('ticket_img_url')->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')
                ->references('id')->on('tickets')->onDelete('cascade');
            $table->foreign('user_id')
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
        Schema::dropIfExists('ticket_sections');
    }
}
