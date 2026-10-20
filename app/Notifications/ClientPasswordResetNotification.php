<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientPasswordResetNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     */
    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('client.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.users.expire', 60);
        $clientName = $notifiable->name ?? 'there';
        $subject = 'Reset Your HotelBooking Password';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.password_reset', [
                'resetUrl'      => $resetUrl,
                'expireMinutes' => $expireMinutes,
                'clientName'    => $clientName,
                'subjectTitle'  => $subject,
            ]);
    }
}
