<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invitations;
use App\Models\Conference;
use App\Models\Topic;
use App\Models\Subtopic;



class ReviewerInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;
    public $conference;
    public $acceptUrl;
    public $declineUrl;
    public $firstName; // Add first name property
    public $lastName; // Add last name property

    public function __construct(Invitations $invitation, Conference $conference, $firstName, $lastName)
    {
        $this->invitation = $invitation;
    $this->conference = $conference;
    $this->acceptUrl = route('invitation.accept', ['id' => $invitation->id]);
    $this->declineUrl = route('invitation.decline', ['id' => $invitation->id]);
    $this->firstName = $firstName; // Assign first name
    $this->lastName = $lastName; // Assign last name
    }

    public function build()
    {
        $topics = Topic::where('conference_id', $this->conference->id)->get();
        $subtopics = Subtopic::whereIn('topic_id', $topics->pluck('id'))->get();

        return $this->view('emails.reviewers_invitation')
                    ->subject('Invitation to Review Conference')
                    ->with([
                        'conference' => $this->conference,
                        'acceptUrl' => $this->acceptUrl,
                        'declineUrl' => $this->declineUrl,
                        'firstName' => $this->firstName, // Pass first name to the view
                        'lastName' => $this->lastName, // Pass last name to the view
                        'topics' => $topics,
                        'subtopics' => $subtopics
                    ]);
    }
}
