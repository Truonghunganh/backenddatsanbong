<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoanhThusTable extends Migration
{
    public function up()
    {
        Schema::create('doanhthus', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idquan')->unsigned();
            $table->bigInteger('doanhthu');
            $table->dateTime('time');
            $table->timestamps();
            $table->foreign('idquan')->references('id')->on('quans');
          
        });
    }
    public function down()
    {
        Schema::dropIfExists('doanhthus');
    }
}
