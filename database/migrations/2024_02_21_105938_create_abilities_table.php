<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbilitiesTable extends Migration
{
    public function up()
    {
        Schema::create('abilities', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('role_id')->unsigned();
            $table->integer('permission_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('permission_id')->references('id')->on('permissions');
        });
    }

    public function down()
    {
        Schema::dropIfExists('abilities');
    }
}

