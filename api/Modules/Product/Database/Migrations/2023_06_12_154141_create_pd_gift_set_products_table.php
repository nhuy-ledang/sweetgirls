<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdGiftSetProductsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__gift_set_products', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('gift_set_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('name');
            $table->decimal('price', 15, 0)->default(0);
            $table->smallInteger('quantity')->default(0);

            $table->foreign('gift_set_id')->references('id')->on('pd__gift_sets')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('pd__products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__gift_set_products');
    }
}
