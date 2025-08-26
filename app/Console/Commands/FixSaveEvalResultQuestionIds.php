<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\SavedQuestion;
use App\Models\Question;

class FixSaveEvalResultQuestionIds extends Command
{
    protected $signature = 'fix:save-eval-result-questions';
    protected $description = 'Fix save_eval_result.question_id to match saved_questions.id by academic year, staff type, and title';

    public function handle()
    {
        $this->info('Starting to fix save_eval_result.question_id values...');
        $count = 0;
        $skipped = 0;
        $rows = DB::table('save_eval_result')->get();
        foreach ($rows as $row) {
            // Find the original question (may be deleted, so fallback to DB query)
            $question = DB::table('questions')->where('id', $row->question_id)->first();
            if (!$question) {
                $skipped++;
                continue;
            }
            // Find the matching saved_questions record
            $saved = SavedQuestion::where('title', $question->title)
                ->where('staff_type', $question->staff_type)
                ->where('academic_year_id', $row->academic_year_id)
                ->first();
            if ($saved) {
                DB::table('save_eval_result')->where('id', $row->id)->update(['question_id' => $saved->id]);
                $count++;
            } else {
                $skipped++;
            }
        }
        $this->info("Updated $count rows. Skipped $skipped rows (no match found).");
        $this->info('Done.');
    }
} 