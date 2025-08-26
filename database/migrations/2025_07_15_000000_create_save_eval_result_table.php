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
        Schema::create('save_eval_result', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('question_id');
            $table->string('response', 100);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->text('comments')->nullable();
            $table->integer('response_score')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('save_eval_result');
    }
}; 