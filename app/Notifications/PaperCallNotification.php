<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use App\Models\Topic;
use App\Models\Subtopic;

class PaperCallNotification extends Notification
{
    use Queueable;

    public function __construct($conference,$paperCall)
    {
        $this->conference = $conference;
        $this->paperCall = $paperCall; // Assign the value to the $paperCall property


    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        \Log::info('Conference object: ', ['conference' => $this->conference]);

        $topics = Topic::where('conference_id', $this->conference->id)->get();
        $subtopics = Subtopic::whereIn('topic_id', $topics->pluck('id'))->get();
        $websiteUrl = url('http://localhost:8080/');

        return (new MailMessage)
            ->subject('Paper Call Notification :' .$this->conference->title )
            ->line('Call For Paper')
            ->view(
                'emails.paper_call_notification',
                [
                    'conference' => $this->conference,
                    'topics' => $topics,
                    'subtopics' => $subtopics,
                    'websiteUrl' =>  $websiteUrl, // Pass the website URL to the email template
                ]
            );
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
