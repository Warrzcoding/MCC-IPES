<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Modify enum to include jo and cos if column exists
        if (Schema::hasColumn('staff', 'status')) {
            Schema::table('staff', function (Blueprint $table) {
                // Change enum options: full-time, part-time, jo, cos
                $table->enum('status', ['full-time', 'part-time', 'jo', 'cos'])->default('full-time')->change();
            });
        }
    }

    public function down(): void
    {
        // Revert enum back to original two values
        if (Schema::hasColumn('staff', 'status')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->enum('status', ['full-time', 'part-time'])->default('full-time')->change();
            });
        }
    }
};