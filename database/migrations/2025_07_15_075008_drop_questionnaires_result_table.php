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
        Schema::dropIfExists('save_questionnaires_result');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('save_questionnaires_result', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('staff_type', ['teaching', 'non-teaching']);
            $table->string('response_type', 50)->default('rating_5');
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->timestamps();
        });
    }
};
