<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusPromosTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('bus__promos', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned()->nullable();
            $table->integer('staff_id')->unsigned()->nullable();
            $table->string('name', 191);
            $table->string('code', 10)->unique();
            $table->char('type', 1);
            $table->decimal('discount', 15, 0)->default(0);
            $table->decimal('total', 15, 0)->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('uses_total')->unsigned()->default(0);
            $table->integer('uses_customer')->unsigned()->default(0);
            $table->text('description')->nullable();
            $table->string('customer')->nullable();
            $table->text('guide')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamp('approve_at')->nullable();
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
        Schema::dropIfExists('bus__promos');
    }
}
