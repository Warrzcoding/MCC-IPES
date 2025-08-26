<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id', 20);
            $table->string('full_name', 100);
            $table->string('email', 100);
            $table->string('phone', 15);
            $table->string('department', 50);
            $table->enum('staff_type', ['teaching', 'non-teaching']);
            $table->string('image_path', 255)->nullable();
            $table->string('profile_image', 255)->default('default-staff.png');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff');
    }
}