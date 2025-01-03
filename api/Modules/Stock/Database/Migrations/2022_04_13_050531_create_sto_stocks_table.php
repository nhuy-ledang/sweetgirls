<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoStocksTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sto__stocks', function(Blueprint $table) {
            $table->increments('id');
            $table->string('idx', 191)->nullable();
            $table->string('name', 191)->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('type_id')->unsigned()->nullable();
            $table->string('phone_number', 63)->nullable();
            $table->integer('province_id')->unsigned()->nullable();
            $table->integer('district_id')->unsigned()->nullable();
            $table->integer('ward_id')->unsigned()->nullable();
            $table->string('address')->nullable();
            $table->integer('st_manager_id')->unsigned()->nullable();
            $table->string('default_place')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('type_id')->references('id')->on('sto__types')->onDelete('set null');
            $table->foreign('province_id')->references('id')->on('loc__provinces')->onDelete('set null');
            $table->foreign('district_id')->references('id')->on('loc__districts')->onDelete('set null');
            $table->foreign('ward_id')->references('id')->on('loc__wards')->onDelete('set null');
            //$table->foreign('st_manager_id')->references('id')->on('usrs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sto__stocks');
    }
}
