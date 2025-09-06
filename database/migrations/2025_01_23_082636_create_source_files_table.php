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
        Schema::create('source_files', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID  
            $table->string('name'); // File name  
            $table->foreignId('owner')->constrained('users')->onDelete('cascade'); // Reference to users table
            $table->string('type'); // File type (e.g., pdf, doc, docx, txt, ...)
            $table->string('lang'); // For example ar, fa, en, etc
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('source_files');
    }
};
