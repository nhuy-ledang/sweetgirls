<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalariesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('st__salaries', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->date('date');
            $table->tinyInteger('date_num')->default(30);
            $table->tinyInteger('date_off')->default(0);
            $table->decimal('salary', 15, 0)->default(0);
            $table->decimal('real', 15, 0)->default(0);
            $table->decimal('debt', 15, 0)->default(0);
            $table->date('salary_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'date']);

            $table->foreign('user_id')->references('id')->on('st__users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('st__salaries');
    }
}
