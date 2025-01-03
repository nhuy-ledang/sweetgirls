<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductReviewImagesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__product_review_images', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('review_id')->unsigned();
            $table->string('image')->nullable();

            $table->foreign('review_id')->references('id')->on('pd__product_reviews')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__product_review_images');
    }
}
