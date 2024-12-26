<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePgModulesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasColumn('pg__page_content_desc', 'menu_text')) {
            Schema::table('pg__page_content_desc', function(Blueprint $table) {
                $table->string('menu_text')->nullable();
                $table->string('btn_text')->nullable();
                $table->string('btn_link')->nullable();
            });
        }
        if (!Schema::hasColumn('pg__module_desc', 'menu_text')) {
            Schema::table('pg__module_desc', function(Blueprint $table) {
                $table->string('menu_text')->nullable();
                $table->string('btn_text')->nullable();
                $table->string('btn_link')->nullable();
            });
        }
        if (!Schema::hasColumn('pg__page_module_desc', 'menu_text')) {
            Schema::table('pg__page_module_desc', function(Blueprint $table) {
                $table->string('menu_text')->nullable();
                $table->string('btn_text')->nullable();
                $table->string('btn_link')->nullable();
            });
        }
        if (!Schema::hasColumn('pg__layout_modules', 'menu_text')) {
            Schema::table('pg__layout_modules', function(Blueprint $table) {
                $table->string('menu_text')->nullable();
                $table->string('btn_text')->nullable();
                $table->string('btn_link')->nullable();
            });
        }
        if (!Schema::hasColumn('pg__page_contents', 'menu_text')) {
            Schema::table('pg__page_contents', function(Blueprint $table) {
                $table->string('menu_text')->nullable();
                $table->string('btn_text')->nullable();
                $table->string('btn_link')->nullable();
            });
        }
        if (!Schema::hasColumn('pg__modules', 'menu_text')) {
            Schema::table('pg__modules', function(Blueprint $table) {
                $table->string('menu_text')->nullable();
                $table->string('btn_text')->nullable();
                $table->string('btn_link')->nullable();
            });
        }
        if (!Schema::hasColumn('pg__page_modules', 'menu_text')) {
            Schema::table('pg__page_modules', function(Blueprint $table) {
                $table->string('menu_text')->nullable();
                $table->string('btn_text')->nullable();
                $table->string('btn_link')->nullable();
            });
        }
        if (!Schema::hasColumn('mkt__camp_modules', 'menu_text')) {
            Schema::table('mkt__camp_modules', function(Blueprint $table) {
                $table->string('menu_text')->nullable();
                $table->string('btn_text')->nullable();
                $table->string('btn_link')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}
