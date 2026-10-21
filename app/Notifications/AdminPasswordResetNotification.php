<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Password reset notification for regular Admin accounts (role: admin).
 * Sends the reset link to the Admin-specific reset route, not the Client route.
 */
class AdminPasswordResetNotification extends Notification
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
     * Uses the same email template as the Client reset, but points to the
     * Admin-specific reset route so the link opens the Admin Reset form.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.admins.expire', 60);
        $adminName     = $notifiable->name ?? 'there';
        $subject       = 'Reset Your HotelBooking Admin Password';

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.password_reset', [
                'resetUrl'      => $resetUrl,
                'expireMinutes' => $expireMinutes,
                'clientName'    => $adminName,
                'subjectTitle'  => $subject,
            ]);
    }
}
