<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWidgetsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pg__widgets', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('idx', 63);
            $table->string('code', 63);
            $table->longText('description')->nullable();
            $table->longText('properties')->nullable();
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
        Schema::dropIfExists('pg__widgets');
    }
}
