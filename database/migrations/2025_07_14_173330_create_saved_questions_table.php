<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('saved_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_year_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('staff_type');
            $table->string('response_type');
            $table->timestamps();

            $table->foreign('academic_year_id')->references('id')->on('academic_years')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('saved_questions');
    }
}
