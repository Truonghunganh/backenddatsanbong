<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSansTable extends Migration
{
    public function up()
    {
        Schema::create('sans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idquan')->unsigned();
            $table->string('name');
            $table->integer('numberpeople');
            $table->boolean('trangthai');
            $table->bigInteger('priceperhour');
            $table->dateTime('Create_time');
            $table->foreign('idquan')->references('id')->on('quans');
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('sans');
    }
}
