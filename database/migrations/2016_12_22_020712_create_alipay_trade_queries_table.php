<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlipayTradeQueriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alipay_trade_queries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('out_trade_no');
            $table->string('trade_no');
            $table->string('store_id');
            $table->decimal('total_amount',11,2);
            $table->string('status');
            $table->string('type');
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
        Schema::dropIfExists('alipay_trade_queries');
    }
}
