<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Password reset notification for Super Admin accounts (role: super_admin).
 * Sends the reset link to the Super Admin-specific reset route (super_admin.password.reset).
 * Intentionally synchronous (no Queueable/ShouldQueue) so the reset email is
 * delivered immediately even when QUEUE_CONNECTION=database and no worker runs.
 */
class SuperAdminPasswordResetNotification extends Notification
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
     * Uses the shared password_reset view but points specifically to the
     * Super Admin reset password route.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('super_admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.super_admins.expire', 60);
        $adminName     = $notifiable->name ?? 'Super Admin';
        $subject       = 'Reset Your HotelBooking Super Admin Password';

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
