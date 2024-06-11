<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Paper;
use App\Models\Session;
use App\Models\ConferenceSchedule;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http; 

class SessionController extends Controller {
    
    public function assign_slot(Request $request , $conferenceId)
    {
        // Assuming you have a Session model representing your sessions table
        $sessions = Session::where('conference_id', $conferenceId)->get();
    
        // Prepare the session data to be sent to the Flask API
        $sessionData = [];
        foreach ($sessions as $session) {
            // Assuming each session has 'id' and 'keywords' attributes
            $keywords = json_decode($session->sessionKeywords);
            $keywords = array_map(function($keyword) {
                return str_replace(['"', '\\'], '', $keyword);
            }, $keywords);
    
            $sessionData[] = [
                'session_id' => $session->id,
                'keywords' => $keywords
            ];
        }

        $schedules = ConferenceSchedule::where('conference_id', $conferenceId)->get();

        $scheduleData = [];
        foreach ($schedules as $schedule) {
            $scheduleData[] = [
                'slot_limit' => $schedule->session_number
            ];
        }
        \Log::info('Request data type: ' . json_encode($scheduleData));
        \Log::info('Request data type: ' . json_encode($sessionData));


        
       
        
        // Make a POST request to the Flask API
        $response = Http::post(env('FLASK_API_URL1') . '/run_slot_algorithm', [
            'session' => $sessionData,
            'schedules' => $scheduleData
        ]);
    
        // Decode the response and return it
        $result = $response->json();
        $slotSessions = $result['slot_sessions'];
        $slots = $result['slots'];
        
        foreach ($slotSessions as $slotIndex => $sessionIds) {
            $schedule = ConferenceSchedule::find($slotIndex + 1);

            foreach ($sessionIds as $sessionId) {
                $session = Session::find($sessionId);
                if ($session && $session->conference_id == $conferenceId) {
                    $session->conference_schedules_id = $schedule->id;
                    $session->save();
                }
            }
        }

        return response()->json($result);
    }
    
      


    public function fetchPaperTitles(Request $request, $conferenceId)
    {
         // Retrieve the sessions from the database
    $sessions = Session::all();
    
    // Initialize an array to store session data with paper titles
    $sessionData = [];

    // Loop through each session
    foreach ($sessions as $session) {
        // Extract paper IDs from the session's paper JSON field
        $paperIds = json_decode($session->sessionPaper);
        
        // Query the papers table to fetch titles associated with paper IDs
        $papers = Paper::whereIn('id', $paperIds)->get();
        
        // Extract paper titles
        $paperTitles = $papers->pluck('paperTitle')->toArray();
        
        // Add session data with paper titles to the array
        $sessionData[] = [
            'session_id' => $session->id,
            'session_papers' => $paperTitles,
            // You can add other session data here if needed
        ];
    }
    
    // Return the session data with paper titles
    return response()->json($sessionData);
    }

    public function fetchSessionsWithSchedule($conferenceId)
    {
        // Fetch sessions with related conference schedules for the given conference
        $sessions = Session::where('conference_id', $conferenceId)->with('conferenceSchedule')->get();

        return response()->json($sessions);
    }


}