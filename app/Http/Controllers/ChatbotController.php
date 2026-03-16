<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function handleMessage(Request $request)
    {
        $message = strtolower($request->input('message', ''));
        $words = explode(' ', $message);
        $response = "I'm sorry, I thinks you're query is not part of the system or else contact IPES CONCERN page.";

        // Logic 0: Greetings
        if (in_array('hi', $words) || in_array('hello', $words) || in_array('hey', $words)) {
            $response = "Hello! How can I help you today?";
        }
        // Logic 1: Basic Keyword Match
        else if (Str::contains($message, ['what', 'IPES'])) {
            $response = "MCC-IPES (Instructors Performance Evaluation System) is a digital platform for evaluating faculty performance.";
        }

          else if (Str::contains($message, 'buang') || Str::contains($message, 'pisti') || Str::contains($message, 'yawa')) {
            $response = "Please stop using bad words, you are monitored by the team";
        }

          else if (Str::contains($message, 'thank you') || Str::contains($message, 'nice') || Str::contains($message, 'good')) {
            $response = "Thank you my dear student, I am here to help you. If you have any questions, feel free to ask.";
        }
        // Logic 2: Database Query (Example: Count students)
        else if (Str::contains($message, ['how many', 'students'])) {
            // Assuming your table name is 'students'
            $count = DB::table('students')->count();
            $response = "There are currently " . $count . " students registered in the system.";
        }

        // Logic 3: Another Database Query (Example: Completed evaluations)
        else if (Str::contains($message, ['done', 'evaluated'])) {
            // Adjust table name and condition to your schema
            $evaluatedCount = DB::table('evaluations')->where('status', 'completed')->count();
            $response = "So far, " . $evaluatedCount . " students have completed their evaluations.";
        }
          
       
        
        return response()->json(['reply' => $response]);
    }
}
