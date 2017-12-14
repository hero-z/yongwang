<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlipayAppOauthUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alipay_app_oauth_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id');
            $table->integer('promoter_id');
            $table->string('auth_shop_name');
            $table->string('auth_phone');
            $table->string('auth_app_id');
            $table->string('app_auth_token');
            $table->string('app_refresh_token');
            $table->string('expires_in');
            $table->string('re_expires_in');
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
        Schema::dropIfExists('alipay_app_oauth_users');
    }
}
