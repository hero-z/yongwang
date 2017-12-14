<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlipayShopListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('alipay_shop_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_id');
            $table->string('shop_id');
            $table->integer('user_id');
            $table->string('apply_id');
            $table->string('audit_status');
            $table->string('app_auth_token');
            $table->string('category_id');
            $table->string('brand_name');
            $table->string('brand_logo');
            $table->string('main_shop_name');
            $table->string('branch_shop_name');
            $table->string('province_code');
            $table->string('city_code');
            $table->string('district_code');
            $table->string('address');
            $table->double('longitude', 15, 6);
            $table->string('latitude');
            $table->string('contact_number');
            $table->string('notify_mobile');
            $table->string('main_image');
            $table->string('audit_images');
            $table->string('business_time');
            $table->string('wifi');
            $table->string('parking');
            $table->string('value_added');
            $table->string('avg_price');
            $table->string('isv_uid');
            $table->string('licence');
            $table->string('licence_code');
            $table->string('licence_name');
            $table->string('business_certificate');
            $table->string('business_certificate_expires');
            $table->string('auth_letter');
            $table->string('is_operating_online');
            $table->string('online_url');
            $table->string('operate_notify_url');
            $table->string('implement_id');
            $table->string('no_smoking');
            $table->string('box');
            $table->string('request_id');
            $table->string('other_authorization');
            $table->string('licence_expires');
            $table->string('op_role');
            $table->string('biz_version');
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
        Schema::dropIfExists('alipay_shop_lists');
    }
}
