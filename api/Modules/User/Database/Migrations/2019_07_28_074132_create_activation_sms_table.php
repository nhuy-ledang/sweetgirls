<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivationSmsTable extends Migration {

    /**
     * Run the migrations.
     * @return void
     */
    public function up() {
        Schema::create('activation_sms', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger("user_id");
            $table->string('code', 20);
            $table->boolean('completed');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down() {
        Schema::drop('activation_sms');
    }

}
