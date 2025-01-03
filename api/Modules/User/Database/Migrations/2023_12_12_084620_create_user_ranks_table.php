<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user__ranks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191);
            $table->integer('value')->unsigned()->default(0);
            $table->smallInteger('rank')->default(1);
            $table->boolean('status')->default(0);
        });
        DB::unprepared("
          INSERT INTO `user__ranks` (`id`, `name`, `value`, `rank`, `status`) VALUES
            (1, 'NEW MEMBER', 0, 1, 1),
            (2, 'SILVER MEMBER', 5000, 2, 1),
            (3, 'GOLD MEMBER', 15000, 3, 1),
            (4, 'DIAMOND MEMBER', 30000, 4, 1);"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user__ranks');
    }
}
