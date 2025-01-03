<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pg__menus', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->default(0);
            $table->integer('page_id')->unsigned()->nullable();
            $table->string('translates', 63)->nullable();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            //$table->enum('source', ['product'])->nullable();
            $table->string('source', 31)->nullable();
            $table->string('link')->nullable();
            $table->boolean('is_sub')->default(0);
            $table->boolean('is_redirect')->default(0);
            $table->boolean('is_sidebar')->default(0);
            $table->boolean('is_footer')->default(0);
            $table->boolean('is_header')->default(0);
            $table->integer('sort_order')->default(1);
            $table->boolean('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pg__menus');
    }
}
