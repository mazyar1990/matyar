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
        Schema::create('corpora', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID  
            $table->string('name'); // Name of the corpus  
            $table->string('source_lang'); // Source language of the corpus
            $table->string('target_lang'); // Target language of the corpus
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('corpora');
    }
};
