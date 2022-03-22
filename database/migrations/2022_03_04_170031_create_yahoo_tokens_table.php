<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateYahooTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('yahoo_tokens', function (Blueprint $table) {
            $table->id();
            $table->longText('access_token')->nullable();
            $table->longText('refresh_token')->nullable();
            $table->string('state')->nullable();
            $table->string('nonce')->nullable();
            $table->integer('app_id')->nullable();
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
        Schema::dropIfExists('yahoo_tokens');
    }
}
