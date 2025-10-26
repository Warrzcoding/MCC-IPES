<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('super_admins')->update([
            'accesscode' => Hash::make('super20202024', ['algorithm' => 'argon2id']),
        ]);
    }

    public function down(): void
    {
        DB::table('super_admins')->update([
            'accesscode' => null,
        ]);
    }
};
