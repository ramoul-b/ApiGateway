<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id'); // Assurez-vous que c'est un entier auto-incrémenté et unsigned
            $table->string('name', 255);
            $table->string('code', 255);
            $table->unsignedInteger('permission_category_id'); // Correspond maintenant à 'increments' de permission_categories
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('permission_category_id')->references('id')->on('permission_categories');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
