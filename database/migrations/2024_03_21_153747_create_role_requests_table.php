<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('role_id'); 
            $table->unsignedInteger('account_id');
            $table->enum('status', ['waiting', 'denied', 'assigned']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_requests');
    }
};
