<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->nullable();
            $table->string('pagekey')->nullable();
            $table->string('name')->nullable();
            $table->tinyInteger('display')->nullable();
            $table->dateTime('updatetime')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('get_status')->nullable();
            $table->integer('total')->nullable();
            $table->integer('start')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_categories');
    }
}
