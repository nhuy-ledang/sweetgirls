<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductSpecDescTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__product_spec_desc', function(Blueprint $table) {
            $table->increments('idx');
            $table->integer('id')->unsigned();
            $table->string('lang', 4)->default('en');
            $table->string('name')->nullable();
            $table->string('value')->nullable();
            $table->unique(['id', 'lang']);

            $table->foreign('id')->references('id')->on('pd__product_specs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__product_spec_desc');
    }
}
