<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pg__pages', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->default(0);
            $table->integer('category_id')->unsigned()->nullable();
            $table->string('translates', 63)->nullable();
            $table->string('name');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('table_contents')->nullable();
            $table->longText('properties')->nullable();
            $table->string('style', 63)->nullable();
            $table->boolean('is_sub')->default(0);
            $table->boolean('is_land')->default(0);
            $table->boolean('home')->default(0);
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('banner')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('status')->default(1);
            $table->string('alias')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pg__pages');
    }
}
