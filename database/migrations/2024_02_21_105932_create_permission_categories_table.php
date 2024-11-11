<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('permission_categories', function (Blueprint $table) {
            $table->increments('id'); 
            $table->string('name', 255);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permission_categories');
    }
}
