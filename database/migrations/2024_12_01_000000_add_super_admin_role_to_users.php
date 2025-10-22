<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddSuperAdminRoleToUsers extends Migration
{
    public function up()
    {
        // Add 'super-admin' to the role enum if not exists
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'student', 'super-admin', 'staff') DEFAULT 'student'");
    }

    public function down()
    {
        // Revert back to original enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'student') DEFAULT 'student'");
    }
}