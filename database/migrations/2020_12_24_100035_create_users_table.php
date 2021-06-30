<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('role');
            $table->boolean('trangthai');
            $table->string('name');
            $table->string('phone')->index();
            $table->string('gmail');
            $table->string('address');
            $table->string('password');
            $table->longText('token')->nullable()->index();
            $table->dateTime('Create_time');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
