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
        Schema::table('source_files', function (Blueprint $table) {
            // Add the project_id column as a foreign key referencing the projects table.
            $table->unsignedBigInteger('project_id')->after('owner');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->index('project_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('source_files', function (Blueprint $table) {
            $table->dropForeign(['project_id']); // Important: Drop the foreign key constraint first
            $table->dropColumn('project_id');
        });
    }
};