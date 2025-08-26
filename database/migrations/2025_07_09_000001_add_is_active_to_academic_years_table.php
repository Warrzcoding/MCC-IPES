<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->boolean('is_active')->default(false);
            $table->dateTime('open_at')->nullable()->after('is_active');
            $table->dateTime('close_at')->nullable()->after('open_at');
        });
    }

    public function down()
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn('is_active');
            $table->dropColumn(['open_at', 'close_at']);
        });
    }
}; 