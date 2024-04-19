<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Conference;
use App\Models\Role;
use App\Models\UserRoleInConference;
use Illuminate\Validation\Rule;

class ConferenceController extends Controller
{
    public function create(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Get the authenticated user
        $user = $request->user();

        // Create a new conference
        $conference = Conference::create([
            'name' => $request->input('name'),
            'location' => $request->input('location'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'userID' => $user->id,
        ]);

        // Assign the chair role to the authenticated user
        $chairRole = Role::where('name', 'chair')->firstOrCreate(['name' => 'chair']);
        UserRoleInConference::create([
            'user_id' => $user->id,
            'role_id' => $chairRole->id,
            'conference_id' => $conference->id,
        ]);

        // Return the conference with the chair role assigned
        return response()->json(['conference' => $conference, 'chairRole' => $chairRole], 201);
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
            'location' => 'required|string|max:255',
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
