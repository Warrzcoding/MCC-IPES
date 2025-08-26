<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('full_name');
            $table->string('school_id')->unique()->nullable();
            $table->enum('role', ['admin', 'student'])->default('student');
            $table->string('profile_image')->nullable();
            $table->string('course')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->boolean('is_main_admin')->default(false);
            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
} 