<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupSupplierContactsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sup__supplier_contacts', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('owner_id')->unsigned();
            $table->integer('supplier_id')->unsigned()->nullable();
            $table->integer('type_id')->unsigned()->nullable();
            $table->string('fullname')->nullable();
            $table->string('contact_title')->nullable();
            $table->smallInteger('gender')->default(0);
            $table->string('phone_number', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable();
            $table->date('birthday')->nullable();
            $table->string('interests')->nullable();
            $table->string('personality')->nullable();
            $table->string('contact_tool')->nullable();
            $table->string('contact_time')->nullable();
            $table->string('address')->nullable();
            $table->smallInteger('marital_status')->default(0);
            $table->string('home_town')->nullable();
            $table->string('religion')->nullable();
            $table->string('dreams')->nullable();
            $table->string('favorite_activity')->nullable();
            $table->string('social_achievements')->nullable();
            $table->string('social_groups')->nullable();
            $table->string('social_network')->nullable();
            $table->string('note')->nullable();
            $table->integer('rating_id')->unsigned()->nullable();
            $table->smallInteger('progress')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('sup__suppliers')->onDelete('cascade');
            //$table->foreign('type_id')->references('id')->on('cus__types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sup__supplier_contacts');
    }
}
