<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeixinPayNotifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weixin_pay_notifies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('store_type');
            $table->string('store_id');
            $table->string('store_name');
            $table->string('template_id');
            $table->string('receiver');
            $table->string('topColor');
            $table->string('linkTo');
            $table->text('data');
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
        Schema::dropIfExists('weixin_pay_notifies');
    }
}
