<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\SavedQuestion;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'staff_type' => 'required|in:teaching,non-teaching',
            'response_type' => 'required|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }
        
        // Get the current academic year ID
        $academic_year = AcademicYear::where('is_active', true)->first();
        if (!$academic_year) {
            return redirect()->back()
                ->with('message', 'No academic year found. Please create an academic year first.')
                ->with('message_type', 'danger');
        }
        
        // Check for duplicate question with same title and description
        $existingQuestion = Question::where('academic_year_id', $academic_year->id)
            ->where('title', $request->title)
            ->where('description', $request->description)
            ->first();
            
        if ($existingQuestion) {
            return redirect()->back()
                ->withInput()
                ->with('message', 'A question with the same title and description already exists. You can use the same title with a different description.')
                ->with('message_type', 'warning');
        }
        
        try {
            Question::create([
                'title' => $request->title,
                'description' => $request->description,
                'staff_type' => $request->staff_type,
                'response_type' => $request->response_type,
                'academic_year_id' => $academic_year->id,
                'is_open' => false // <-- DEFAULT TO CLOSED
            ]);
            
            return redirect()->back()
                ->with('message', 'Question created successfully!')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error: ' . $e->getMessage())
                ->with('message_type', 'danger')
                ->withInput();
        }
    }
    
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|integer|exists:questions,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'response_type' => 'required|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }
        
        try {
            $question = Question::findOrFail($request->question_id);
            
            // Check for duplicate question with same title and description (excluding current question)
            $existingQuestion = Question::where('academic_year_id', $question->academic_year_id)
                ->where('title', $request->title)
                ->where('description', $request->description)
                ->where('id', '!=', $request->question_id)
                ->first();
                
            if ($existingQuestion) {
                return redirect()->back()
                    ->withInput()
                    ->with('message', 'A question with the same title and description already exists. You can use the same title with a different description.')
                    ->with('message_type', 'warning');
            }
            
            $question->update([
                'title' => $request->title,
                'description' => $request->description,
                'response_type' => $request->response_type
            ]);
            
            return redirect()->back()
                ->with('message', 'Question updated successfully!')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error updating question. Please try again.')
                ->with('message_type', 'danger')
                ->withInput();
        }
    }
    
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|integer|exists:questions,id'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('message', 'Invalid question ID.')
                ->with('message_type', 'danger');
        }
        
        try {
            $question = Question::findOrFail($request->question_id);
            $question->delete();
            
            return redirect()->back()
                ->with('message', 'Question deleted successfully!')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error deleting question. Please try again.')
                ->with('message_type', 'danger');
        }
    }
    
    public function toggleStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|integer|exists:academic_years,id'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('message', 'Invalid academic year ID.')
                ->with('message_type', 'danger');
        }
        
        try {
            // Get current status from first question
            $first_question = Question::where('academic_year_id', $request->academic_year_id)->first();
            if (!$first_question) {
                return redirect()->back()
                    ->with('message', 'No questions found for this academic year.')
                    ->with('message_type', 'warning');
            }
            
            // Toggle status
            $new_status = !$first_question->is_open;
            
            // Update all questions for this academic year
            Question::where('academic_year_id', $request->academic_year_id)
                   ->update(['is_open' => $new_status]);
            
            // Clear schedule so manual action always wins
            $year = AcademicYear::find($request->academic_year_id);
            if ($year) {
                $year->open_at = null;
                $year->close_at = null;
                $year->save();
            }
            
            $status_text = $new_status ? 'opened' : 'closed';
            return redirect()->back()
                ->with('message', "Questionnaire has been $status_text successfully!")
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error updating questionnaire status. Please try again.')
                ->with('message_type', 'danger');
        }
    }
    
    public function saveToAcademic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|integer|exists:academic_years,id'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->with('message', 'Invalid academic year ID.')
                ->with('message_type', 'danger');
        }
        
        try {
            // Get all current questions
            $questions = Question::where('academic_year_id', $request->academic_year_id)->get();
            
            if ($questions->isEmpty()) {
                return redirect()->back()
                    ->with('message', 'No questions found to save.')
                    ->with('message_type', 'warning');
            }
            
            // Update all questions to be saved to this academic year
            foreach ($questions as $question) {
                $question->update([
                    'academic_year_id' => $request->academic_year_id
                ]);
            }
            
            return redirect()->back()
                ->with('message', 'All questions have been saved to the academic year successfully!')
                ->with('message_type', 'success');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error saving questions to academic year. Please try again.')
                ->with('message_type', 'danger');
        }
    }
    
    public function setSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
            'open_at' => 'nullable|date',
            'close_at' => 'nullable|date',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('message', 'Invalid schedule data.')->with('message_type', 'danger');
        }
        $year = AcademicYear::findOrFail($request->academic_year_id);
        $year->open_at = $request->open_at;
        $year->close_at = $request->close_at;
        $year->save();
        return redirect()->back()->with('message', 'Schedule saved!')->with('message_type', 'success');
    }

    public function clearSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'required|integer|exists:academic_years,id',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('message', 'Invalid academic year ID.')->with('message_type', 'danger');
        }
        $year = AcademicYear::findOrFail($request->academic_year_id);
        $year->open_at = null;
        $year->close_at = null;
        $year->save();
        return redirect()->back()->with('message', 'Schedule cleared!')->with('message_type', 'success');
    }

    public function getQuestionnairesData()
    {
        // Get current academic year (active)
        $current_academic_year = AcademicYear::where('is_active', true)->first();
        // Auto open/close logic
        if ($current_academic_year) {
            $now = now();
            $openAt = $current_academic_year->open_at ? \Carbon\Carbon::parse($current_academic_year->open_at) : null;
            $closeAt = $current_academic_year->close_at ? \Carbon\Carbon::parse($current_academic_year->close_at) : null;
            $shouldBeOpen = true;
            if ($openAt && $now->lt($openAt)) {
                $shouldBeOpen = false;
            }
            if ($closeAt && $now->gte($closeAt)) {
                $shouldBeOpen = false;
            }
            // Update all questions if needed
            $questions = Question::where('academic_year_id', $current_academic_year->id)->get();
            if ($questions->isNotEmpty() && $questions->first()->is_open !== $shouldBeOpen) {
                Question::where('academic_year_id', $current_academic_year->id)->update(['is_open' => $shouldBeOpen]);
            }
        }
        
        // Get all questions for current academic year
        $questions = collect();
        if ($current_academic_year) {
            $questions = Question::where('academic_year_id', $current_academic_year->id)
                                ->orderBy('staff_type')
                                ->get(); // This is a Collection
        }
        
        // Count questions by staff type
        $teaching_count = $questions->where('staff_type', 'teaching')->count();
        $non_teaching_count = $questions->where('staff_type', 'non-teaching')->count();
        
        // Get questionnaire status (from first question)
        $questionnaire_status = $questions->isNotEmpty() ? $questions->first()->is_open : false;
        
        // Response type options
        $response_types = [
            'Rating_Scale' => 'Rating_Scale (Poor, Fair, Good, Very Good, Excellent)',
            'Frequency' => 'Frequency (Always, Most of the Time, Sometimes, Rarely)',
            'Agreement' => 'Agreement (Strongly Disagree, Disagree, Neutral, Agree, Strongly Agree)',
            'Satisfaction' => 'Satisfaction (Very Dissatisfied, Dissatisfied, Neutral, Satisfied, Very Satisfied)',
            'Yes_No' => 'Yes/No',
            'Text' => 'Text Response'
        ];
        
        return [
            'current_academic_year' => $current_academic_year,
            'questions' => $questions,
            'teaching_count' => $teaching_count,
            'non_teaching_count' => $non_teaching_count,
            'questionnaire_status' => $questionnaire_status,
            'response_types' => $response_types
        ];
    }

    public function createAcademicYear(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'academic_year' => 'required|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('message', 'Validation failed. Please check your input.')
                ->with('message_type', 'danger');
        }
        
        try {
            // Check if an academic year already exists
            $exists = AcademicYear::count();
            
            if ($exists == 0) {
                AcademicYear::create([
                    'year' => $request->academic_year
                ]);
                
                return redirect()->back()
                    ->with('message', 'Academic Year created successfully!')
                    ->with('message_type', 'success');
            } else {
                return redirect()->back()
                    ->with('message', 'An academic year already exists.')
                    ->with('message_type', 'danger');
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('message', 'Error creating academic year. Please try again.')
                ->with('message_type', 'danger')
                ->withInput();
        }
    }

    public function saveAllQuestions(Request $request)
    {
        $activeYear = AcademicYear::where('is_active', 1)->first();
        if (!$activeYear) {
            return redirect()->back()->with('message', 'No active academic year found.')->with('message_type', 'danger');
        }

        $questions = Question::all();
        if ($questions->isEmpty()) {
            return redirect()->back()->with('message', 'No questions to save.')->with('message_type', 'warning');
        }

        DB::transaction(function () use ($questions, $activeYear) {
            $questionMapping = []; // Map old question ID to new saved question ID
            
            foreach ($questions as $q) {
                $savedQuestion = SavedQuestion::create([
                    'academic_year_id' => $activeYear->id,
                    'title' => $q->title,
                    'description' => $q->description,
                    'staff_type' => $q->staff_type,
                    'response_type' => $q->response_type,
                ]);
                
                // Store the mapping
                $questionMapping[$q->id] = $savedQuestion->id;
            }
            
            // Update existing evaluations to use the new saved question IDs
            foreach ($questionMapping as $oldQuestionId => $newSavedQuestionId) {
                DB::table('evaluations')
                    ->where('question_id', $oldQuestionId)
                    ->update(['question_id' => $newSavedQuestionId]);
            }
            
            Question::query()->delete();
        });

        return redirect()->back()->with('message', 'All questions saved and cleared successfully!')->with('message_type', 'success');
    }

    /**
     * Reuse a single saved question by copying it to the questions table.
     */
    public function reuseSavedQuestion(Request $request)
    {
        $request->validate([
            'saved_question_id' => 'required|exists:saved_questions,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        // Get the current active academic year to ensure we're using the right one
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        if (!$activeAcademicYear) {
            return redirect()->back()->with('message', 'No active academic year found. Please activate an academic year first.')->with('message_type', 'danger');
        }

        $saved = SavedQuestion::findOrFail($request->saved_question_id);
        
        // Create new question in the questions table with the active academic year
        $newQuestion = new Question();
        $newQuestion->title = $saved->title;
        $newQuestion->description = $saved->description;
        $newQuestion->staff_type = $saved->staff_type;
        $newQuestion->response_type = $saved->response_type;
        $newQuestion->academic_year_id = $activeAcademicYear->id; // Use the active academic year
        $newQuestion->is_open = 0; // Set to closed
        $newQuestion->save();
        
        return redirect()->back()->with('message', 'Question reused successfully!')->with('message_type', 'success');
    }

    /**
     * Reuse all saved questions for an academic year by copying them to the questions table.
     */
    public function reuseAllSavedQuestions(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);
        
        // Get the current active academic year to ensure we're using the right one
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        if (!$activeAcademicYear) {
            return redirect()->back()->with('message', 'No active academic year found. Please activate an academic year first.')->with('message_type', 'danger');
        }
        
        $savedQuestions = SavedQuestion::where('academic_year_id', $request->academic_year_id)->get();
        if ($savedQuestions->isEmpty()) {
            return redirect()->back()->with('message', 'No saved questions to reuse.')->with('message_type', 'warning');
        }
        
        foreach ($savedQuestions as $savedQuestion) {
            // Create new question in the questions table with the active academic year
            $newQuestion = new Question();
            $newQuestion->title = $savedQuestion->title;
            $newQuestion->description = $savedQuestion->description;
            $newQuestion->staff_type = $savedQuestion->staff_type;
            $newQuestion->response_type = $savedQuestion->response_type;
            $newQuestion->academic_year_id = $activeAcademicYear->id; // Use the active academic year
            $newQuestion->is_open = 0; // Set to closed
            $newQuestion->save();
        }
        
        return redirect()->back()->with('message', 'All saved questions reused successfully!')->with('message_type', 'success');
    }

    /**
     * AJAX endpoint to check if questions table is empty
     */
    public function checkQuestionsEmpty()
    {
        $count = \App\Models\Question::count();
        return response()->json(['empty' => $count === 0]);
    }
} 