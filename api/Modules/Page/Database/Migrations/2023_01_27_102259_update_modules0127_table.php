<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateModules0127Table extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('pg__modules', function(Blueprint $table) {
            $table->smallInteger('sort_order')->default(1);
        });
        DB::unprepared("UPDATE `pg__modules` SET `sort_order` = '999' WHERE 1;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}
