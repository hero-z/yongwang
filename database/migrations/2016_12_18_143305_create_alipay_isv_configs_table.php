<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlipayIsvConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alipay_isv_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_id');
            $table->string('pid');
            $table->text('rsaPrivateKey');
            $table->string('rsaPrivateKeyFilePath');
            $table->string('alipayrsaPublicKey');
            $table->string('rsaPublicKeyFilePath');
            $table->string('callback');
            $table->string('operate_notify_url');
            $table->string('notify');
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
        Schema::dropIfExists('alipay_isv_configs');
    }
}
