<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderOrdersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('orders', function(Blueprint $table) {
            $table->increments('id');
            $table->string('idx', 31)->nullable()->unique();
            $table->integer('master_id')->unsigned()->nullable();
            $table->integer('invoice_no')->unsigned()->nullable();
            $table->string('invoice_prefix', 26)->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('user_group_id')->unsigned()->nullable();
            $table->integer('usr_id')->unsigned()->nullable();
            $table->integer('pusr_id')->unsigned()->nullable();
            $table->string('first_name', 32)->nullable();
            $table->string('last_name', 32)->nullable();
            $table->smallInteger('gender')->default(0);
            $table->string('email', 96)->nullable();
            $table->string('phone_number', 32)->nullable();
            $table->string('telephone', 32)->nullable();
            $table->string('fax', 32)->nullable();
            $table->boolean('is_invoice')->default(0);
            $table->boolean('invoiced')->default(0);
            $table->string('company', 191)->nullable();
            $table->string('company_tax', 191)->nullable();
            $table->string('company_email', 96)->nullable();
            $table->string('company_address', 191)->nullable();
            $table->string('payment_method', 128)->nullable();
            $table->string('payment_code', 128)->nullable();
            $table->string('shipping_method', 128)->nullable();
            $table->string('shipping_time', 64)->nullable();
            $table->string('shipping_code', 128)->nullable();
            $table->string('shipping_first_name', 128)->nullable();
            $table->string('shipping_last_name', 128)->nullable();
            $table->string('shipping_company', 128)->nullable();
            $table->string('shipping_address_1', 255)->nullable();
            $table->string('shipping_address_2', 255)->nullable();
            $table->string('shipping_phone_number', 32)->nullable();
            $table->string('shipping_city', 128)->nullable();
            $table->string('shipping_country', 128)->nullable();
            $table->integer('shipping_country_id')->unsigned()->nullable();
            $table->string('shipping_province', 128)->nullable();
            $table->integer('shipping_province_id')->unsigned()->nullable();
            $table->string('shipping_district', 128)->nullable();
            $table->integer('shipping_district_id')->unsigned()->nullable();
            $table->string('shipping_ward', 128)->nullable();
            $table->integer('shipping_ward_id')->unsigned()->nullable();
            $table->timestamp('shipping_at')->nullable();
            $table->string('note')->nullable();
            $table->string('tags')->nullable();
            $table->text('comment')->nullable();
            $table->integer('coins')->unsigned()->default(0);
            $table->decimal('sub_total', 15, 0)->default(0);
            $table->string('discount_code', 10)->nullable();
            $table->decimal('discount_total', 15, 0)->default(0);
            $table->string('voucher_code', 10)->nullable();
            $table->decimal('voucher_total', 15, 0)->default(0);
            $table->decimal('shipping_fee', 15, 0)->default(0);
            $table->decimal('shipping_discount', 15, 0)->default(0);
            $table->decimal('vat', 15, 0)->default(0);
            $table->decimal('total', 15, 0)->default(0);
            $table->integer('total_coins')->unsigned()->default(0);
            $table->enum('status', ['pending', 'in_process', 'completed', 'failed', 'unknown', 'paid', 'refunded', 'canceled'])->nullable(); // Will remove
            $table->enum('order_status', ['pending', 'processing', 'shipping', 'completed', 'canceled', 'returning', 'returned'])->default('pending');
            $table->enum('payment_status', ['pending', 'in_process', 'paid', 'failed', 'unknown', 'refunded', 'canceled'])->nullable();
            $table->enum('shipping_status', ['create_order', 'delivering', 'delivered', 'return'])->nullable();
            $table->integer('sto_request_id')->unsigned()->nullable();
            $table->string('channel', 31)->default('online');
            $table->string('reason', 255)->nullable();
            $table->integer('affiliate_id')->unsigned()->nullable();
            $table->string('tracking', 63)->nullable();
            $table->string('lang', 4)->default('vi');
            $table->string('currency_code', 8)->nullable();
            $table->string('summary', 255)->nullable();
            $table->string('transaction_no', 255)->nullable();
            $table->string('response_code', 31)->nullable();
            $table->text('payload')->nullable();
            $table->string('ip', 40)->nullable();
            $table->string('referral_code', 20)->nullable();
            $table->string('forwarded_ip', 40)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('accept_language', 255)->nullable();
            $table->timestamp('payment_at')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
