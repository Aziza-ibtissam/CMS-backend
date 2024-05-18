<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Paper;
use App\Models\Conference;
use App\Models\User;
use Illuminate\Support\Facades\DB;


use Illuminate\Http\Request;

class PaperController extends Controller
{
    public function submitPaper(Request $request)
    {
        // Decode authors JSON
        $authorsArray = $request->input('authors');

        // Validate the request
        $request->validate([
            'paperFile' => 'required|file|max:2048',
            'conference_id' => 'required|exists:conferences,id',
            'paperTitle' => 'required|string',
            'submitted_at' => 'required|date',
            'abstract' => 'required|string',
            'keywords' => 'required|string',
            'authors' => 'required|array',
            'email' => 'required|email|exists:users,email'
        ]);
        $existingPaper = Paper::where('conference_id', $request->input('conference_id'))
        ->where(function($query) use ($request, $authorsArray) {
            $query->where('abstract', $request->input('abstract'))
                  ->orWhere('keywords', $request->input('keywords'))
                  ->orWhere('authors', json_encode($authorsArray));
        })
        ->first();
    if ($existingPaper) {
        return response()->json(['message' => 'This paper has already been submitted to this conference'], 409);
    }
    $user = User::where('email', $request->input('email'))->first();
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }
    
        // Format the submitted_at value
        $submittedAt = date('Y-m-d H:i:s', strtotime($request->input('submitted_at')));
    
        // Handle file upload
        if ($request->hasFile('paperFile')) {
            $paperFile = $request->file('paperFile')->store('papers');
        }
    
        // Store the submitted paper details
        $paper = new Paper();
        $paper->conference_id = $request->input('conference_id');
        $paper->user_id = $user->id;
        $paper->paperTitle = $request->input('paperTitle');
        $paper->abstract = $request->input('abstract');
        $paper->keywords = $request->input('keywords');
        $paper->authors = json_encode($authorsArray); // Save the authors as a JSON string
        $paper->submitted_at = $submittedAt;
        $paper->paperFile = $paperFile;
        $paper->save();
    
        if ($user) {
            $conference = Conference::find($request->input('conference_id'));
            $user->conferences()->attach($conference, ['role' => 'author']);
        }
    
        return response()->json(['message' => 'Paper submitted successfully'], 200);
    }
    
    public function calculateAverageScore($paperId)
{
    // Retrieve the paper object
    $paper = Paper::findOrFail($paperId);

    // Retrieve all reviewers and their reviews for the paper
    $reviews = DB::table('assign_paper')
        ->where('paper_id', $paperId)
        ->get();

    // Initialize variables
    $totalWeightedSum = 0;
    $totalCoefficient = 0;

    // Loop through each review
    foreach ($reviews as $review) {
        // Decode answers JSON string to array
        $answers = json_decode($review->answers, true);

        // Retrieve the conference ID of the paper
        $conferenceId = $paper->conference_id;

        // Retrieve form ID associated with the conference of the paper
        $formId = DB::table('forms')
            ->where('conference_id', $conferenceId)
            ->value('id');

        if (!$formId) {
            // If no form is found for the conference, skip this review
            continue;
        }

        // Retrieve coefficients from the forms table
        $coefficients = DB::table('forms')
            ->where('id', $formId)
            ->first();

        // Calculate weighted sum for answers
        foreach ($answers as $questionId => $answer) {
            // Retrieve coefficient for this question
            $questionCoefficient = DB::table('questions')
                ->where('id', $questionId)
                ->value('coefficient');

            // Add weighted sum for this question
            $totalWeightedSum += $answer * $questionCoefficient;
            $totalCoefficient += $questionCoefficient;

        }

        // Add weighted sum for finalDecision
        $totalWeightedSum += $review->finalDecision * $coefficients->finalDecisionCoefficient;
        $totalCoefficient += $coefficients->finalDecisionCoefficient;

        // Add weighted sum for isEligible
        $totalWeightedSum += ($review->isEligible === 'yes' ? 1 : 0) * $coefficients->eligibleCoefficient;
        $totalCoefficient += $coefficients->eligibleCoefficient;

        // Add weighted sum for confidentialRemarks
        $totalWeightedSum += $review->confidentialRemarks * $coefficients->confidentialRemarksCoefficient;
        $totalCoefficient += $coefficients->confidentialRemarksCoefficient;
    }

    // Calculate total average
    $totalAverage = $totalCoefficient > 0 ? $totalWeightedSum / $totalCoefficient : 0;

    // Return the total average
    return  ['totalAverage' => $totalAverage, ];
}

public function getPaperForAuthor($conferenceId, $userId)
    {
        $paper = Paper::where('conference_id', $conferenceId)
                      ->where('user_id', $userId)
                      ->first();

        if (!$paper) {
            return response()->json(['message' => 'Paper not found'], 404);
        }

        $conference = Conference::find($conferenceId);

        return response()->json([
            'paper' => $paper,
            'conference' => $conference
        ]);
    }

    public function uploadFinalVersion(Request $request, $paperId)
    {
        // Validate the request
        $request->validate([
            'finalVersionFile' => 'required|file|max:2048'
        ]);

        $paper = Paper::find($paperId);

        if (!$paper) {
            return response()->json(['message' => 'Paper not found'], 404);
        }

        // Handle file upload
        if ($request->hasFile('finalVersionFile')) {
            $finalVersionFile = $request->file('finalVersionFile')->store('final_versions');
            $paper->finalVersionFile = $finalVersionFile;
            $paper->save();

            return response()->json(['message' => 'Final version uploaded successfully'], 200);
        }

        return response()->json(['message' => 'File upload failed'], 500);
    }

}
