<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoInventoryProductsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sto__inventory_products', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('stock_id')->unsigned()->nullable(); // can be null for client testing
            $table->integer('inventory_id')->unsigned()->nullable(); // can be null for client testing
            $table->integer('product_id')->unsigned();
            $table->string('unit')->nullable();
            $table->integer('quantity')->unsigned()->default(0);
            $table->integer('reality')->unsigned()->default(0);
            $table->string('note')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->foreign('stock_id')->references('id')->on('sto__stocks')->onDelete('cascade');
            $table->foreign('inventory_id')->references('id')->on('sto__inventories')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('pd__products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sto__inventory_products');
    }
}
