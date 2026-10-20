<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ReservationStatusChangedNotification;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_id', 'room_id', 'user_id', 'customer_id', 'guest_name', 'guest_phone', 'guests_count', 'check_in', 'check_out', 'total_price', 'status'];

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

        static::updated(function ($reservation) {
            // Check if status has changed
            if ($reservation->wasChanged('status')) {
                $reservation->sendStatusChangeNotification();
            }
        });
    }

    /**
     * Send email notification to client when status changes.
     */
    public function sendStatusChangeNotification(): void
    {
        // Don't send notification for pending status or if notifiable recipient cannot be resolved
        if (!in_array($this->status, ['confirmed', 'checked_in', 'checked_out', 'cancelled'])) {
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

            if ($recipient) {
                $recipient->notify(new ReservationStatusChangedNotification($this));
            }
        } catch (\Throwable $e) {
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
