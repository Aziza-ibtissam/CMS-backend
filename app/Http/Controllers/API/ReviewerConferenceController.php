<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invitations;
use website\Excel\Facades\Excel;
use Illuminate\Support\Facades\Notification;
use App\Notifications\InvitationNotification;
use App\Models\Conference;
use App\Models\User;


class ReviewerConferenceController extends Controller
{
    public function inviteReviewers(Request $request)
    {
        // Validate request data
        $data = $request->validate([
            'conference_id' => 'required|integer',
            'email' => 'sometimes|required|email', // Allow email input (optional)
            'file' => 'sometimes|required|file|mimes:xlsx,xls', // Allow file upload (optional)
            'firstName' => 'sometimes|required|string',
            'lastName' => 'sometimes|required|string',
            'affiliation' => 'sometimes|required|string',
        ]);

        // If a user with the given email already exists, use their first and last names instead of the provided ones
        if ($request->has('email')) {
            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $data['firstName'] = $user->firstName;
                $data['lastName'] = $user->lastName;
            }
        }

        // Find the conference by ID
        $conference = Conference::findOrFail($request->conference_id);

        // Initialize an empty array to store email addresses
        $emails = [];

        // Check if email is provided directly in the request
        if ($request->has('email')) {
            // If email is provided, add it to the emails array
            $emails[] = $request->email;
        }

        // Check if file is provided in the request
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $import = new EmailsImport(); // Create instance of Excel import class
            Excel::import($import, $file); // Execute import process

            // Get email addresses from the import
            $emailsFromFile = $import->getEmails();

            // Merge email addresses obtained from file with existing emails array
            $emails = array_merge($emails, $emailsFromFile);
        }

        // Validate if at least one email address is provided
        if (empty($emails)) {
            return response()->json(['error' => 'No email addresses provided'], 422);
        }

        // Send invitations and store in database
        foreach ($emails as $email) {
            // Create a new invitation record
            $invitation = new Invitations([
                'conference_id' => $conference->id,
                'email' => $email,
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'affiliation' => $data['affiliation'],
            ]);
            
            $invitation->save();

            // Send email invitation
            Notification::route('mail', $email)
            ->notify(new InvitationNotification($invitation,$conference,$data['firstName'],$data['lastName']));        }
        return response()->json(['message' => 'Invitations sent successfully']);
    }

    public function acceptInvitation(Request $request ,$id)
{
    $invitation = Invitations::findOrFail($id);
    $invitation->invitationStatus = 'accepted';
    $invitation->reviewerTopic = $request->input('expertise');
    $invitation->save();

    // Search for user by email
    $user = User::where('email', $invitation->email)->first();

    if ($user) {
        // Update user's role to 'reviewer' for the corresponding conference
        $conference = Conference::findOrFail($invitation->conference_id); // Assuming there's a conference_id field in the invitations table
        $user->conferences()->updateExistingPivot($conference->id, ['role' => 'reviewer']);
        
        // You can add additional logic here, like redirecting the user or sending a confirmation email
        
        return response()->json(['success' => true,'message' => 'Invitation accepted successfully'],200);
    } else {
        // Handle case where user with provided email is not found
        return response()->json(['error' => 'User with provided email not found'], 404);
    }
}
    public function show()
    {
        return view('accept_invitation');
    }

    public function declineInvitation($id)
    {
        $invitation = Invitations::findOrFail($id);
        $invitation->invitationStatus = 'declined';
        $invitation->save();

        // You can add additional logic here, like redirecting the user or sending a confirmation email

        return response()->json(['message' => 'Invitation declined successfully']);
    }

    public function showInvitations($conferenceId)
    {
        // Fetch invitations for the specified conference ID
        $invitations = Invitations::where('conference_id', $conferenceId)->get();

        // Check if invitations exist for the specified conference ID
        if ($invitations->isEmpty()) {
            // If no invitations found, return a response indicating no invitations exist
            return response()->json(['message' => 'No invitations found for the specified conference ID'], 404);
        }

        // Return the invitations as JSON response
        return response()->json($invitations);
    }
}