<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicYearSeeder extends Seeder
{
    public function run()
    {
        DB::table('academic_years')->insert([
            'id' => 1,
            'year' => '2025-2026',
            'created_at' => '2025-06-01 16:48:50'
        ]);
    }
} 