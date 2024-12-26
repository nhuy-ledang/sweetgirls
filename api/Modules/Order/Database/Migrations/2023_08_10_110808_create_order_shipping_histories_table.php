<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//[
//	"order_number" => "18466196770",
//	"order_reference" => "ST-INV-0011#29",
//	"order_statusdate" => "19/07/2023 12:13:04",
//	"order_status" => int(107),
//	"status_name" => "Đối tác yêu cầu hủy qua API",
//	"localion_currently" => NULL,
//	"note" => "test",
//	"money_collection" => int(2278000),
//	"money_feecod" => int(0),
//	"money_totalfee" => int(29091),
//	"money_total" => int(32000),
//	"expected_delivery" => NULL,
//	"product_weight" => int(300),
//	"order_service" => string(4) "LCOD",
//	"order_payment" => int(2),
//	"expected_delivery_date" => NULL,
//	"detail" => [],
//	"voucher_value" => int(0),
//	"money_collection_origin" => NULL,
//	"employee_name" => NULL,
//	"employee_phone" => NULL,
//	"is_returning" => bool(false),
//]

class CreateOrderShippingHistoriesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('order__shipping_histories', function(Blueprint $table) {
            $table->id();
            $table->integer('order_id')->unsigned();
            $table->string('order_number', 31);
            $table->smallInteger('order_status')->nullable();
            $table->string('note')->nullable();
            $table->decimal('money_collection', 15, 0)->nullable();
            $table->decimal('money_feecod', 15, 0)->nullable();
            $table->decimal('money_totalfee', 15, 0)->nullable();
            $table->decimal('money_total', 15, 0)->nullable();
            $table->decimal('product_weight', 15, 0)->nullable();
            $table->string('order_service', 31)->nullable();
            $table->smallInteger('order_payment')->nullable();
            $table->decimal('voucher_value', 15, 0)->nullable();
            $table->string('employee_name')->nullable();
            $table->string('employee_phone')->nullable();
            $table->string('localion_currently')->nullable();
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
        Schema::dropIfExists('order__shipping_histories');
    }
}
