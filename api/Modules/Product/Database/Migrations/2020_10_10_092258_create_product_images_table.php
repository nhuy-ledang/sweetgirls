<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductImagesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__product_images', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->smallInteger('sort_order')->default(1);

            $table->foreign('product_id')->references('id')->on('pd__products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__product_images');
    }
}
