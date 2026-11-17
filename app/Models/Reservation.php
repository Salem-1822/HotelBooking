<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReservationStatusChangedNotification;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_id', 'room_id', 'user_id', 'customer_id', 'guest_name', 'guest_phone', 'guests_count', 'check_in', 'check_out', 'total_price', 'status'];

    /**
     * Whether status change notifications should wait for active database transactions to commit.
     * In production, always true whenever DB::transactionLevel() > 0.
     * Can be explicitly forced in automated tests to verify rollback safety.
     */
    public static bool $dispatchAfterCommit = true;
    public static bool $forceAfterCommitTesting = false;

    protected static function booted()
    {
        static::saving(function ($reservation) {
            // Synchronize compatibility fields from the selected customer
            if ($reservation->customer_id) {
                // We use the relation to ensure we have the most up-to-date data
                // without triggering another query if it's already loaded
                $customer = $reservation->customer ?? Customer::find($reservation->customer_id);
                if ($customer) {
                    $reservation->guest_name = $customer->name;
                    $reservation->guest_phone = $customer->phone;
                }
            }
        });

        static::created(function ($reservation) {
            if (in_array($reservation->status, ['confirmed', 'checked_in', 'checked_out', 'cancelled'])) {
                $dispatch = function () use ($reservation) {
                    $reservation->sendStatusChangeNotification(null);
                };

                $shouldUseAfterCommit = static::$dispatchAfterCommit && (!app()->runningUnitTests() || static::$forceAfterCommitTesting);

                if ($shouldUseAfterCommit && DB::transactionLevel() > 0) {
                    DB::afterCommit($dispatch);
                } else {
                    $dispatch();
                }
            }
        });

        static::updated(function ($reservation) {
            $oldStatus = $reservation->getOriginal('status');
            $newStatus = $reservation->status;

            if ($reservation->wasChanged('status') && $oldStatus !== $newStatus) {
                $dispatch = function () use ($reservation, $oldStatus) {
                    $reservation->sendStatusChangeNotification($oldStatus);
                };

                $shouldUseAfterCommit = static::$dispatchAfterCommit && (!app()->runningUnitTests() || static::$forceAfterCommitTesting);

                if ($shouldUseAfterCommit && DB::transactionLevel() > 0) {
                    DB::afterCommit($dispatch);
                } else {
                    $dispatch();
                }
            }
        });
    }

    /**
     * Send email notification to client when status changes.
     */
    public function sendStatusChangeNotification(?string $oldStatus = null): void
    {
        // Don't send notification for pending status or if notifiable recipient cannot be resolved
        if (!in_array($this->status, ['confirmed', 'checked_in', 'checked_out', 'cancelled'])) {
            return;
        }

        // Do not send if status has not actually changed
        if ($oldStatus !== null && $oldStatus === $this->status) {
            return;
        }

        // Prevent duplicate notifications for the same status transition
        $dedupKey = "rsv_notif_{$this->id}_{$this->status}";
        if (!\Illuminate\Support\Facades\Cache::add($dedupKey, now()->toIso8601String(), now()->addDays(7))) {
            Log::info("Skipping duplicate notification for reservation ID {$this->id} (status: {$this->status}).");
            return;
        }

        try {
            $recipient = null;

            if ($this->user_id && $this->user) {
                $recipient = $this->user;
            } elseif ($this->customer_id && $this->customer && !empty($this->customer->email)) {
                $recipient = $this->customer;
            } elseif ($this->user_id) {
                $user = User::find($this->user_id);
                if ($user) {
                    $recipient = $user;
                }
            } elseif ($this->customer_id) {
                $customer = Customer::find($this->customer_id);
                if ($customer && !empty($customer->email)) {
                    $recipient = $customer;
                }
            }

            if (!$recipient && $this->customer_id) {
                $customer = $this->customer ?? Customer::find($this->customer_id);
                if ($customer && !empty($customer->email)) {
                    $matchedUser = User::where('email', $customer->email)->where('role', 'client')->first();
                    if ($matchedUser) {
                        $this->forceFill(['user_id' => $matchedUser->id])->saveQuietly();
                        $recipient = $matchedUser;
                    } else {
                        $recipient = $customer;
                    }
                }
            }

            if ($recipient) {
                $recipient->notify(new ReservationStatusChangedNotification($this));
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Cache::forget($dedupKey);

            // An email failure must not undo or throw an exception that breaks a successfully saved reservation status
            Log::error("Failed to send reservation status notification for reservation ID {$this->id}: " . $e->getMessage(), [
                'exception' => $e
            ]);
        }
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
