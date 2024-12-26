<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pg__modules', function(Blueprint $table) {
            $table->increments('id');
            $table->string('translates', 63)->nullable();
            $table->string('name');
            $table->string('idx', 63);
            $table->string('code', 63);
            $table->string('title')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->longText('table_contents')->nullable();
            $table->longText('table_images')->nullable();
            $table->longText('properties')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->unique(['idx', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pg__modules');
    }
}
