<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuDescTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pg__menu_desc', function(Blueprint $table) {
            $table->increments('idx');
            $table->integer('id')->unsigned();
            $table->string('lang', 4)->default('en');
            $table->string('name');
            $table->string('link')->nullable();
            $table->unique(['id', 'lang']);

            $table->foreign('id')->references('id')->on('pg__menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pg__menu_desc');
    }
}
