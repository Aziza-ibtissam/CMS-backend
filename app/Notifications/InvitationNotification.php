<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Topic;
use App\Models\Subtopic;

class InvitationNotification extends Notification
{
    use Queueable;

    public $invitation;
    public $acceptUrl;
    public $declineUrl;
    public $firstName; // Add first name property
    public $lastName; // Add last name property

    public function __construct($invitation , $conference, $firstName, $lastName)
    {
        $this->invitation = $invitation;
        $this->conference = $conference;
        $this->acceptUrl = url('http://localhost:8080/invitation/accept/' . $conference->id. '/' . $invitation->id );
        $this->declineUrl = url('http://localhost:8080/invitation/decline/' . $conference->id. '/' . $invitation->id  );
        $this->firstName = $firstName; // Assign first name
        $this->lastName = $lastName; // Assign last name
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $topics = Topic::where('conference_id', $this->conference->id)->get();
        $subtopics = Subtopic::whereIn('topic_id', $topics->pluck('id'))->get();
        return (new MailMessage)
            ->subject('Invitation to Review Conference: ' . $this->conference->title)
            ->view('emails.reviewers_invitation', [
                'conference' => $this->conference,
                'firstName' => $this->firstName, // Pass first name to the view
                'lastName' => $this->lastName, // Pass last name to the view
                'acceptUrl' => $this->acceptUrl,
                'declineUrl' => $this->declineUrl,
                'topics' => $topics,
                'subtopics' => $subtopics
            ]);
    }
}
