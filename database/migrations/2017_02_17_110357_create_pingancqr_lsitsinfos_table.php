<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePingancqrLsitsinfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pingancqr_lsitsinfos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code_number');
            $table->integer('user_id');
            $table->string('user_name');
            $table->integer('code_type');//0空码，1收款码
            $table->string('store_id');
            $table->string('store_name');
            $table->string('from_info');//平安 支付宝 微信
            $table->string('cno');//生成的批次
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
        Schema::dropIfExists('pingancqr_lsitsinfos');
    }
}
