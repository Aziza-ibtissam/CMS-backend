<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Paper;
use Illuminate\Http\Request;

class PaperController extends Controller
{
    public function submitPaper(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'paperFile' => 'required|file|max:2048', // Adjust the file size limit as needed
            'conference_id' => 'required|exists:conferences,id',
            'paperTitle' => 'required|string',
            'emailAuth' => 'required|string',
            'submitted_at' => 'required|date',
        ]);

        // Store the submitted paper details
        $paperCall = new Paper();
        $paperCall->conference_id = $request->input('conference_id');
        $paperCall->emailAuth = $request->input('emailAuth');
        $paperCall->filename = $request->input('paperTitle');
        $paperCall->submitted_at = $request->input('submitted_at');
        $paperFile = $request->file('paperFile');
       

        $paperCall->save();
        $user = User::where('email', $request->input('email'))->first();
    if ($user) {
        $conference = Conference::find($request->input('conference_id'));
        $user->conferences()->attach($conference, ['role' => 'author']);
    }
        return response()->json(['message' => 'Paper submitted successfully'], 200);
    }
}
