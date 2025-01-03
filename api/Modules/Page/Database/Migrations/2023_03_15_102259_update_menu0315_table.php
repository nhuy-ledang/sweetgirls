<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMenu0315Table extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('pg__menus', function(Blueprint $table) {
            $table->boolean('is_header')->default(1);
        });
        DB::unprepared("UPDATE `pg__menus` SET `is_header` = 0 WHERE `is_footer` = 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}
