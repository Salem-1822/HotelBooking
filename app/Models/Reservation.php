<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = ['hotel_id', 'guest_name', 'guest_phone', 'guests_count', 'check_in', 'check_out', 'total_price', 'status'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
