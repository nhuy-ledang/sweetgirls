<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdGiftOrdersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__gift_orders', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->decimal('total', 15, 0)->default(0);
            $table->decimal('amount', 15, 0)->default(0);
            $table->text('description')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->integer('limited')->nullable();
            $table->integer('uses_total')->unsigned()->default(0);
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__gift_orders');
    }
}
