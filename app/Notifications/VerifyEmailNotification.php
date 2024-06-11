<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class VerifyEmailNotification extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = URL::signedRoute('verification.verify', ['id' => $notifiable->id]);

        return (new MailMessage)
                    ->subject('Confirm Your Email Address for Conference Management System (ConfMan)')
                    ->greeting('Hello!')
                    ->line('Welcome to our Conference Management System (ConfMan) platform. Thank you for registering with us.')
                    ->line('Please confirm your email address to complete your registration process.')
                    ->action('Confirm Email Address', $verificationUrl)
                    ->line('If you did not create an account, no further action is required.');
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60))
        );
    }
}
