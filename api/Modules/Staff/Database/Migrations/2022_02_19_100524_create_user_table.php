<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('st__users', function(Blueprint $table) {
            $table->increments('id');
            $table->string('idx', 191)->nullable();
            $table->integer('department_id')->unsigned()->nullable();
            //$table->integer('usr_id')->unsigned()->nullable();
            $table->string('fullname', 191)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('calling_code', 7)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->smallInteger('gender')->default(0);
            $table->date('birthday')->nullable();
            $table->string('address')->nullable();
            $table->string('fixed_address')->nullable();
            $table->string('bank_id')->nullable();
            $table->string('bank_name')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('position', 191)->nullable();
            $table->string('mission', 191)->nullable();
            $table->string('description')->nullable();
            $table->string('avatar')->nullable();
            $table->decimal('salary', 15, 0)->default(0);
            $table->decimal('real', 15, 0)->default(0);
            $table->enum('method', ['full-time', 'part-time', 'freelancer'])->nullable();
            $table->smallInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('st__users');
    }
}
