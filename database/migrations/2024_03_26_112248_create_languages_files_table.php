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
        Schema::create('language_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('languages');
            $table->string('path_file', 255);
            $table->enum('type', ['frontend', 'other']); 
            $table->string('md5_path_file', 32);
            $table->timestamps();
        
            $table->unique(['language_id', 'type'], 'languages_files_language_type_unique');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages_files');
    }
};
