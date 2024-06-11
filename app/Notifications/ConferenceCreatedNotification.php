<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConferenceCreatedNotification extends Notification
{
    use Queueable;
    public $confirmedUrl;

    protected $conference;
    protected $user;

    public function __construct($conference, $user)
    {
        $this->conference = $conference;
        $this->user = $user;
        $this->confirmedUrl = url('http://localhost:8080/conference/confirmed/' . $conference->id);

    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Conference Created')
                    ->markdown('emails.conference_created', [
                        'conference' => $this->conference,
                        'user' => $this->user,
                        'confirmedUrl' => $this->confirmedUrl,

                    ]);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
