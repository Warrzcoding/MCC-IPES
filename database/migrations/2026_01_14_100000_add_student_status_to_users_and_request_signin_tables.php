<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentStatusToUsersAndRequestSigninTables extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'student_status')) {
                $table->enum('student_status', ['Regular', 'Irregular'])->nullable();
            }
        });

        Schema::table('request_signin', function (Blueprint $table) {
            if (!Schema::hasColumn('request_signin', 'student_status')) {
                $table->enum('student_status', ['Regular', 'Irregular'])->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'student_status')) {
                $table->dropColumn('student_status');
            }
        });

        Schema::table('request_signin', function (Blueprint $table) {
            if (Schema::hasColumn('request_signin', 'student_status')) {
                $table->dropColumn('student_status');
            }
        });
    }
}
