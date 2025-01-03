<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoProductsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sto__products', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('stock_id')->unsigned();
            $table->integer('ticket_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->string('content')->nullable();
            $table->integer('quantity')->unsigned()->default(0);
            $table->decimal('price', 15, 0)->default(0);
            $table->decimal('total', 15, 0)->default(0);
            $table->string('shipment', 63)->nullable();
            $table->date('due_date')->nullable();
            $table->string('code', 63)->nullable();
            $table->enum('type', ['in', 'out']);
            $table->boolean('status')->default(0);

            $table->foreign('stock_id')->references('id')->on('sto__stocks')->onDelete('cascade');
            //$table->foreign('ticket_id')->references('id')->on('sto__requests')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('pd__products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sto__products');
    }
}
