<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResponseOptionsSeeder extends Seeder
{
    public function run()
    {
        DB::table('response_options')->insert([
            // Rating 5 options
            ['id' => 1, 'response_type' => 'rating_5', 'option_value' => 'poor', 'option_label' => 'Poor', 'option_order' => 1, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 2, 'response_type' => 'rating_5', 'option_value' => 'fair', 'option_label' => 'Fair', 'option_order' => 2, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 3, 'response_type' => 'rating_5', 'option_value' => 'good', 'option_label' => 'Good', 'option_order' => 3, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 4, 'response_type' => 'rating_5', 'option_value' => 'very_good', 'option_label' => 'Very Good', 'option_order' => 4, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 5, 'response_type' => 'rating_5', 'option_value' => 'excellent', 'option_label' => 'Excellent', 'option_order' => 5, 'created_at' => '2025-06-04 07:05:50'],
            
            // Frequency 4 options
            ['id' => 6, 'response_type' => 'frequency_4', 'option_value' => 'rarely', 'option_label' => 'Rarely', 'option_order' => 1, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 7, 'response_type' => 'frequency_4', 'option_value' => 'sometimes', 'option_label' => 'Sometimes', 'option_order' => 2, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 8, 'response_type' => 'frequency_4', 'option_value' => 'most_time', 'option_label' => 'Most of the Time', 'option_order' => 3, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 9, 'response_type' => 'frequency_4', 'option_value' => 'always', 'option_label' => 'Always', 'option_order' => 4, 'created_at' => '2025-06-04 07:05:50'],
            
            // Agreement 5 options
            ['id' => 10, 'response_type' => 'agreement_5', 'option_value' => 'strongly_disagree', 'option_label' => 'Strongly Disagree', 'option_order' => 1, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 11, 'response_type' => 'agreement_5', 'option_value' => 'disagree', 'option_label' => 'Disagree', 'option_order' => 2, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 12, 'response_type' => 'agreement_5', 'option_value' => 'neutral', 'option_label' => 'Neutral', 'option_order' => 3, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 13, 'response_type' => 'agreement_5', 'option_value' => 'agree', 'option_label' => 'Agree', 'option_order' => 4, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 14, 'response_type' => 'agreement_5', 'option_value' => 'strongly_agree', 'option_label' => 'Strongly Agree', 'option_order' => 5, 'created_at' => '2025-06-04 07:05:50'],
            
            // Satisfaction 5 options
            ['id' => 15, 'response_type' => 'satisfaction_5', 'option_value' => 'very_satisfied', 'option_label' => 'Very Satisfied', 'option_order' => 1, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 16, 'response_type' => 'satisfaction_5', 'option_value' => 'satisfied', 'option_label' => 'Satisfied', 'option_order' => 2, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 17, 'response_type' => 'satisfaction_5', 'option_value' => 'neutral', 'option_label' => 'Neutral', 'option_order' => 3, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 18, 'response_type' => 'satisfaction_5', 'option_value' => 'dissatisfied', 'option_label' => 'Dissatisfied', 'option_order' => 4, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 19, 'response_type' => 'satisfaction_5', 'option_value' => 'very_dissatisfied', 'option_label' => 'Very Dissatisfied', 'option_order' => 5, 'created_at' => '2025-06-04 07:05:50'],
            
            // Yes/No options
            ['id' => 20, 'response_type' => 'yes_no', 'option_value' => 'no', 'option_label' => 'No', 'option_order' => 0, 'created_at' => '2025-06-04 07:05:50'],
            ['id' => 21, 'response_type' => 'yes_no', 'option_value' => 'yes', 'option_label' => 'Yes', 'option_order' => 1, 'created_at' => '2025-06-04 07:05:50'],
        ]);
    }
} 