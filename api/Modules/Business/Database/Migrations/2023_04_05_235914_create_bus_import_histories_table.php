<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusImportHistoriesTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('bus__import_histories', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->string('idx_im', 191)->nullable();
            $table->integer('supplier_id')->unsigned()->nullable();
            $table->decimal('price_im', 15, 0)->default(0);
            $table->decimal('operating_costs', 15, 0)->default(0);
            $table->decimal('expected_profit', 5, 2)->unsigned()->default(0);
            $table->smallInteger('quantity')->default(0);
            $table->decimal('earning_ratio', 5, 2)->unsigned()->default(0);
            $table->decimal('pretax', 15, 0)->default(0);
            $table->smallInteger('vat')->unsigned()->default(0);
            $table->decimal('price', 15, 0)->default(0);
            $table->integer('appraiser_id')->unsigned()->nullable();
            $table->integer('approver_id')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->smallInteger('status')->default(0); // 0-Đang tính giá, 1-Đã tính xong, 2-Hiệu lực phát hành,3-Ngưng phát hành
            $table->string('note')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('bus__products')->onDelete('cascade');
            //$table->foreign('supplier_id')->references('id')->on('sup__suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('bus__import_histories');
    }
}
