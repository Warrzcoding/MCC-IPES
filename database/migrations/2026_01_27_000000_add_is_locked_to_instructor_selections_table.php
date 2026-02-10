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
        Schema::table('instructor_selections', function (Blueprint $table) {
            // Add is_locked column to track if selection is confirmed and locked
            $table->boolean('is_locked')->default(false)->after('selection_stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructor_selections', function (Blueprint $table) {
            $table->dropColumn('is_locked');
        });
    }
};
