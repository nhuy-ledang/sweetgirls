<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderHistoriesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('order__histories', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->enum('status', ['pending', 'in_process', 'completed', 'failed', 'unknown', 'paid', 'refunded', 'canceled'])->nullable(); // Will remove
            $table->enum('order_status', ['pending', 'processing', 'shipping', 'completed', 'canceled', 'returning', 'returned'])->nullable();
            $table->enum('payment_status', ['pending', 'in_process', 'paid', 'failed', 'unknown', 'refunded', 'canceled'])->nullable();
            $table->boolean('notify')->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('order__histories');
    }
}
