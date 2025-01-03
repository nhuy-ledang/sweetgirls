<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBannedTable extends Migration {
    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        Schema::create('user__banned', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->timestamp('expired_at')->comment('null value means never expired')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::dropIfExists('user__banned');
    }
}
