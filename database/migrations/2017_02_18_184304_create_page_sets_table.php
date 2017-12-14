<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePageSetsTable extends Migration
{
    /**单页面不确定的字段表
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_sets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('int1');
            $table->integer('int2');
            $table->integer('int3');
            $table->integer('int4');
            $table->integer('int5');
            $table->string('string1');
            $table->string('string2');
            $table->string('string3');
            $table->string('string4');
            $table->string('string5');
            $table->text('text1');
            $table->text('text2');
            $table->text('text3');
            $table->text('text4');
            $table->text('text5');
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
        Schema::dropIfExists('page_sets');
    }
}
