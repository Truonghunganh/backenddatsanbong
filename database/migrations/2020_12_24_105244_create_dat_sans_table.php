<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// $table->foreign('idsan')->references('id')->on('sans')->onUpdate('cascade')->onDelete('cascade');
            
class CreateDatSansTable extends Migration
{
    public function up()
    {
        Schema::create('datsans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('idsan')->unsigned();
            $table->foreign('idsan')->references('id')->on('sans');
            $table->bigInteger('iduser')->unsigned();
            $table->foreign('iduser')->references('id')->on('users');
            $table->dateTime('start_time');
            $table->bigInteger('price');
            $table->boolean('xacnhan');
            $table->dateTime('Create_time');
            $table->timestamps();
            
           
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
