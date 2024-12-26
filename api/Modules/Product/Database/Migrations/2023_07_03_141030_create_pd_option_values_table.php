<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdOptionValuesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__option_values', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('option_id')->unsigned();
            $table->string('name');
            $table->string('value')->nullable();
            $table->smallInteger('sort_order')->default(1);

            $table->foreign('option_id')->references('id')->on('pd__options')->onDelete('cascade');
        });

       DB::unprepared("
INSERT INTO `pd__options` (`id`, `name`, `type`, `sort_order`) VALUES
(1, 'Thể tích', 'vol', 1),
(2, 'Màu sắc', 'color', 2);
INSERT INTO `pd__option_values` (`id`, `option_id`, `name`, `value`, `sort_order`) VALUES
(1, 1, '200ml', NULL, 1),
(2, 1, '500ml', NULL, 2),
(3, 2, 'Màu xanh', NULL, 1),
(4, 2, 'Màu đỏ', NULL, 2);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__option_values');
    }
}
