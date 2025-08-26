<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaffSeeder extends Seeder
{
    public function run()
    {
        DB::table('staff')->insert([
            [
                'id' => 8,
                'staff_id' => 'TH0282900',
                'full_name' => 'KC JOY Veliganilao',
                'email' => 'kc@gmail.com',
                'phone' => '09502337793',
                'department' => 'Registrar',
                'staff_type' => 'non-teaching',
                'image_path' => 'uploads/staff/TH0282900_1749054368.jpg',
                'profile_image' => 'default-staff.png',
                'created_at' => '2025-06-04 16:26:08',
                'updated_at' => '2025-06-04 16:26:08',
            ],
            [
                'id' => 9,
                'staff_id' => 'dsds',
                'full_name' => 'staf2 dwewe',
                'email' => 'staff@gmail.com',
                'phone' => '43434343434',
                'department' => 'BEED',
                'staff_type' => 'teaching',
                'image_path' => 'uploads/staff/dsds_1749087544.png',
                'profile_image' => 'default-staff.png',
                'created_at' => '2025-06-05 01:39:04',
                'updated_at' => '2025-06-05 01:39:04',
            ],
        ]);
    }
}