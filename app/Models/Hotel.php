<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = ['city_id', 'admin_id', 'name', 'description', 'address', 'image', 'status'];
    
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
