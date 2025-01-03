<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePages221017Table extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('pg__pages', function(Blueprint $table) {
            $table->boolean('bottom')->default(0);
        });
        Schema::table('pg__menus', function(Blueprint $table) {
            $table->string('source', 31)->nullable()->change();
            $table->boolean('is_sidebar')->default(1);
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
