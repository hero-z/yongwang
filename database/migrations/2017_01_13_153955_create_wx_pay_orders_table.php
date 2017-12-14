<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWxPayOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wx_pay_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mch_id');//商户号
            $table->string('out_trade_no');//订单号
            $table->string('transaction_id');//微信订单号
            $table->decimal('total_fee',11,2);//金额
            $table->string('open_id');//付款人id
            $table->string('status');//状态
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
        Schema::dropIfExists('wx_pay_orders');
    }
}
