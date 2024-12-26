<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderInvoiceVatsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('order__invoice_vats', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned();
            $table->string('id_attr', 63);
            $table->string('no')->nullable();
            $table->date('date_release')->nullable();
            $table->string('code_cqt')->nullable();
            $table->string('serial')->nullable();
            $table->string('lookup_code')->nullable();
            $table->string('domain_lookup')->nullable();
            $table->string('history')->nullable();
            $table->string('type')->nullable();
            $table->decimal('vat_amount', 15, 0)->default(0);
            $table->decimal('total', 15, 0)->default(0);
            $table->decimal('amount', 15, 0)->default(0);
            $table->timestamps();
            //$table->unique(['invoice_id', 'id_attr']);

            //$table->foreign('invoice_id')->references('id')->on('ord__invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('ord__invoice_vats');
    }
}
