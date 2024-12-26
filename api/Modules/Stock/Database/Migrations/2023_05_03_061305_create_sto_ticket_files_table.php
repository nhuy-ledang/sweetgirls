<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoTicketFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sto__ticket_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id')->unsigned()->nullable();
            $table->integer('ticket_id')->unsigned()->nullable();
            $table->enum('type', ['att', 'cert'])->default('att');
            $table->string('filename')->nullable();
            $table->string('path')->nullable();
            $table->string('extension', 7)->nullable();
            $table->string('mimetype', 15)->nullable();
            $table->decimal('filesize', 15, 0)->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('sto__tickets')->onDelete('cascade');
            //$table->foreign('owner_id')->references('id')->on('usrs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sto__ticket_files');
    }
}
