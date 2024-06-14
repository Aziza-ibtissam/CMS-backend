<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\ConferenceSchedule;

use Illuminate\Http\Request;

class ConferenceScheduleController extends Controller
{

    public function index($conferenceId)
    {
        $conferenceSchedules = ConferenceSchedule::where('conference_id', $conferenceId)->get();

        return response()->json($conferenceSchedules);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'day' => 'required',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'conferenceId' => 'required|integer|exists:conferences,id',
            'session_number' =>'required',
        ]);
    
        $conferenceSchedule = ConferenceSchedule::updateOrCreate(
            ['id' => $request->input('id')], // Check if id exists
            [
                'day' => $request->input('day'),
                'date' => $request->input('date'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'conference_id' => $request->input('conferenceId'),
                'session_number' => $request->input('session_number'),
    
            ]
        );
    
        $message = $request->has('id') ? 'Conference updated successfully!' : 'Conference scheduled successfully!';
    
        return response()->json(['message' => $message, 'data' => $conferenceSchedule], 200);
    }
    public function showByConference( $conferenceId)
    {
        $conferenceSchedules = ConferenceSchedule::where('conference_id', $conferenceId)->get();
    
        return response()->json($conferenceSchedules, 200);
    }
}
