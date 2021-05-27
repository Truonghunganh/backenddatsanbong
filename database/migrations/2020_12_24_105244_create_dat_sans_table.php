<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDatSansTable extends Migration
{
    public function up()
    {
        Schema::create('datsans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idsan')->unsigned();
            $table->foreign('idsan')->references('id')->on('sans')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('iduser')->unsigned();
            $table->foreign('iduser')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('start_time');
            $table->bigInteger('price');
            $table->boolean('xacnhan');
            $table->dateTime('Create_time');
           
        });
        // Schema::create('datsans', function (Blueprint $table) {
        //     $table->foreign('idsan')->references('id')->on('sans')->onDelete('cascade');
        //     $table->foreign('iduser')->references('id')->on('users')->onDelete('cascade');
        // });
    }

    public function down()
    {
        Schema::dropIfExists('datsans');
    }
}
