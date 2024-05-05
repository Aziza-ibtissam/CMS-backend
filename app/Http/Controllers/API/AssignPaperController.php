<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AssignPaperController extends Controller
{
    public function createAssignPaper(Request $request)
    {
        $validatedData = $request->validate([
            'answers' => 'required|array',
            'finalDecision' => 'required|integer',
            'isEligible' => 'required',
            'comments' => 'required|string',
            'confidentialRemarks' => 'nullable|string',
        ]);

        // Fetch userId, conferenceId, paperId from route
        $userId = $request->userId;
        $conferenceId = $request->conferenceId;
        $paperId = $request->paperId;

        // Create new record
        $assignPaper = AssignPaper::create([
            'userId' => $userId,
            'conference_id' => $conferenceId,
            'paper_id' => $paperId,
            'answers' => $validatedData['answers'],
            'finalDecision' => $validatedData['finalDecision'],
            'isEligible' => $validatedData['isEligible'],
            'comments' => $validatedData['comments'],
            'confidentialRemarks' => $validatedData['confidentialRemarks'],
        ]);

        return response()->json($conferencePaperDecision, 201);
    }
}
