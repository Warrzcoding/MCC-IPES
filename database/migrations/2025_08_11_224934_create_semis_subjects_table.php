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
        Schema::create('semis_subjects', function (Blueprint $table) {
            $table->id();
            $table->enum('semester', ['1', '2'])->default('1');
            $table->enum('department', ['BSIT', 'BSBA', 'BSHM', 'BSED', 'BEED']);
            $table->enum('year', ['1ST YEAR', '2ND YEAR', '3RD YEAR', '4TH YEAR']);
            $table->string('subcode', 20);
            $table->string('subname', 255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semis_subjects');
    }
};
