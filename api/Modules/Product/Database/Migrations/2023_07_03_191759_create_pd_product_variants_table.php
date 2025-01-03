<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdProductVariantsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__product_variants', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('option_id')->unsigned();
            $table->integer('option_value_id')->unsigned();

            $table->foreign('product_id')->references('id')->on('pd__products')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('pd__options')->onDelete('cascade');
            $table->foreign('option_value_id')->references('id')->on('pd__option_values')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__product_variants');
    }
}
