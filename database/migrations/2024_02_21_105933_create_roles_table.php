<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id'); 
            $table->string('name', 255)->unique('name');
            $table->string('code', 255)->unique();
            $table->tinyInteger('requestable')->default(0);
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->unsignedBigInteger('organization_address_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}
