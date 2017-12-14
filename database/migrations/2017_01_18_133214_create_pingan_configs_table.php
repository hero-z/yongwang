<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinganConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pingan_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('app_id');
            $table->text('rsaPrivateKey');
            $table->text('pinganrsaPublicKey');
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
        Schema::dropIfExists('pingan_configs');
    }
}
