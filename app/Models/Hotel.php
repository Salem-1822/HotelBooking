<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        // Core fields (existing — unchanged)
        'city_id',
        'admin_id',
        'name',
        'description',
        'address',
        'price_per_night',
        'image',   // cover image (existing column, reused)
        'status',

        // Profile fields added via migration
        'phone',
        'email',
        'stars',
        'gallery_images',
        'amenities',
        'check_in_time',
        'check_out_time',
        'cancellation_policy',
        'children_policy',
        'pets_policy',
        'smoking_policy',
        'latitude',
        'longitude',
        // Note: 'logo', 'website', 'category' columns exist in DB but are not
        // exposed in the UI per project requirements. Left nullable in the schema.
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'amenities'      => 'array',
        'stars'          => 'integer',
        'latitude'       => 'float',
        'longitude'      => 'float',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
