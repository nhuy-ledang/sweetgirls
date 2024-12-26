<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManufacturersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__manufacturers', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->smallInteger('commission')->unsigned()->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('image')->nullable();
            $table->smallInteger('sort_order')->default(1);
            $table->boolean('status')->default(0);
            $table->string('alias')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__manufacturers');
    }
}
