<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationsTable extends Migration
{
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('question_id');
            $table->string('response', 100)->comment('Stores option_value from response_options');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->text('comments')->nullable();
            $table->integer('response_score')->default(0)->comment('Stores option_order for calculations');
        });
    }

    public function down()
    { 
        Schema::dropIfExists('evaluations');
    }
}