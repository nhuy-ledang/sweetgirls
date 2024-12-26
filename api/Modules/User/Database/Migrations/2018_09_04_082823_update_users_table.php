<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->smallInteger('prefix')->default(0);
            $table->string('fullname')->nullable();
            $table->string('username', 63)->nullable();
            $table->string('avatar')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('calling_code', 7)->default('84');
            $table->string('phone_number', 20)->nullable();
            $table->smallInteger('gender')->default(0);
            $table->date('birthday')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('status',20)->default('starter');
            $table->boolean('email_verified')->default(0);
            $table->boolean('phone_verified')->default(0);
            $table->smallInteger('password_failed')->default(0);
            $table->bigInteger('spend')->unsigned()->default(0);
            $table->integer('coins')->unsigned()->default(0);
            $table->date('coins_expired')->nullable();
            $table->integer('points')->unsigned()->default(0);
            $table->string('ip', 40)->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->boolean('is_notify')->default(1);
            $table->boolean('is_sms')->default(1);
            $table->string('device_id', 63)->nullable();
            $table->string('device_platform', 7)->nullable();
            $table->string('device_token', 255)->nullable();
            $table->string('last_provider', 31)->nullable();
            $table->string('share_code', 63)->nullable();
            $table->string('id_no', 15)->nullable();
            $table->date('id_date')->nullable();
            $table->string('id_provider', 191)->nullable();
            $table->string('id_address', 191)->nullable();
            $table->string('id_front')->nullable();
            $table->string('id_behind')->nullable();
            $table->string('tax', 191)->nullable();
            $table->string('card_holder', 191)->nullable();
            $table->string('bank_number', 191)->nullable();
            $table->string('bank_name', 191)->nullable();
            $table->integer('bank_id')->unsigned()->nullable();
            $table->string('bank_branch', 191)->nullable();
            $table->string('paypal_number', 191)->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
}
