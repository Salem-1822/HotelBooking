<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Events\NotificationFailed;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // ── Email Delivery Observability & Safe Audit Logging ────────────────
        Event::listen(MessageSent::class, function (MessageSent $event) {
            try {
                $toAddresses = array_map(function ($addr) {
                    $email = $addr->getAddress();
                    $parts = explode('@', $email, 2);
                    if (count($parts) === 2) {
                        $name = $parts[0];
                        $domain = $parts[1];
                        $len = strlen($name);
                        $masked = $len <= 2 ? $name[0] . '***' : $name[0] . str_repeat('*', min(4, $len - 2)) . $name[$len - 1];
                        return $masked . '@' . $domain;
                    }
                    return '***';
                }, $event->message->getTo());

                $messageId = $event->sent ? $event->sent->getMessageId() : null;

                Log::info('Email successfully dispatched via Brevo SMTP', [
                    'subject'    => $event->message->getSubject(),
                    'recipients' => implode(', ', $toAddresses),
                    'message_id' => $messageId,
                    'timestamp'  => now()->toIso8601String(),
                ]);
            } catch (\Throwable) {
                // Safeguard against logging exceptions interfering with delivery
            }
        });

        Event::listen(NotificationSent::class, function (NotificationSent $event) {
            try {
                if ($event->channel === 'mail') {
                    $role = match (true) {
                        $event->notifiable instanceof \App\Models\Admin    => $event->notifiable->role ?? 'admin',
                        $event->notifiable instanceof \App\Models\User     => $event->notifiable->role ?? 'client',
                        $event->notifiable instanceof \App\Models\Customer => 'customer',
                        default => 'user',
                    };

                    $email = method_exists($event->notifiable, 'routeNotificationFor')
                        ? ($event->notifiable->routeNotificationFor('mail', $event->notification) ?? $event->notifiable->email ?? null)
                        : ($event->notifiable->email ?? null);

                    $parts = $email ? explode('@', $email, 2) : [];
                    $masked = count($parts) === 2 && strlen($parts[0]) > 2
                        ? $parts[0][0] . '***' . substr($parts[0], -1) . '@' . $parts[1]
                        : '***@***';

                    $reservationId = property_exists($event->notification, 'reservation')
                        ? $event->notification->reservation->id
                        : null;

                    Log::info('Notification delivered to mail channel', [
                        'notification'   => class_basename($event->notification),
                        'recipient_role' => $role,
                        'recipient'      => $masked,
                        'reservation_id' => $reservationId,
                        'timestamp'      => now()->toIso8601String(),
                    ]);
                }
            } catch (\Throwable) {
                // Safeguard
            }
        });

        Event::listen(NotificationFailed::class, function (NotificationFailed $event) {
            try {
                if ($event->channel === 'mail') {
                    $role = match (true) {
                        $event->notifiable instanceof \App\Models\Admin    => $event->notifiable->role ?? 'admin',
                        $event->notifiable instanceof \App\Models\User     => $event->notifiable->role ?? 'client',
                        $event->notifiable instanceof \App\Models\Customer => 'customer',
                        default => 'user',
                    };

                    Log::error('Notification delivery failed on mail channel', [
                        'notification'   => class_basename($event->notification),
                        'recipient_role' => $role,
                        'failure_reason' => is_string($event->data) ? $event->data : ($event->data['message'] ?? 'Unknown error'),
                        'timestamp'      => now()->toIso8601String(),
                    ]);
                }
            } catch (\Throwable) {
                // Safeguard
            }
        });
    }
}
