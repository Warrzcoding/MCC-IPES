<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionsSeeder extends Seeder
{
    public function run()
    {
        DB::table('questions')->update(['is_open' => 0]);
        DB::table('questions')->insert([
            [
                'id' => 1,
                'title' => 'Teaching Effectiveness',
                'description' => 'Rate the overall teaching effectiveness of the instructor',
                'staff_type' => 'teaching',
                'response_type' => 'rating_5',
                'academic_year_id' => 1,
                'created_at' => '2025-06-05 00:31:48',
                'is_open' => 1,
            ],
            [
                'id' => 2,
                'title' => 'Communication Skills',
                'description' => 'How well does the instructor communicate concepts?',
                'staff_type' => 'teaching',
                'response_type' => 'rating_5',
                'academic_year_id' => 1,
                'created_at' => '2025-06-05 00:31:48',
                'is_open' => 1,
            ],
            [
                'id' => 3,
                'title' => 'Punctuality',
                'description' => 'How often is the instructor punctual?',
                'staff_type' => 'teaching',
                'response_type' => 'frequency_4',
                'academic_year_id' => 1,
                'created_at' => '2025-06-05 00:31:48',
                'is_open' => 1,
            ],
            [
                'id' => 4,
                'title' => 'Service Quality',
                'description' => 'Rate the quality of service provided',
                'staff_type' => 'non-teaching',
                'response_type' => 'rating_5',
                'academic_year_id' => 1,
                'created_at' => '2025-06-05 00:31:48',
                'is_open' => 1,
            ],
            [
                'id' => 5,
                'title' => 'Helpfulness',
                'description' => 'How helpful is this staff member?',
                'staff_type' => 'non-teaching',
                'response_type' => 'rating_5',
                'academic_year_id' => 1,
                'created_at' => '2025-06-05 00:31:48',
                'is_open' => 1,
            ],
            [
                'id' => 6,
                'title' => 'Professional Behavior',
                'description' => 'Staff maintains professional behavior',
                'staff_type' => 'non-teaching',
                'response_type' => 'agreement_5',
                'academic_year_id' => 1,
                'created_at' => '2025-06-05 00:31:48',
                'is_open' => 1,
            ],
            [
                'id' => 7,
                'title' => 'Hdhh',
                'description' => 'Bhb',
                'staff_type' => 'teaching',
                'response_type' => 'frequency_4',
                'academic_year_id' => 1,
                'created_at' => '2025-06-05 01:04:43',
                'is_open' => 1,
            ],
            [
                'id' => 8,
                'title' => 'rATE THE EEFECTIVESNESS OF',
                'description' => 'WHY DSJDJjdjisdjksdjksdsd',
                'staff_type' => 'teaching',
                'response_type' => 'rating_5',
                'academic_year_id' => 1,
                'created_at' => '2025-06-06 10:34:08',
                'is_open' => 1,
            ],
            [
                'id' => 9,
                'title' => 'attendance',
                'description' => 'always ba mosulod si marfa sa  kalci',
                'staff_type' => 'teaching',
                'response_type' => 'frequency_4',
                'academic_year_id' => 1,
                'created_at' => '2025-06-07 02:52:39',
                'is_open' => 1,
            ],
        ]);
    }
} 