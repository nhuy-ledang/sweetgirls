<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdProductBestsellersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__product_bestsellers', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('pd__products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__product_bestsellers');
    }
}
