<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductModulesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__product_modules', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('module_id')->unsigned()->nullable();
            $table->string('translates', 63)->nullable();
            $table->string('name');
            $table->string('code', 63)->nullable();
            $table->string('title')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('table_contents')->nullable();
            $table->longText('table_images')->nullable();
            $table->longText('properties')->nullable();
            $table->boolean('is_overwrite')->default(0);
            $table->string('layout', 63)->nullable();
            $table->string('tile', 63)->nullable();
            $table->string('attach')->nullable();
            $table->string('image')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('pd__products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__product_modules');
    }
}
