<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Password reset notification for Client accounts.
 * Intentionally synchronous (no Queueable/ShouldQueue) so the reset email is
 * delivered immediately even when QUEUE_CONNECTION=database and no worker runs.
 */
class ClientPasswordResetNotification extends Notification
{

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

        $expireMinutes = config('auth.passwords.users.expire') ?? config('auth.passwords.clients.expire') ?? 60;
        $clientName = (!empty($notifiable->name) && trim($notifiable->name) !== '') ? $notifiable->name : 'there';
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
