<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Conference;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConferenceCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $conference;

    public function __construct(User $user, Conference $conference)
    {
        $this->user = $user;
        $this->conference = $conference;
    }

    public function build()
    {
        return $this->view('emails.conference_created')
                    ->subject('Conference Created');
    }

}
