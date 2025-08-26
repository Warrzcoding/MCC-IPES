<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcadQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('acad_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->unsignedBigInteger('question_id');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('staff_type', ['teaching', 'non-teaching']);
            $table->string('response_type', 50);
            $table->timestamps();
            
            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('acad_questions');
    }
} 