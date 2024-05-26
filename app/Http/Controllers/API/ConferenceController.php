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
use App\Mail\ConferenceCreatedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
class ConferenceController extends Controller
{


    public function index()
   {
    $conferences = Conference::all()->map(function ($conference) {
        // Assuming 'logo' is the attribute storing the logo file name
        return $conference;
    });

    
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
            'logo' => $request->file('logo')->store('conferences', 'public'),

        ]);
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move('uploads/logos/', $filename);
            $conference->logo = $filename;
        }

        // Assign the chair role to the authenticated user
        $user = Auth::user();

        $user->conferences()->attach($conference->id, ['role' => 'chair']);

        // Retrieve the user with their role in the conference
        $userConference = $conference->users()->where('users.id', $user->id)->first();


        Mail::to($request->input('email'))->send(new ConferenceCreatedMail($user, $conference));
        $user->update(['is_verified' => 1]);
        return response()->json(['conference' => $conference,'user role in conference' => $userConference ], 201);
    }

    public function update(Request $request, $id)
    {
        // Find the conference
        $conference = Conference::findOrFail($id);

        // Check if the user is authorized to update the conference
        // if (!Auth::user()->hasRole('admin') && !$conference->isChair(Auth::user()->id)) {
           //  return response()->json(['error' => 'Unauthorized action.'], 403);
        // }  
        // Validate the request data
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
            'camera_ready_paper' => 'file|mimes:pdf,doc,docx|max:2048', // Adjust the file types and size as needed

        ]);
       
         //$conference->register_due_date = $register_due_date ? $register_due_date : $conference->register_due_date;
        // Update the conference
        $conference->update($request->all());


        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move('uploads/logos/', $filename);
            $conference->logo = $filename;
        }

        if ($request->hasFile('camera_ready_paper')) {
            $file = $request->file('camera_ready_paper');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move('uploads/camera_ready_papers/', $filename);
            $conference->camera_ready_paper = $filename;
        }
        return response()->json(['conference' => $conference, 'message' => 'Conference updated successfully'], 200);
    }

    public function ConfereceTopic(Request $request, $id)
    {
        $validatedData = $request->validate([
            'topic_name' => 'required|string|max:255',
            'subtopics.*' => 'string|max:255', // Validate subtopics only if present
        ]);
    
            // Create the topic
            $topic = new Topic();
            $topic->name = $validatedData['topic_name'];
            $topic->conference_id = $id;
            $topic->save();
    
            // Create subtopics for the topic if they exist
            if (isset($validatedData['subtopics'])) {
                $subtopics = [];
                foreach ($validatedData['subtopics'] as $subtopicName) {
                    $subtopic = new Subtopic();
                    $subtopic->name = $subtopicName;
                    $subtopic->topic_id = $topic->id; // Associate the subtopic with the newly created topic
                    $subtopic->save();
                    $subtopics[] = $subtopic;
                }
            } else {
                // No subtopics provided, set $subtopics to null or an empty array
                $subtopics = null; // Or $subtopics = [];
            }
    
            // Return success response
            return response()->json([
                'message' => 'Topic and subtopics created successfully',
                'topic' => $topic,
                'subtopics' => $subtopics,
            ], 201);
            }  
            
    public function createCallForPaper(Request $request, $conferenceId)
    {
                // Validate the request data
                $request->validate([
                    'body' => 'required|string',
                    'start_at' => 'required|date',
                    'end_at' => 'required|date|after:start_at',
                ]);
            
                // Create the call for paper
                $callForPaper = PaperCall::create([
                    'conference_id' => $conferenceId,
                    'body' => $request->input('body'),
                    'start_at' => $request->input('start_at'),
                    'end_at' => $request->input('end_at'),
                ]);
            
                // Return a response indicating success
                return response()->json(['message' => 'Call for paper created successfully', 'callForPaper' => $callForPaper], 201);
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
        // Retrieve conferences where is_accept column is 2 (assuming 2 means not accepted)
        $conferences = Conference::where('is_accept', 2)->get();
    // Append logo URL to each conference object
    $conferences->transform(function ($conference) {
        // Assuming you have a column named 'logo' in the conferences table
        $conference->logo_url = asset("uploads/logos/{$conference->logo}");
        return $conference;
    });

    // Return JSON response with conferences including logo URLs
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

            return response()->json(['message' => 'Conference accepted successfully'], 200);
        }
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