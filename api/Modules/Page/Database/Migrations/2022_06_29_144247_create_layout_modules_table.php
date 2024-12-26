<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLayoutModulesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pg__layout_modules', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('layout_id')->unsigned();
            $table->integer('module_id')->unsigned()->nullable();
            $table->string('code', 63)->nullable();
            $table->string('translates', 63)->nullable();
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('table_contents')->nullable();
            $table->longText('table_images')->nullable();
            $table->longText('properties')->nullable();
            $table->boolean('is_overwrite')->default(0);
            $table->string('layout', 63)->nullable();
            $table->string('tile', 63)->nullable();
            $table->string('attach')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(1);
            $table->timestamps();

            $table->foreign('layout_id')->references('id')->on('pg__layouts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pg__layout_modules');
    }
}
