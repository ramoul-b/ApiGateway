<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMicroservicesTable extends Migration
{
    public function up()
    {
        Schema::create('microservices', function (Blueprint $table) {
            $table->increments('id'); // Assure un INT UNSIGNED auto-incrémenté
            $table->string('name', 255);
            $table->string('code', 255)->unique();
            $table->string('secret_key', 255);
            $table->string('main_ipv4', 255);
            $table->string('load_balancer_ipv4', 255)->nullable();
            $table->string('main_ipv6', 255);
            $table->string('load_balancer_ipv6', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('microservices');
    }
}
