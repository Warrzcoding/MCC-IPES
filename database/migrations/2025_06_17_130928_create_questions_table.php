<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('staff_type', ['teaching', 'non-teaching']);
            $table->string('response_type', 50)->default('rating_5');
            $table->unsignedBigInteger('academic_year_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->boolean('is_open')->default(0);
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}