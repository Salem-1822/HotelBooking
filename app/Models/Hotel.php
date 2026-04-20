<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    // price_per_night is already included in $fillable to allow mass assignment
    protected $fillable = ['city_id', 'admin_id', 'name', 'description', 'address', 'price_per_night', 'image', 'status'];
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
