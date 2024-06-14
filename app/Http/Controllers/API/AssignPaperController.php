<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssignPaper;
use App\Models\Conference;
use App\Models\Paper;
use App\Models\Invitations;
use App\Models\User;
use App\Notifications\PaperAssigned;
use App\Http\Controllers\API\PaperController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; 



class AssignPaperController extends Controller

{   public function getAssignedPapers($conferenceId)
    {
        $user = Auth::user();
    
        // Log conference ID
        \Log::info('Conference ID: ' . $conferenceId);
    
        // Query papers based on the logged-in user and conference ID
        $papers = $user->papers()->where('conference_id', $conferenceId)->with('conference')->get();
    
        // Fetch chair email
        $conference = Conference::findOrFail($conferenceId);
        $chair = $conference->users()->wherePivot('role', 'chair')->first();
    
        // Add chair email to each paper
        $papers->each(function($paper) use ($chair) {
            $paper->chair_email = $chair ? $chair->email : null;
        });
    
        return response()->json($papers);
    }
    

    public function assignReviewers(Request $request, $conferenceId)
    {
        $papers = Paper::where('conference_id', $conferenceId)->get(['id', 'paperTopic']);
        $reviewers = Invitations::where('conference_id', $conferenceId)
            ->where('invitationStatus', 'accepted')
            ->get(['email', 'reviewerTopic']);
    
        // Transform papers data
        $transformedPapers = [];
        foreach ($papers as $paper) {
            $topics = str_replace(['"', '\\'], '', $paper->paperTopic);
            $topics = explode(',', $topics);
            $transformedPapers[] = ['id' => $paper->id, 'topics' => $topics];
        }
    
        // Transform reviewers data
        $transformedReviewers = [];
        foreach ($reviewers as $reviewer) {
            $expertise = str_replace(['"', '\\'], '', $reviewer->reviewerTopic);
            $expertise = explode(',', $expertise);
            $transformedReviewers[] = ['id' => $reviewer->email, 'expertise' => $expertise];
        }
        \Log::info('requset data type: ' . json_encode($transformedReviewers));
        \Log::info('requset data type: ' . json_encode($transformedPapers));
        // Prepare payload for Flask API
        $payload = [
            'papers' => $transformedPapers,
            'reviewers' => $transformedReviewers
        ];
    
        // Send data to Flask API
        $response = Http::post('http://127.0.0.1:5054/assign-reviewers', $payload);
    
        // Process response from Flask API (assuming assignments are saved in the database)
        $assignments = $response->json('assignments');
    
        foreach ($assignments as $assignment) {
            $paperId = $assignment['paper_id'];
            foreach ($assignment['reviewers'] as $reviewerEmail) {
                $user = User::where('email', $reviewerEmail)->first();
    
                if ($user) {
                    AssignPaper::create([
                        'paper_id' => $paperId,
                        'user_id' => $user->id
                    ]);
                    //$user->notify(new PaperAssigned(Paper::find($paperId)));
                }
            }
        }
    
        return response()->json(['message' => 'Assignments saved successfully']);
    }


    public function manualAssign(Request $request, $reviewerid)
    {

    // Step 1: Retrieve the reviewer
    $reviewer = User::findOrFail($reviewerId);

    // Step 2: Retrieve the paper IDs and additional data from the request
    $assignments = $request->input('assignments');

    // Step 3: Insert records into the assign_papers table
    foreach ($assignments as $assignment) {
        // Example structure assuming 'assignments' is an array of arrays
        $paperId = $assignment['paper_id'];
       

        // Insert into the assign_papers table
        AssignPapers::create([
            'user_id' => $reviewer->id,
            'paper_id' => $paperId,
            
        ]);
    }

    // Optionally, return a response or redirect to a success page
    return response()->json(['message' => 'Papers assigned successfully'], 200);
}



    public function getReviewers($conferenceId)
    {
        $reviewers = User::whereHas('conferences', function($query) use ($conferenceId) {
            $query->where('conference_id', $conferenceId)
                ->where('role', 'reviewer');
        })
        ->with(['papers' => function ($query) use ($conferenceId) {
            $query->where('conference_id', $conferenceId);
        }])
        ->get()
        ->map(function ($reviewer) {
            $reviewer->assigned_papers_count = $reviewer->papers->count();
            $reviewer->completed_papers_count = $reviewer->papers->where('pivot.finalDecision', '!=', null)->count();
            return $reviewer;
        });

        return response()->json($reviewers);
    }
    public function storeEvaluation(Request $request, $paperId)
    {
        $request->validate([
            'answers' => 'required|array',
            'finalDecision' => 'nullable|integer',
            'isEligible' => 'nullable|in:yes,no',
            'comments' => 'required|string',
            'confidentialRemarks' => 'nullable|string',
        ]);

        $isEligible = $request->input('isEligible', 'no');
        $userId = Auth::id();

        
        $assignPaper = AssignPaper::updateOrCreate(
            [
                'paper_id' => $paperId,
                'user_id' => $userId,
            ],
            [
                'answers' => json_encode($request->answers),
                'finalDecision' => $request->finalDecision,
                'isEligible' => $isEligible,
                'comments' => $request->comments,
                'confidentialRemarks' => $request->confidentialRemarks,
            ]
        );

            // Call the method to calculate the average score
        $paperController = new PaperController();
        $paperController->calculateAverageScore($assignPaper->paper_id);

        return response()->json($assignPaper, 201);
        
    }
    
}
