<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePdGiftOrder240420Table extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('pd__gift_orders', function(Blueprint $table) {
            $table->timestamp('start_date')->nullable()->after('description');
            $table->timestamp('end_date')->nullable()->after('start_date');
            $table->integer('limited')->nullable()->after('end_date');
            $table->integer('uses_total')->unsigned()->default(0)->after('limited');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
    }
}
