<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreateStoTicketsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sto__tickets', function(Blueprint $table) {
            $table->increments('id');
            $table->string('idx', 63)->nullable();
            $table->integer('request_id')->unsigned();
            $table->integer('stock_id')->unsigned();
            $table->integer('owner_id')->unsigned();
            $table->enum('type', ['in', 'out']);
            $table->smallInteger('status')->default(0);
            $table->string('note')->nullable();
            $table->integer('reviewer_id')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('stock_id')->references('id')->on('sto__stocks')->onDelete('cascade');
            //$table->foreign('request_id')->references('id')->on('sto__requests')->onDelete('cascade');
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
        Schema::dropIfExists('sto__tickets');
    }
}
