<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Paper;
use App\Models\Conference;
use App\Models\User;
use App\Models\ConferenceSchedule;
use App\Models\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\AcceptationsSetting;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http; 






use Illuminate\Http\Request;

class PaperController extends Controller

{   public function getPapersByConference($conferenceId)
    {    

        $papers = Paper::select('papers.*', 'conferences.review_due_date')
        ->where('papers.conference_id', $conferenceId)
        ->join('conferences', 'papers.conference_id', '=', 'conferences.id')
        ->get();

        
        return response()->json( ['papers'=>$papers]);
    }
    
    public function show($paperId)
    {
        // Retrieve the paper details by ID
        $paper = Paper::findOrFail($paperId);

        return response()->json($paper);
    }
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
    
        $submittedAt = date('Y-m-d H:i:s', strtotime($request->input('submitted_at')));
    
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
        
        \Log::info('Paper file path: ' . $paperFile);

        // Attach the conference role to the user
        if ($user) {
            $conference = Conference::find($request->input('conference_id'));
            $user->conferences()->attach($conference, ['role' => 'author']);
        }
    
        // Call the OCR API endpoint and handle the response
        $response = Http::post(env('FLASK_API_URL2') . '/verify_author_names', ['pdf_path' => $paper->paperFile ]);
            

    
        $authorNamesFound = $response->json()['author_names_found'];
    
        if ($response->successful() && $response->json() && isset($response->json()['author_names_found'])) {
            // Get the value of 'author_names_found'
            $authorNamesFound = $response->json()['author_names_found'];
    
            if ($authorNamesFound) {
                // If author names are found, delete the saved paper and the role
                $paper->delete();
                if ($user) {
                    $user->conferences()->detach($conference);
                }
                return response()->json(['message' => 'Author names found in the paper. Paper submission failed.'], 400);
            }
    
            // If author names are not found, proceed with success message
            return response()->json(['message' => 'Paper submitted successfully'], 200);
        } else {
            // Handle the case when the response doesn't have the expected structure or fails
            return response()->json(['message' => 'Error processing OCR.'], 500);
        }
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
    $paper->mark = $totalAverage;
    $paper->save();
    // Return the total average
    return  ['totalAverage' => $totalAverage, ];
    }
    
     public function getPaperForAuthor($conferenceId, $userId)
    {
        $paper = Paper::where('conference_id', $conferenceId)
                      ->where('user_id', $userId)
                      ->get();

        if (!$paper) {
            return response()->json(['message' => 'Paper not found'], 404);
        }

        $conference = Conference::find($conferenceId);

        return response()->json([
            'paper' => $paper,
            'conference' => $conference
        ]);
    }
    public function download($paperId)
    {
        $paper = Paper::findOrFail($paperId);

        // Get the relative file path from the database
        $filePath = $paper->paperFile;

        // Full path to the storage directory
        $storagePath = storage_path('app/' . $filePath);

        // Check if the file exists using the absolute path
        if (!file_exists($storagePath)) {
            return response()->json(['message' => 'File not found'], 404);
        }

        // Return the file as a downloadable response
        return response()->download($storagePath);
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

    public function tryPaper($conferenceId)
    {
        // Get the acceptance settings for the conference
        $acceptationsSetting = AcceptationsSetting::where('conference_id', $conferenceId)->first();
    
        // Check if the acceptationsSetting is not null
        if ($acceptationsSetting) {
            $acceptedOralPresentations = 0;
            $acceptedPosters = 0;
            $acceptedWaitingList = 0;
    
            // Fetch papers for the conference and order them by mark in descending order
            $papers = Paper::where('conference_id', $conferenceId)
                ->orderBy('mark', 'desc')
                ->get();
    
            // Iterate through each paper
            foreach ($papers as $paper) {
                // Determine the acceptance setting for the paper
                if ($acceptedOralPresentations < $acceptationsSetting->oral_presentations) {
                    $paper->acceptations_setting = 'oral_presentations';
                    $acceptedOralPresentations++;
                } elseif ($acceptedPosters < $acceptationsSetting->poster) {
                    $paper->acceptations_setting = 'poster';
                    $acceptedPosters++;
                } elseif ($acceptedWaitingList < $acceptationsSetting->waiting_list) {
                    $paper->acceptations_setting = 'waiting_list';
                    $acceptedWaitingList++;
                } else {
                    // If all acceptance settings are filled, reject the paper
                    $paper->acceptations_setting = 'rejected';
                }
                
                // Save the updated paper
                $paper->save();
            }
        }
    }
    public function sessionCollect(Request $request, $conferenceId)
    {
        // Assuming you have a Paper model representing your papers table
        $papers = Paper::all();
    
        // Prepare the paper data to be sent to the Flask API
        $paperData = [];
        foreach ($papers as $paper) {
            $keywords = json_decode($paper->keywords, true);
            $paperData[] = [
                'title' => $paper->paperTitle,
                'keywords' => $keywords
            ];
        }
    
        // Prepare the request data
        $requestData = [
            'conference_id' => $conferenceId,
            'papers' => $paperData,
        ];
        \Log::info('Request data type: ' . gettype($requestData));
    
        // Make a POST request to the Flask API
        $response = Http::post(env('FLASK_API_URL') . '/run_algorithm', $requestData);
    
        // Decode the response and return it
        $result = $response->json();
    
        // Log the response data type
        \Log::info('Response data type: ' . gettype($result));
        \Log::info('Request data: ', $requestData);
    
        // Save the processed data into the database
        foreach ($result['sessions'] as $sessionName => $sessionData) {
            $paperIds = [];
            foreach ($sessionData['sessionPapers'] as $paperTitle) {
                // Find the paper by title (assuming titles are unique)
                $paper = Paper::where('paperTitle', $paperTitle)->first();
                if ($paper) {
                    $paperIds[] = $paper->id;
                }
            }
    
            $session = Session::updateOrCreate(
                ['conference_id' => $conferenceId],
                [
                    'sessionPaper' => json_encode($paperIds), // Save paper IDs as JSON
                    'sessionKeywords' => json_encode($sessionData['sessionKeywords'])
                ]
            );
        }
    
        return response()->json(['message' => 'Data processed and saved successfully.', 'result' =>$result ]);
    }
    
}
