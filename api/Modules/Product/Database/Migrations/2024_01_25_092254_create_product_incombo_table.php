<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductIncomboTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__product_incombo', function(Blueprint $table) {
            $table->integer('product_id')->unsigned();
            $table->integer('incombo_id')->unsigned();
            $table->integer('quantity')->default(1);
            $table->smallInteger('sort_order')->default(1);

            $table->foreign('product_id')->references('id')->on('pd__products')->onDelete('cascade');
            $table->foreign('incombo_id')->references('id')->on('pd__products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__product_incombo');
    }
}
