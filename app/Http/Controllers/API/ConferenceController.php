<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conference;
use App\Models\Role;
use App\Rules\AcademicEmail;
use App\Models\UserRoleInConference;
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
        $conference->logo_url = Storage::url($conference->logo);
        return $conference;
    });
    
    return response()->json($conferences);
}
public function show($id)
    {
        $conferences =Conferences::find($id);

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
            'paper_subm_date' => 'required|date|after:start_at',
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
            'paper_subm_date' => $request->input('paper_subm_date'),
            'logo' => $request->file('logo')->store('conferences', 'public'),

        ]);

        // Assign the chair role to the authenticated user
        $user = Auth::user();

        $user->conferences()->attach($conference->id, ['role' => 'chair']);

        // Retrieve the user with their role in the conference
        $userConference = $conference->users()->where('users.id', $user->id)->first();


        Mail::to($request->input('email'))->send(new ConferenceCreatedMail($user, $conference));
        $user->update(['is_verified' => 1]);
        return response()->json(['conference' => $conference,'user role in conference' => $userConference ], 201);
    }




    public function notAccepted()
    {
        // Retrieve conferences where accept column is 0
        $conferences = Conference::where('is_accept', 0)->get();
        #$userConference = $conferences->users()->get();
        
        return response()->json($conferences );
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
            $conference->is_accept = 2;
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


    public function submit(Request $request)
    {
        // Validate the request data
        $request->validate([
            'conference_id' => 'required|exists:conferences,id',
        ]);

        // Get the authenticated user
        $user = $request->user();

        // Get the conference
        $conference = Conference::findOrFail($request->input('conference_id'));

        // Assign the reviewer role to the authenticated user
        $reviewerRole = Role::where('name', 'reviewer')->firstOrCreate(['name' => 'reviewer']);
        UserRoleInConference::create([
            'user_id' => $user->id,
            'role_id' => $reviewerRole->id,
            'conference_id' => $conference->id,
        ]);

        // Return success response
        return response()->json(['message' => 'Conference submitted successfully'], 200);
    }
    public function update(Request $request, $id)
    {
        // Find the conference
        $conference = Conference::findOrFail($id);

        // Check if the user is authorized to update the conference
        if (!Auth::user()->hasRole('admin') && !$conference->isChair(Auth::user()->id)) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Update the conference
        $conference->update($request->all());

        return response()->json(['conference' => $conference, 'message' => 'Conference updated successfully'], 200);
    }

    public function delete($id)
    {
        // Find the conference
        $conference = Conference::findOrFail($id);

        // Check if the user is authorized to delete the conference
        if (!Auth::user()->hasRole('admin') && !$conference->isChair(Auth::user()->id)) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        // Delete the conference
        $conference->delete();

        return response()->json(['message' => 'Conference deleted successfully'], 200);
    }
}
