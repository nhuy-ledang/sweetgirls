<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePageContentsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('pg__page_contents', function(Blueprint $table) {
            $table->boolean('is_overwrite')->default(0);
            $table->string('layout', 63)->nullable();
            $table->string('tile', 63)->nullable();
            $table->string('attach')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}
