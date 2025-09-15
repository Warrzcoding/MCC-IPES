<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add status column if it doesn't exist
        if (!Schema::hasColumn('staff', 'status')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->enum('status', ['full-time', 'part-time'])->default('full-time')->after('email');
            });
        }

        // Drop phone column if it exists
        if (Schema::hasColumn('staff', 'phone')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }

    public function down(): void
    {
        // Recreate phone column
        if (!Schema::hasColumn('staff', 'phone')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->string('phone', 15)->nullable()->after('email');
            });
        }

        // Drop status column
        if (Schema::hasColumn('staff', 'status')) {
            Schema::table('staff', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};