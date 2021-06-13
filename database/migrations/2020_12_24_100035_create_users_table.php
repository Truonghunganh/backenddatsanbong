<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('role');
            $table->string('name');
            $table->string('phone')->index();
            $table->string('gmail');
            $table->string('address');
            $table->string('password');
            $table->longText('token')->nullable()->index();
            $table->dateTime('createtime');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
