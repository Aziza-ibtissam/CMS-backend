<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conference;
use App\Models\Topic;
use App\Models\Subtopic;
use App\Models\PaperCall;
use App\Models\Role;
use App\Models\AcceptationsSetting;
use App\Rules\AcademicEmail;
use App\Models\UserRoleInConference;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Notifications\ConferenceCreatedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
class ConferenceController extends Controller
{


    public function index()
   {
        $conferences = Conference::all();
    
        return response()->json($conferences);
    }
     public function show($id)
    {
        $conferences =Conference::find($id);

        if (!$conferences) {
            return response()->json(['message' => 'Conference not found'], 404);
        }

        return response()->json($conferences);
    }

    public function create(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => ['required', 'email', new AcademicEmail],
            'title' => 'required|string',
            'acronym' => 'required|string',
            'city' => 'required|string',
            'country' => 'required|string',
            'webpage' => 'required|url',
            'category' => 'required|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'paper_subm_due_date' => 'required|date|after:start_at',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

        ]);

        // Get the authenticated user
        $userID = Auth::id();
        $logoFile = $request->file('logo')->store('logos', 'public');
        $logoPath = Storage::url($logoFile); // Get the relative URL to the file

        // Create a new conference
        $conference = Conference::create([
            'email' =>$request->input('email'),
            'userID' => $userID,
            'title' => $request->input('title'),
            'acronym' => $request->input('acronym'),
            'city' => $request->input('city'),
            'country' => $request->input('country'),
            'webpage' => $request->input('webpage'),
            'category' => $request->input('category'),
            'start_at' => $request->input('start_at'),
            'end_at' => $request->input('end_at'),
            'paper_subm_due_date' => $request->input('paper_subm_due_date'),
            'logo' => $logoPath, // Save the relative path to the logo in the database

        ]);
        
        // Assign the chair role to the authenticated user
        $user = Auth::user();

        $user->conferences()->attach($conference->id, ['role' => 'chair']);

        // Retrieve the user with their role in the conference
        $userConference = $conference->users()->where('users.id', $user->id)->first();


        $user->notify(new ConferenceCreatedNotification($conference, $user));

        return response()->json(['conference' => $conference,'user role in conference' => $userConference ], 201);
    }

    public function update(Request $request, $id)
    {
        // Find the conference
        $conference = Conference::findOrFail($id);

        $request->validate([
            'email' => ['email', new AcademicEmail],
            'title' => 'string',
            'acronym' => 'string',
            'city' => 'string',
            'country' => 'string',
            'webpage' => 'url',
            'category' => 'string',
            'start_at' => 'date',
            'end_at' => 'date|after:start_at',
            'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'paper_subm_due_date' => 'date|after:start_at|before:end_at',
            'review_due_date' => 'date|after:start_at|before:end_at',
            'register_due_date' => 'date|after:start_at|before:end_at',
            'acceptation_notification' => 'date|after:start_at|before:end_at',

        ]);
       
         
        $conference->update($request->all());


        

       
        return response()->json(['conference' => $conference, 'message' => 'Conference updated successfully'], 200);
    }

    public function ConfereceTopic(Request $request, $id)
{
    $validatedData = $request->validate([
        'topic_id' => 'nullable|exists:topics,id', // Allow topic_id to be nullable for creating new topics
        'topic_name' => 'required|string|max:255',
        'subtopics.*' => 'string|max:255',
    ]);

    // If topic_id is provided, attempt to update the existing topic; otherwise, create a new topic
    if ($validatedData['topic_id']) {
        // Topic exists, update it
        $topic = Topic::findOrFail($validatedData['topic_id']);
        $topic->name = $validatedData['topic_name'];
        $topic->save();
    } else {
        // Topic doesn't exist, create a new one
        $topic = new Topic();
        $topic->name = $validatedData['topic_name'];
        $topic->conference_id = $id;
        $topic->save();
    }

    // Update or create subtopics for the topic
    $subtopics = [];
    foreach ($validatedData['subtopics'] as $subtopicName) {
        $subtopic = Subtopic::updateOrCreate(
            ['topic_id' => $topic->id, 'name' => $subtopicName],
            ['topic_id' => $topic->id] // Ensure topic_id is set correctly
        );
        $subtopics[] = $subtopic;
    }

    // Return success response
    return response()->json([
        'message' => 'Topic and subtopics updated/created successfully',
        'topic' => $topic,
        'subtopics' => $subtopics,
    ]);
}
             
            
    
            
      

    public function inviteReviewers(Request $request)
    {
        // Validate request data
        $request->validate([
            'file' => 'nullable|file|mimes:xlsx,xls', // Allow file to be nullable
            'emails' => 'nullable|array', // Allow emails to be nullable
            'emails.*' => 'email',
        ]);

        // Process Excel file if provided
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $import = new EmailsImport(); // Create instance of Excel import class
            Excel::import($import, $file); // Execute import process

            // Get email addresses from the import
            $emails = $import->getEmails();
        } else {
            $emails = $request->input('emails', []); // Get emails directly from request
        }

        // Send invitations and store in database
        foreach ($emails as $email) {
            $invitation = Invitation::create([
                'email' => $email,

            ]);

            // Send email invitation
            Mail::to($email)->send(new ReviewerInvitation($invitation));
        }

        return response()->json(['message' => ' Reiviewers Invitations sent successfully']);
    }

    public function notAccepted()
    {
        $conferences = Conference::where('is_accept', 2)
            ->where('is_verified', 1)
            ->get();

        return response()->json($conferences);
    }
    public function accept(Request $request, $id)
    {
        // Get the conference
        $conference = Conference::findOrFail($id);

        // Check if the user is authenticated
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Check if the user has the role of "admin"
        if ($request->user()->roles->contains('name', 'admin')) {
            // Update the accept column to 1
            $conference->is_accept = 1;
            $conference->save();

            return response()->json(['message' => 'Conference accepted successfully'], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 403);

    }


    public function Confirmation(Request $request, $id)
    {
        // Get the conference
        $conference = Conference::findOrFail($id);
            $conference->is_verified = 1;
            $conference->save();
         return response()->json(['message' => 'Conference accepted successfully'], 200);
    }
    public function reject(Request $request, $id)
    {
        // Get the conference
        $conference = Conference::findOrFail($id);

        // Check if the user is authenticated
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Check if the user has the role of "admin"
        if ($request->user()->roles->contains('name', 'admin')) {
            // Update the accept column to 1
            $conference->is_accept = 0;
            $conference->save();

            return response()->json(['message' => 'Conference rejected  successfully'], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 403);

    }


    public function userConferences(Request $request)
    {
        // Retrieve the authenticated user
        $user = $request->user();
        
        // Retrieve conferences for the user along with their role
        $conferences = $user->conferences()->withPivot('role')->get();
        
        return response()->json($conferences);
    }
    
    public function acceptationsSetting($id, Request $request)
{
    // Validate the input
    $validated = $request->validate([
        'oralPresentations' => 'required|integer|min:0',
        'poster' => 'required|integer|min:0',
        'waitingList' => 'required|integer|min:0',
    ]);

    // Check if counts have already been submitted for this conference
    $existingSetting = AcceptationsSetting::where('conference_id', $id)->first();

    if ($existingSetting) {
        // Retrieve the conference associated with the acceptance setting
        $conference = Conference::findOrFail($id);

        // Check if the conference has ended
        $conferenceEndDate = Carbon::parse($conference->end_at);
        $currentDate = Carbon::now();
        if ($currentDate->gte($conferenceEndDate)) {
            return response()->json(['message' => 'The conference has ended. You can no longer edit the acceptance counts.'], 403);
        }

        // Update the existing acceptance setting with the new data
        $existingSetting->oral_presentations = $validated['oralPresentations'];
        $existingSetting->poster = $validated['poster'];
        $existingSetting->waiting_list = $validated['waitingList'];
        $existingSetting->save();

        return response()->json(['message' => 'Acceptance counts updated successfully.'], 200);
    } else {
        // Save the counts to the database as a new record
        $setting = new AcceptationsSetting();
        $setting->conference_id = $id;
        $setting->oral_presentations = $validated['oralPresentations'];
        $setting->poster = $validated['poster'];
        $setting->waiting_list = $validated['waitingList'];
        $setting->save();

        return response()->json(['message' => 'Counts submitted successfully.'], 200);
    }
}

    public function getAcceptanceInfo($conferenceId)
    {
        // Assuming AcceptationsSetting is the model representing acceptance information
        $acceptanceInfo = AcceptationsSetting::where('conference_id', $conferenceId)->first();

        if ($acceptanceInfo) {
            return response()->json($acceptanceInfo);
        } else {
            return response()->json(['message' => 'Acceptance information not found for the conference.'], 404);
        }
    }

}