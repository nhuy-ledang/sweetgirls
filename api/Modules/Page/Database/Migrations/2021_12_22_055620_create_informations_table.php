<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInformationsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pg__informations', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->default(0);
            $table->string('translates', 63)->nullable();
            $table->string('title');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();
            $table->smallInteger('sort_order')->default(1);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('pg__informations');
    }
}
