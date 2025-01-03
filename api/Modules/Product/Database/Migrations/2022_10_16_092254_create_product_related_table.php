<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductRelatedTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__product_related', function(Blueprint $table) {
            $table->integer('product_id')->default(0);
            $table->integer('related_id')->default(0);
            $table->smallInteger('sort_order')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__product_related');
    }
}
