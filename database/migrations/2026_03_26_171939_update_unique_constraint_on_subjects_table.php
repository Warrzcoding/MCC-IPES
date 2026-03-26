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
        Schema::table('subjects', function (Blueprint $table) {
            // Drop the old unique constraint that only checked sub_code and section
            $table->dropUnique('unique_sub_code_section');
            
            // Add a new unique constraint including the semester
            $table->unique(['sub_code', 'section', 'semester'], 'unique_sub_code_section_semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Revert the unique constraint changes
            $table->dropUnique('unique_sub_code_section_semester');
            $table->unique(['sub_code', 'section'], 'unique_sub_code_section');
        });
    }
};
