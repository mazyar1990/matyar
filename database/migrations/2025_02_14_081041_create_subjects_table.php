<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id(); // Primary key (auto-incrementing)
            $table->string('name')->unique(); // Subject name (e.g., "Medical," "Legal," "Technical") - must be unique
            $table->text('description')->nullable(); // Optional description
            $table->timestamps(); // Created_at and updated_at timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};