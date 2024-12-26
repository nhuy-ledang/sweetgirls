<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductDescTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__product_desc', function(Blueprint $table) {
            $table->increments('idx');
            $table->integer('id')->unsigned();
            $table->string('lang', 4)->default('en');
            $table->string('name');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->string('long_name')->nullable();
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->text('properties')->nullable();
            $table->text('user_guide')->nullable();
            $table->text('tag')->nullable();
            $table->string('delivery')->nullable();
            $table->string('warranty')->nullable();
            $table->string('model')->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('alias')->nullable();
            $table->unique(['id', 'lang']);

            $table->foreign('id')->references('id')->on('pd__products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__product_desc');
    }
}
