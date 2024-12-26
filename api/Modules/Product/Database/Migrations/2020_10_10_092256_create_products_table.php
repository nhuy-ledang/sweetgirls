<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('pd__products', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('master_id')->unsigned()->nullable();
            $table->integer('category_id')->unsigned()->nullable();
            $table->integer('manufacturer_id')->unsigned()->nullable();
            $table->string('translates', 63)->nullable();
            $table->string('name');
            $table->string('long_name')->nullable();
            $table->string('model', 63)->nullable();
            $table->smallInteger('num_of_child')->unsigned()->default(0);
            $table->decimal('price', 15, 0)->default(0);
            $table->boolean('is_gift')->default(0);
            $table->boolean('is_coin_exchange')->default(0);
            $table->boolean('is_free')->default(0);
            $table->boolean('is_included')->default(0);
            $table->boolean('no_cod')->default(0);
            $table->integer('coins')->unsigned()->default(0);
            $table->decimal('weight', 15, 0)->default(0);
            $table->decimal('length', 15, 0)->default(0);
            $table->decimal('width', 15, 0)->default(0);
            $table->decimal('height', 15, 0)->default(0);
            $table->decimal('price_min', 15, 0)->default(0);
            $table->decimal('price_max', 15, 0)->default(0);
            $table->integer('gift_set_id')->unsigned()->nullable();
            $table->string('image')->nullable();
            $table->string('image_alt')->nullable();
            $table->string('banner')->nullable();
            $table->boolean('top')->default(0);
            $table->smallInteger('viewed')->default(0);
            $table->smallInteger('sort_order')->default(1);
            $table->boolean('status')->default(0);
            $table->string('alias')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keyword')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->text('tag')->nullable();
            $table->string('link')->nullable();
            $table->text('properties')->nullable();
            $table->text('user_guide')->nullable();
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
        Schema::dropIfExists('pd__products');
    }
}
