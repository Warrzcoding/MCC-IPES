<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('admin_otp_code')->nullable()->after('remember_token');
            $table->timestamp('admin_otp_expires_at')->nullable()->after('admin_otp_code');
            $table->unsignedTinyInteger('admin_otp_attempts')->default(0)->after('admin_otp_expires_at');
            $table->timestamp('admin_otp_last_sent_at')->nullable()->after('admin_otp_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'admin_otp_code',
                'admin_otp_expires_at',
                'admin_otp_attempts',
                'admin_otp_last_sent_at',
            ]);
        });
    }
};
