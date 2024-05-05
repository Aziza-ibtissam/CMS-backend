<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\PaperCall;
use App\Models\Conference;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaperCallNotification;
use Illuminate\Support\Facades\Auth;

class PaperCallController extends Controller
{
    public function sendEmails(Request $request)
    {
        $emails = $request->input('selectedUserEmails');
        $conferenceId = $request->input('conference_id');

        // Fetch conference details
        $conference = Conference::findOrFail($conferenceId);
        $authUser = Auth::user();

        // Fetch users where userId matches authId
        $user= $conference->users()->where('user_id', $authUser->id)->get();
        $emailAuther = json_encode($emails);
        \Log::info('selectedUserEmails: ' . $user);

        // Store emails in the database
        $paperCall = PaperCall::create([
            'conference_id' => $conferenceId,
            'emailAuther' => $emailAuther,
            'callstart_at' => $conference->start_at,
            'callend_at' => $conference->paper_subm_due_date,
        ]);
    
        // Send notifications to each user
        Notification::route('mail', $emails)->notify(new PaperCallNotification($conference, $paperCall));


        return response()->json(['message' => 'Invitations sent successfully'], 200);
    }
}
