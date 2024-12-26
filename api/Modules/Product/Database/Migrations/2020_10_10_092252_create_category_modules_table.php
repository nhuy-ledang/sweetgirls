<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryModulesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__category_modules', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned();
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
            $table->string('menu_text')->nullable();
            $table->string('btn_text')->nullable();
            $table->string('btn_link')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('pd__categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__category_modules');
    }
}
