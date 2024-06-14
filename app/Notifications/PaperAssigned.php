<?php

// app/Notifications/PaperAssigned.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Paper;

class PaperAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $paper;

    /**
     * Create a new notification instance.
     *
     * @param Paper $paper
     */
    public function __construct(Paper $paper)
    {
        $this->paper = $paper;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('New Paper Assignment')
                    ->greeting('Hello ' .  $notifiable->firstName . ' ' . $notifiable->lastName )
                    ->line('You have been assigned to review the paper: ' . $this->paper->paperTitle)
                    ->action('View Paper', url('/papers/' . $this->paper->id))
                    ->line('Thank you for your contribution!');
    }
}

