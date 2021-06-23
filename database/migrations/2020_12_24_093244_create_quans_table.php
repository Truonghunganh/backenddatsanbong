<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuansTable extends Migration
{
    public function up()
    {
        Schema::create('quans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('image');
            $table->string('address');
            $table->string('phone');
            $table->longText('linkaddress')->nullable();
            $table->double('vido');
            $table->double('kinhdo');
            $table->boolean('trangthai');
            $table->dateTime('Create_time');
            $table->double('review');
            $table->timestamps();
            
            
        });
     }

    public function down()
    {
        Schema::dropIfExists('quans');
    }
}
