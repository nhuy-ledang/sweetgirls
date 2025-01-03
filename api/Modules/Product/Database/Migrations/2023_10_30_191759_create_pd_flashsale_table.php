<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePdFlashsaleTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__flashsales', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->datetime('start_date')->unique();
            $table->datetime('end_date')->unique();
            $table->text('special_ids')->nullable();
            $table->boolean('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('pd__flashsales');
    }
}
