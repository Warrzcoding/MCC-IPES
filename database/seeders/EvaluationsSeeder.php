<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EvaluationsSeeder extends Seeder
{
    public function run()
    {
        DB::table('evaluations')->insert([
            [
                'id' => 90,
                'staff_id' => 9,
                'question_id' => 2,
                'response' => 'excellent',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:05:13',
                'comments' => 'good',
                'response_score' => 5
            ],
            [
                'id' => 89,
                'staff_id' => 9,
                'question_id' => 1,
                'response' => 'very_good',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:05:13',
                'comments' => 'good',
                'response_score' => 4
            ],
            [
                'id' => 88,
                'staff_id' => 7,
                'question_id' => 9,
                'response' => 'rarely',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:04:31',
                'comments' => 'ilisanan na',
                'response_score' => 1
            ],
            [
                'id' => 87,
                'staff_id' => 7,
                'question_id' => 8,
                'response' => 'fair',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:04:31',
                'comments' => 'ilisanan na',
                'response_score' => 2
            ],
            [
                'id' => 86,
                'staff_id' => 7,
                'question_id' => 7,
                'response' => 'sometimes',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:04:31',
                'comments' => 'ilisanan na',
                'response_score' => 2
            ],
            [
                'id' => 85,
                'staff_id' => 7,
                'question_id' => 3,
                'response' => 'sometimes',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:04:31',
                'comments' => 'ilisanan na',
                'response_score' => 2
            ],
            [
                'id' => 84,
                'staff_id' => 7,
                'question_id' => 2,
                'response' => 'poor',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:04:31',
                'comments' => 'ilisanan na',
                'response_score' => 1
            ],
            [
                'id' => 83,
                'staff_id' => 7,
                'question_id' => 1,
                'response' => 'poor',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:04:31',
                'comments' => 'ilisanan na',
                'response_score' => 1
            ],
            [
                'id' => 91,
                'staff_id' => 9,
                'question_id' => 3,
                'response' => 'always',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:05:13',
                'comments' => 'good',
                'response_score' => 4
            ],
            [
                'id' => 92,
                'staff_id' => 9,
                'question_id' => 7,
                'response' => 'always',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:05:13',
                'comments' => 'good',
                'response_score' => 4
            ],
            [
                'id' => 93,
                'staff_id' => 9,
                'question_id' => 8,
                'response' => 'excellent',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:05:13',
                'comments' => 'good',
                'response_score' => 5
            ],
            [
                'id' => 94,
                'staff_id' => 9,
                'question_id' => 9,
                'response' => 'most_time',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:05:13',
                'comments' => 'good',
                'response_score' => 3
            ],
            [
                'id' => 95,
                'staff_id' => 8,
                'question_id' => 4,
                'response' => 'poor',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:05:31',
                'comments' => 'xdsx',
                'response_score' => 1
            ],
            [
                'id' => 96,
                'staff_id' => 8,
                'question_id' => 5,
                'response' => 'poor',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:05:31',
                'comments' => 'xdsx',
                'response_score' => 1
            ],
            [
                'id' => 97,
                'staff_id' => 8,
                'question_id' => 6,
                'response' => 'strongly_disagree',
                'user_id' => 23,
                'created_at' => '2025-06-07 03:05:31',
                'comments' => 'xdsx',
                'response_score' => 1
            ],
            [
                'id' => 98,
                'staff_id' => 7,
                'question_id' => 1,
                'response' => 'excellent',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:12:40',
                'comments' => 'eweweweewe',
                'response_score' => 5
            ],
            [
                'id' => 99,
                'staff_id' => 7,
                'question_id' => 2,
                'response' => 'excellent',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:12:40',
                'comments' => 'eweweweewe',
                'response_score' => 5
            ],
            [
                'id' => 100,
                'staff_id' => 7,
                'question_id' => 3,
                'response' => 'always',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:12:40',
                'comments' => 'eweweweewe',
                'response_score' => 4
            ],
            [
                'id' => 101,
                'staff_id' => 7,
                'question_id' => 7,
                'response' => 'always',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:12:40',
                'comments' => 'eweweweewe',
                'response_score' => 4
            ],
            [
                'id' => 102,
                'staff_id' => 7,
                'question_id' => 8,
                'response' => 'excellent',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:12:40',
                'comments' => 'eweweweewe',
                'response_score' => 5
            ],
            [
                'id' => 103,
                'staff_id' => 7,
                'question_id' => 9,
                'response' => 'always',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:12:40',
                'comments' => 'eweweweewe',
                'response_score' => 4
            ],
            [
                'id' => 104,
                'staff_id' => 9,
                'question_id' => 1,
                'response' => 'poor',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:13:19',
                'comments' => 'swwsqw',
                'response_score' => 1
            ],
            [
                'id' => 105,
                'staff_id' => 9,
                'question_id' => 2,
                'response' => 'excellent',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:13:19',
                'comments' => 'swwsqw',
                'response_score' => 5
            ],
            [
                'id' => 106,
                'staff_id' => 9,
                'question_id' => 3,
                'response' => 'always',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:13:19',
                'comments' => 'swwsqw',
                'response_score' => 4
            ],
            [
                'id' => 107,
                'staff_id' => 9,
                'question_id' => 7,
                'response' => 'always',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:13:19',
                'comments' => 'swwsqw',
                'response_score' => 4
            ],
            [
                'id' => 108,
                'staff_id' => 9,
                'question_id' => 8,
                'response' => 'excellent',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:13:19',
                'comments' => 'swwsqw',
                'response_score' => 5
            ],
            [
                'id' => 109,
                'staff_id' => 9,
                'question_id' => 9,
                'response' => 'always',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:13:19',
                'comments' => 'swwsqw',
                'response_score' => 4
            ],
            [
                'id' => 110,
                'staff_id' => 8,
                'question_id' => 4,
                'response' => 'excellent',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:13:51',
                'comments' => 'wwwww',
                'response_score' => 5
            ],
            [
                'id' => 111,
                'staff_id' => 8,
                'question_id' => 5,
                'response' => 'excellent',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:13:51',
                'comments' => 'wwwww',
                'response_score' => 5
            ],
            [
                'id' => 112,
                'staff_id' => 8,
                'question_id' => 6,
                'response' => 'strongly_agree',
                'user_id' => 25,
                'created_at' => '2025-06-07 03:13:51',
                'comments' => 'wwwww',
                'response_score' => 5
            ]
        ]);
    }
} 