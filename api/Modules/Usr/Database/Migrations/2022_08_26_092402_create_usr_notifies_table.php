<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsrNotifiesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('usr__notifies', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('usr_id')->unsigned()->nullable();
            $table->integer('object_id')->unsigned()->nullable();
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->string('type', 31)->nullable();
            $table->text('data')->nullable();
            $table->timestamps();

            $table->foreign('usr_id')->references('id')->on('usrs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('usr__notifies');
    }
}
