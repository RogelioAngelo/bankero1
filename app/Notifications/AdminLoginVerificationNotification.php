<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AdminLoginVerificationNotification extends Notification
{
    use Queueable;

    protected $signedUrl;

    public function __construct($signedUrl)
    {
        $this->signedUrl = $signedUrl;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Confirm your admin login')
            ->line('A login attempt to your admin account was detected. Click below to confirm this login.')
            ->action('Confirm Login', $this->signedUrl)
            ->line('If you did not initiate this request, ignore this email.');
    }
}
