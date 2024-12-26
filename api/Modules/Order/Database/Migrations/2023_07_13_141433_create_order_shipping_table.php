<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderShippingTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('order__shipping', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->string('order_number', 31);
            $table->decimal('money_collection', 15, 0)->default(0);
            $table->decimal('exchange_weight', 15, 0)->default(0);
            $table->decimal('money_total', 15, 0)->default(0);
            $table->decimal('money_total_fee', 15, 0)->default(0);
            $table->decimal('money_fee', 15, 0)->default(0);
            $table->decimal('money_collection_fee', 15, 0)->default(0);
            $table->decimal('money_other_fee', 15, 0)->default(0);
            $table->decimal('money_vas', 15, 0)->default(0);
            $table->decimal('money_vat', 15, 0)->default(0);
            $table->decimal('kpi_ht', 15, 0)->default(0);
            $table->integer('receiver_province')->unsigned()->default(0);
            $table->integer('receiver_district')->unsigned()->default(0);
            $table->integer('receiver_wards')->unsigned()->default(0);
            $table->text('params')->nullable();
            $table->text('data')->nullable();
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
        Schema::dropIfExists('order__shipping');
    }
}
