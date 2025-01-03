<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductModuleDescTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__product_module_desc', function(Blueprint $table) {
            $table->increments('idx');
            $table->integer('id')->unsigned();
            $table->string('lang', 4)->default('en');
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('table_contents')->nullable();
            $table->longText('table_images')->nullable();
            $table->string('menu_text')->nullable();
            $table->string('btn_text')->nullable();
            $table->string('btn_link')->nullable();
            $table->unique(['id', 'lang']);

            $table->foreign('id')->references('id')->on('pd__product_modules')->onDelete('cascade');
        });
        Schema::table('pd__product_modules', function(Blueprint $table) {
            $table->string('menu_text')->nullable();
            $table->string('btn_text')->nullable();
            $table->string('btn_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__product_module_desc');
    }
}
