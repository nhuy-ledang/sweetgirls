<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoInventoriesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sto__inventories', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('stock_id')->unsigned();
            $table->integer('owner_id')->unsigned();
            $table->date('date')->nullable();
            $table->string('note')->nullable();
            $table->enum('status', ['new', 'approved', 'rejected'])->default('new');
            $table->integer('reviewer_id')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('stock_id')->references('id')->on('sto__stocks')->onDelete('cascade');
            //$table->foreign('owner_id')->references('id')->on('usrs')->onDelete('cascade');
            //$table->foreign('reviewer_id')->references('id')->on('usrs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sto__inventories');
    }
}
