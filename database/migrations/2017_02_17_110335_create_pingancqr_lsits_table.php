<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePingancqrLsitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pingancqr_lsits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cno');
            $table->integer('user_id');
            $table->integer('user_name');
            $table->string('from_info');
            $table->integer('num');
            $table->integer('s_num');
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
        Schema::dropIfExists('pingancqr_lsits');
    }
}
