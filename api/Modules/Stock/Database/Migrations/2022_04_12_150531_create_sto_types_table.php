<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoTypesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sto__types', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191)->nullable();
            $table->string('colour', 15)->nullable()->default('#000000');
            $table->string('image')->nullable();
            $table->string('description')->nullable();
            $table->string('uses')->nullable();
            $table->smallInteger('sort_order')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });

        DB::unprepared("INSERT INTO `sto__types` (`id`, `name`, `colour`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES(1, 'Tổng hợp', '#32D593', 1, 1, NULL, NULL);");
        DB::unprepared("INSERT INTO `sto__types` (`id`, `name`, `colour`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES(2, 'Lộ thiên', '#3986FF', 1, 1, NULL, NULL);");
        DB::unprepared("INSERT INTO `sto__types` (`id`, `name`, `colour`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES(3, 'Lộ thiên cổng trục', '#FF39BD', 1, 1, NULL, NULL);");
        DB::unprepared("INSERT INTO `sto__types` (`id`, `name`, `colour`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES(4, 'Kho kín', '#FF4A65', 1, 1, NULL, NULL);");
        DB::unprepared("INSERT INTO `sto__types` (`id`, `name`, `colour`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES(5, 'Kín - lạnh', '#29C6E3', 1, 1, NULL, NULL);");
        DB::unprepared("INSERT INTO `sto__types` (`id`, `name`, `colour`, `sort_order`, `status`, `created_at`, `updated_at`) VALUES(6, 'Kín - cầu trục', '#FFC02C', 1, 1, NULL, NULL);");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sto__types');
    }
}
