<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeixinPayConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weixin_pay_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_id');
            $table->string('merchant_id');
            $table->string('key');
            $table->string('cert_path');
            $table->string('key_path');
            $table->string('notify_url');
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
        Schema::dropIfExists('weixin_pay_configs');
    }
}
