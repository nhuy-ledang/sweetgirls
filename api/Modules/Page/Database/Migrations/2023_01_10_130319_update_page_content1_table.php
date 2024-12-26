<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class UpdatePageContent1Table extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        DB::unprepared("ALTER TABLE `mkt__camp_modules` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `mkt__camp_module_desc` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pd__category_modules` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pd__category_module_desc` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pd__product_modules` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pd__product_module_desc` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pg__page_contents` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pg__page_content_desc` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pg__modules` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pg__module_desc` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pg__layout_modules` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pg__layout_module_desc` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pg__page_modules` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pg__page_module_desc` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pg__layout_patterns` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
        DB::unprepared("ALTER TABLE `pg__layout_pattern_desc` ADD `sub_title` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}
