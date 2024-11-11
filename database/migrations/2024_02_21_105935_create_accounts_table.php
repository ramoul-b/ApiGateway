<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id'); 
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');
            $table->tinyInteger('default');
            $table->unsignedBigInteger('anagrafica_id')->nullable(); 
            $table->unsignedBigInteger('anagrafica_address_id')->nullable();
            $table->tinyInteger('using')->index('using');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}

