<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageContentDescTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pg__page_content_desc', function(Blueprint $table) {
            $table->increments('idx');
            $table->integer('id')->unsigned();
            $table->string('lang', 4)->default('en');
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('table_contents')->nullable();
            $table->longText('table_images')->nullable();
            $table->unique(['id', 'lang']);

            $table->foreign('id')->references('id')->on('pg__page_contents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pg__page_content_desc');
    }
}