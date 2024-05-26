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
    

    public function autoAssign($conferenceId)
    {
        $conference = Conference::find($conferenceId);

         if (!$conference) {
            return response()->json(['message' => 'Conference not found'], 404);
        }

        $papers = Paper::where('conference_id', $conferenceId)->get();
        $reviewers = $conference->users()
            ->wherePivot('role', 'reviewer')
            ->get()
            ->filter(function ($user) use ($conferenceId) {
                return Invitations::where('conference_id', $conferenceId)
                    ->where('email', $user->email)
                    ->where('invitationStatus', 'accepted')
                    ->exists();
                });

        $numPapers = $papers->count();
        $numReviewers = $reviewers->count();

         // Log the number of papers and reviewers
        \Log::info("Number of papers: $numPapers");
        \Log::info("Number of reviewers: $numReviewers");

        if ($numPapers == 0) {
             return response()->json(['message' => 'No papers available for assignment']);
        }

        if ($numReviewers / $numPapers < 2) {
             return response()->json(['message' => 'The number of reviewers is insufficient. Please add more reviewers.']);
        }

        $assignedReviewers = collect();

        foreach ($papers as $paper) {
            $reviewersAssigned = $this->assignReviewers($paper, $reviewers, $numPapers, $assignedReviewers);
            $paper->reviewers()->sync($reviewersAssigned->pluck('id')->toArray());
            $assignedReviewers = $assignedReviewers->merge($reviewersAssigned);
        }

         return response()->json(['message' => 'Papers have been successfully assigned to reviewers']);
    }

      private function assignReviewers($paper, $reviewers, $numPapers, $assignedReviewers)
   {
        $paperKeywords = explode(',', $paper->keywords);
        $matchedReviewers = collect();

        // Matching reviewers from registered users with accepted invitations
        foreach ($reviewers as $reviewer) {
            if (!$assignedReviewers->contains($reviewer)) {
                $reviewerTopic = Invitations::where('email', $reviewer->email)
                     ->where('invitationStatus', 'accepted')
                     ->value('reviewerTopic');

                if (!empty($reviewerTopic) && in_array($reviewerTopic, $paperKeywords)) {
                     $matchedReviewers->push($reviewer);
                }
            }
        }

         // Check if there are matched reviewers
        if ($matchedReviewers->isEmpty()) {
             return collect(); // Return an empty collection if no reviewers are available
        }

         // Calculate the number of reviewers per paper
         $numReviewersPerPaper = min(max(3, floor($reviewers->count() / $numPapers)), 5);

         // Select random reviewers from matched reviewers
         $selectedReviewers = $matchedReviewers->random(min($numReviewersPerPaper, $matchedReviewers->count()));

        return $selectedReviewers;
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
