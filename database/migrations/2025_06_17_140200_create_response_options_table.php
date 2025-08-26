<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResponseOptionsTable extends Migration
{
    public function up()
    {
        Schema::create('response_options', function (Blueprint $table) {
            $table->id();
            $table->string('response_type', 50);
            $table->string('option_value', 100);
            $table->string('option_label', 255);
            $table->integer('option_order')->default(1);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('response_options');
    }
} 