<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApisTable extends Migration
{
    public function up()
    {
        Schema::create('apis', function (Blueprint $table) {
            $table->increments('id'); 
            $table->unsignedInteger('microservice_id'); 
            $table->string('route_in', 255);
            $table->string('method', 255);
            $table->timestamps();
            $table->foreign('microservice_id')->references('id')->on('microservices');
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('apis');
    }
}
