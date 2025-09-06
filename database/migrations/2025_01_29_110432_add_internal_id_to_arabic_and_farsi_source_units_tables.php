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
        Schema::table('arabic_source_units', function (Blueprint $table) {
            $table->integer('internal_id')->after('type');
        });
        Schema::table('farsi_source_units', function (Blueprint $table) {
            $table->integer('internal_id')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arabic_source_units', function (Blueprint $table) {
            $table->dropColumn('internal_id');
        });
        Schema::table('farsi_source_units', function (Blueprint $table) {
            $table->dropColumn('internal_id');
        });
    }
};
