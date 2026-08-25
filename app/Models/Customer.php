<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['hotel_id', 'name', 'phone', 'email'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Normalize phone numbers to a consistent format.
     */
    public static function normalizePhone($phone)
    {
        // Remove all non-numeric characters except +
        $normalized = preg_replace('/[^\d+]/', '', $phone);
        
        // If it starts with +212, convert to 0 (Assuming Moroccan numbers as per example)
        if (str_starts_with($normalized, '+212')) {
            $normalized = '0' . substr($normalized, 4);
        }
        
        // If it starts with 212, convert to 0
        if (str_starts_with($normalized, '212') && strlen($normalized) == 12) {
            $normalized = '0' . substr($normalized, 3);
        }

        return $normalized;
    }
}
