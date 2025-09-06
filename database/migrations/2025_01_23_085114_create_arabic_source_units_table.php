<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//The table for Arabic Source Units

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('arabic_source_units', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID  
            $table->foreignId('source_file')->constrained('source_files')->onDelete('cascade'); // Foreign key referencing source_files table  
            $table->text('text'); // Text of the source unit  
            $table->string('type'); // Type of text (e.g., title, sentence, table cell)  
            $table->string('hash'); // Hash of the text for quick searching  
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arabic_source_units', function (Blueprint $table) {
            $table->dropForeign('source_file');
        });
        Schema::dropIfExists('arabic_source_units');
    }
};
