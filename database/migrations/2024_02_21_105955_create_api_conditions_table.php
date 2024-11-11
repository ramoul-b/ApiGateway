<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiConditionsTable extends Migration
{
    public function up()
    {
        Schema::create('api_conditions', function (Blueprint $table) {
            $table->id(); 
            $table->integer('api_id')->constrained()->onDelete('cascade');
            $table->integer('permission_id')->constrained()->onDelete('cascade');
            $table->enum('condition', ['AND', 'OR'])->nullable();
            $table->enum('condition_object', ['hasPermission', 'hasNotPermission']);
            $table->enum('condition_level', ['AND', 'OR'])->nullable();
            $table->integer('level')->default(0);
            $table->integer('position_level')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('api_conditions');
    }
}
