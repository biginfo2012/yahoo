<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYahooCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yahoo_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('store_id');
            $table->string('category_code')->nullable();
            $table->longText('category_name')->nullable();
            $table->tinyInteger('display')->nullable();
            $table->tinyInteger('is_leaf')->nullable();
            $table->dateTime('update_date')->nullable();
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
        Schema::dropIfExists('yahoo_categories');
    }
}
