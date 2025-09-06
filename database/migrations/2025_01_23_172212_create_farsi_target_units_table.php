<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//The table for Farsi target units

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('farsi_target_units', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID  
            $table->foreignId('source_unit')->constrained('arabic_source_units')->onDelete('cascade'); // Foreign key referencing arabic source units table  
            $table->text('text'); // Text of the target unit 
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farsi_target_units', function (Blueprint $table) {
            $table->dropForeign('source_unit');
        });
        Schema::dropIfExists('farsi_target_units');
    }
};
