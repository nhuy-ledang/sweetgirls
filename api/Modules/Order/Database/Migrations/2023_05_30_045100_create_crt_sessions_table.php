<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrtSessionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::dropIfExists('pd__sessions');
        Schema::create('crt__sessions', function(Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('session_id', 32);
            $table->string('coupon', 10)->nullable();
            $table->string('voucher', 10)->nullable();
            $table->string('shipping_code', 31)->nullable();
            $table->decimal('shipping_fee', 15, 0)->default(0);
            $table->decimal('shipping_discount', 15, 0)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'session_id']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('crt__sessions');
    }
}
