<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePdProducts240517Table extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('pd__products', function(Blueprint $table) {
            $table->string('unit')->nullable()->after('quantity');
            $table->string('idx')->nullable()->after('model');
        });

        DB::unprepared("UPDATE `pd__products` SET `idx` = `model`");
        DB::unprepared("UPDATE `pd__products` SET `quantity` = 0");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}
