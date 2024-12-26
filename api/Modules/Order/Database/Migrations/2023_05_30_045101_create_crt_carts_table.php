<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrtCartsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::dropIfExists('pd__carts');
        Schema::create('crt__carts', function(Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned();
            $table->string('session_id', 32);
            // type: T:Trade (default), G:Gift (by coin), I:Included Products
            $table->enum('type', ['T', 'G', 'I'])->default('T');
            $table->smallInteger('quantity')->default(1);
            $table->text('option')->nullable(); // Remove
            $table->timestamps();
            $table->index(['user_id', 'product_id', 'session_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('pd__products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('crt__carts');
    }
}
