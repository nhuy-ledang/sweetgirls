<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupSupplierNotesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sup__supplier_notes', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('supplier_id')->unsigned();
            $table->integer('owner_id')->unsigned();
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('sup__suppliers')->onDelete('cascade');
            //$table->foreign('owner_id')->references('id')->on('usrs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sup__supplier_notes');
    }
}
