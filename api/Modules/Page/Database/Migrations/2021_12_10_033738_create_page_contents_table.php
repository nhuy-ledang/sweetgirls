<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageContentsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pg__page_contents', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('page_id')->unsigned();
            $table->integer('parent_id')->unsigned()->default(0);
            $table->integer('module_id')->unsigned()->nullable();
            $table->string('translates', 63)->nullable();
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('table_contents')->nullable();
            $table->longText('table_images')->nullable();
            $table->longText('properties')->nullable();
            $table->string('style', 63)->nullable();
            $table->string('code', 63)->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('page_id')->references('id')->on('pg__pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pg__page_contents');
    }
}
