<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSansTable extends Migration
{
    public function up()
    {
        Schema::create('sans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idquan')->unsigned();
            $table->string('name');
            $table->integer('numberpeople');
            $table->boolean('trangthai');
            $table->bigInteger('priceperhour');
            $table->dateTime('createtime');
            $table->foreign('idquan')->references('id')->on('quans');
          
        });
    }
    public function down()
    {
        Schema::dropIfExists('sans');
    }
}
