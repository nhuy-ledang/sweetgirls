<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsrCartalystSentinelTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('usrs', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned()->nullable();
            $table->string('email', 191)->nullable();
            $table->string('calling_code', 7)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('password')->nullable();
            $table->string('first_name', 191)->nullable();
            $table->string('last_name', 191)->nullable();
            $table->string('username', 63)->nullable();
            $table->smallInteger('gender')->default(0);
            $table->date('birthday')->nullable();
            $table->string('address')->nullable();
            $table->string('avatar')->nullable();
            $table->string('avatar_url')->nullable();
            $table->string('status', 20)->default('starter');
            $table->boolean('email_verified')->default(0);
            $table->boolean('phone_verified')->default(0);
            $table->smallInteger('password_failed')->default(0);
            $table->boolean('is_notify')->default(1);
            $table->boolean('is_sms')->default(1);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->string('last_provider', 31)->nullable();
            $table->string('ip', 40)->nullable();
            $table->string('device_platform', 7)->nullable();
            $table->string('device_token')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('usr__activations', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('code', 191);
            $table->boolean('completed')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('usrs')->onDelete('cascade');
        });

        Schema::create('usr__persistences', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('code', 191);
            $table->string('name', 191)->nullable();
            $table->string('model', 40)->nullable();
            $table->string('device_platform', 7)->nullable();
            $table->string('device_token')->nullable();
            $table->string('provider', 31)->nullable();
            $table->string('ip', 40)->nullable();
            $table->timestamps();
            $table->unique('code');

            $table->foreign('user_id')->references('id')->on('usrs')->onDelete('cascade');
        });

        Schema::create('usr__reminders', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('code', 191);
            $table->boolean('completed')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('usrs')->onDelete('cascade');
        });

        Schema::create('usr__roles', function(Blueprint $table) {
            $table->increments('id');
            $table->string('slug', 191);
            $table->string('name', 191);
            $table->text('permissions')->nullable();
            $table->timestamps();
            $table->unique('slug');
        });

        Schema::create('usr__role_users', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('usrs')->onDelete('cascade');
        });

        Schema::create('usr__throttle', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('type', 191);
            $table->string('ip', 40)->nullable();
            $table->timestamps();
            $table->index('user_id');

            $table->foreign('user_id')->references('id')->on('usrs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('usr__activations');
        Schema::drop('usr__persistences');
        Schema::drop('usr__roles');
        Schema::drop('usr__role_users');
        Schema::drop('usr__throttle');
        Schema::drop('usrs');
    }
}
