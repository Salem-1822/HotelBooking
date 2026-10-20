<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_id', 'user_id', 'reservation_id', 'rating', 'comment'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    protected static function booted(): void
    {
        $flushCache = function () {
            \Illuminate\Support\Facades\Cache::forget('home_guest_reviews');
        };

        static::saved($flushCache);
        static::deleted($flushCache);
    }
}
