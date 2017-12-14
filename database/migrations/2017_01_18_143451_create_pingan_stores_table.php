<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinganStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pingan_stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_id');
            $table->string('name');
            $table->string('alias_name');
            $table->string('service_phone');
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('contact_mobile');
            $table->string('contact_email');
            $table->string('category_id');
            $table->string('memo');
            $table->string('sub_merchant_id');//移动支付平台为商户分配的惟一ID
            $table->string('bank_card_no');//银行卡卡号
            $table->string('card_holder');//银行卡的开户人姓名
            $table->integer('is_public_account');//该银行卡是否为对公账户，0为否（默认），1为是
            $table->string('open_bank');//对公账户的开户行
            $table->double('merchant_rate',4,3);
            $table->string('status');
            $table->integer('user_id');
            $table->integer('is_delete');
            $table->string('user_name');
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
        Schema::dropIfExists('pingan_stores');
    }
}
