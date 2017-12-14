<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlipayStoreInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alipay_store_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('apply_id');
            $table->string('store_id');//自编门店
            $table->string('shop_id');//支付宝门店id
            $table->string('audit_status');
            $table->string('notify_time');//通知时间
            $table->string('is_show');
            $table->string('request_id');
            $table->string('biz_type');
            $table->string('result_code');
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
        Schema::dropIfExists('alipay_store_infos');
    }
}
