<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoreLanguagesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::dropIfExists('language');
        Schema::create('core__languages', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 32);
            $table->string('code', 4);
            $table->string('filename', 5);
            $table->string('locale');
            $table->string('image')->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('status')->default(1);
        });
        DB::unprepared("INSERT INTO `core__languages` (`name`, `code`, `filename`, `locale`, `image`, `sort_order`, `status`) VALUES ('Tiếng việt', 'vi', 'vi-vn', 'vi_VN.UTF-8,vi_VN,vi-vn,vietnamese', 'vn.svg', 1, 1);");
        DB::unprepared("INSERT INTO `core__languages` (`name`, `code`, `filename`, `locale`, `image`, `sort_order`, `status`) VALUES ('English', 'en', 'en-gb', 'en_US.UTF-8,en_US,en-gb,english', 'en.svg', 2, 1);");
        DB::unprepared("INSERT INTO `core__languages` (`name`, `code`, `filename`, `locale`, `image`, `sort_order`, `status`) VALUES ('日語', 'jp', 'jp-jp', 'jp_JP.UTF-8,japanese', 'jp.svg', 3, 0);");
        DB::unprepared("INSERT INTO `core__languages` (`name`, `code`, `filename`, `locale`, `image`, `sort_order`, `status`) VALUES ('中文', 'cn', 'zh-cn', 'zh,zh-hk,zh-cn,zh-cn.UTF-8,cn-gb,chinese', 'cn.svg', 4, 0);");
        DB::unprepared("INSERT INTO `core__languages` (`name`, `code`, `filename`, `locale`, `image`, `sort_order`, `status`) VALUES ('朝鮮語', 'ko', 'ko-kr', 'ko-KR', 'ko.svg', 5, 0);");
        DB::unprepared("INSERT INTO `core__languages` (`name`, `code`, `filename`, `locale`, `image`, `sort_order`, `status`) VALUES ('Indonesian', 'id', 'id-id', 'id-ID', 'id.svg', 6, 0);");
        DB::unprepared("INSERT INTO `core__languages` (`name`, `code`, `filename`, `locale`, `image`, `sort_order`, `status`) VALUES ('Cambodia', 'km', 'km-kh', 'km-KH', 'km.svg', 7, 0);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('core__languages');
    }
}
