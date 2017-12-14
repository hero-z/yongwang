<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelfServiceShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('self_service_shops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_id');//店铺id
            $table->integer('user_id');//推广员id
            $table->string('brand_name');//品牌名
            $table->string('brand_logo');//品牌logo
            $table->string('main_shop_name');//门店名称
            $table->string('branch_shop_name');//分店名称
            $table->string('province_code');//省
            $table->string('city_code');//市
            $table->string('district_code');//区编码
            $table->string('address');//详细地址
            $table->string('contact_number');//门店电话号码
            $table->string('main_image');//门店首图
            $table->string('category_name');//经营品类
            $table->string('contact_name');//主要联系人
            $table->string('audit_images1');//门头照
            $table->string('audit_images2');//内景照1
            $table->string('audit_images3');//内景照2
            $table->string('licence');//营业执照
            $table->string('business_certificate');//许可证
            $table->string('auth_letter');//门店授权函
            $table->string('other_authorization');//其他资料
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
        Schema::dropIfExists('self_service_shops');
    }
}
