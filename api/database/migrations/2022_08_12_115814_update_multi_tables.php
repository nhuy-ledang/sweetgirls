<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMultiTables extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasColumn('mkt__campaigns', 'image')) {
            Schema::table('mkt__campaigns', function(Blueprint $table) {
                $table->string('image')->nullable();
            });
        }
        if (!Schema::hasColumn('blg__categories', 'layout')) {
            Schema::table('blg__categories', function(Blueprint $table) {
                $table->string('layout', 63)->nullable();
            });
        }
        if (!Schema::hasColumn('pd__categories', 'layout')) {
            Schema::table('pd__categories', function(Blueprint $table) {
                $table->string('layout', 63)->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
    }
}
