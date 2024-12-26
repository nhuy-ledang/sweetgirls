<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('order__products', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('name')->nullable();
            $table->string('model', 64)->nullable();
            // type: T:Trade (default), G:Gift (by coin), I:Included Products
            $table->enum('type', ['T', 'G', 'I'])->default('T');
            $table->smallInteger('quantity')->unsigned()->default(0);
            $table->decimal('priceo', 15, 0)->default(0);
            $table->decimal('price', 15, 0)->default(0);
            $table->decimal('total', 15, 0)->default(0);
            $table->integer('coins')->unsigned()->default(0);
            $table->string('message', 191)->nullable();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('order__products');
    }
}
