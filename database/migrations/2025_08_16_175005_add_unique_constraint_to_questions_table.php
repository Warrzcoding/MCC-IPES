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
        Schema::table('questions', function (Blueprint $table) {
            // Add unique constraint on title, description, and academic_year_id combination
            $table->unique(['title', 'description', 'academic_year_id'], 'questions_title_description_academic_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('questions_title_description_academic_year_unique');
        });
    }
};
