<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoStockRolesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sto__stock_roles', function(Blueprint $table) {
            $table->integer('stock_id')->unsigned();
            $table->integer('staff_id')->unsigned();
            $table->enum('role', ['keeper', 'seller'])->default('seller');

            $table->foreign('stock_id')->references('id')->on('sto__stocks')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('usrs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sto__stock_roles');
    }
}
