<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoRequestsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sto__requests', function(Blueprint $table) {
            $table->increments('id');
            $table->string('idx', 63)->nullable();
            $table->integer('owner_id')->unsigned();
            $table->integer('stock_id')->unsigned()->nullable();
            $table->string('invoice_id')->nullable();
            $table->enum('type', ['in', 'out']);
            $table->smallInteger('in_type')->nullable();
            $table->smallInteger('shipping_status')->nullable();
            $table->smallInteger('status')->default(0);
            $table->enum('platform', ['website', 'shopee', 'tiktok', 'lazada'])->nullable();
            $table->integer('attach')->nullable();
            $table->string('location')->nullable(); // Gửi/nhận từ khách hàng
            $table->integer('in_stock_id')->unsigned()->nullable();
            $table->smallInteger('out_type')->nullable();
            $table->integer('out_stock_id')->unsigned()->nullable();
            $table->integer('department_id')->unsigned()->nullable(); // Bộ phận yêu cầu
            $table->integer('customer_id')->unsigned()->nullable(); // Bên nhận/bên giao
            //$table->integer('customer_usr_id')->unsigned()->nullable(); // Liên hệ chính
            $table->integer('carrier_id')->unsigned()->nullable(); // Đơn vị giao nhận
            //$table->integer('carrier_supervisor_id')->unsigned()->nullable(); // Phụ trách đơn giao nhận
            //$table->integer('carrier_shipper_id')->unsigned()->nullable(); // Người giao nhận
            $table->integer('storekeeper_id')->unsigned()->nullable(); // Thủ kho
            $table->integer('st_manager_id')->unsigned()->nullable(); // Quản kho
            $table->decimal('total', 15, 0)->default(0);
            $table->string('content')->nullable();
            $table->string('note')->nullable();
            $table->integer('reviewer_id')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->timestamp('estimate_at')->nullable();
            $table->timestamp('reality_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            //$table->foreign('owner_id')->references('id')->on('usrs');
            $table->foreign('stock_id')->references('id')->on('sto__stocks')->onDelete('cascade');
            //$table->foreign('department_id')->references('id')->on('st__departments');
            //$table->foreign('customer_id')->references('id')->on('st__departments'); // Chưa biết bảng nào
            //$table->foreign('carrier_id')->references('id')->on('st__departments'); // Chưa biết bảng nào
            //$table->foreign('storekeeper_id')->references('id')->on('usrs');
            //$table->foreign('st_manager_id')->references('id')->on('usrs');
            //$table->foreign('reviewer_id')->references('id')->on('usrs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sto__requests');
    }
}
