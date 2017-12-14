<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlipayWeixinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alipay_weixins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alipay_user_id');
            $table->string('alipay_auth_shop_name');
            $table->string('promoter_id');
            $table->string('alipay_app_auth_token');
            $table->string('weixin_mch_id');
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
        Schema::dropIfExists('alipay_weixins');
    }
}
