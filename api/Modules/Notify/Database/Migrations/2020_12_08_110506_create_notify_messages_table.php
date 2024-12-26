<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotifyMessagesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('notify__messages', function(Blueprint $table) {
            $table->increments('id');

            $table->integer('from')->unsigned();
            $table->integer('to')->unsigned()->nullable();
            $table->text('message')->nullable();
            $table->boolean('readed')->default(0);
            $table->timestamps();

            $table->foreign('from')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('notify__messages');
    }
}
