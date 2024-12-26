<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusProductsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('bus__products', function(Blueprint $table) {
            $table->increments('id');
            $table->string('idx', 191)->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('supplier_id')->unsigned()->nullable();
            $table->string('name', 191);
            $table->smallInteger('prd_type')->default(0); // 0-Sản phẩm sản xuất, 2-Sản phẩm nhập, 1-Dịch vụ tự thực hiện, 3-Dịch vụ thuê ngoài
            $table->decimal('price_im', 15, 0)->default(0);
            $table->decimal('pretax', 15, 0)->default(0);
            $table->smallInteger('vat')->unsigned()->default(0);
            $table->decimal('price', 15, 0)->default(0);
            $table->string('unit', 191)->nullable();
            $table->decimal('weight', 15, 0)->default(0);
            $table->decimal('length', 15, 0)->default(0);
            $table->decimal('width', 15, 0)->default(0);
            $table->decimal('height', 15, 0)->default(0);
            $table->integer('appraiser_id')->unsigned()->nullable();
            $table->integer('approver_id')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('status')->default(true);
            $table->string('image')->nullable();
            $table->string('short_description')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            //$table->foreign('category_id')->references('id')->on('bus__categories')->onDelete('cascade');
            //$table->foreign('supplier_id')->references('id')->on('sup__suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('bus__products');
    }
}
