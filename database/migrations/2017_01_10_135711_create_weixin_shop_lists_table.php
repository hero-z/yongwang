<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeixinShopListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weixin_shop_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_id');
            $table->string('store_name');
            $table->string('shop_id');
            $table->integer('user_id');
            $table->string('app_id');
            $table->string('mch_id');
            $table->string('device_info');
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
        Schema::dropIfExists('weixin_shop_lists');
    }
}
