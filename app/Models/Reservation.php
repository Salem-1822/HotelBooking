<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
