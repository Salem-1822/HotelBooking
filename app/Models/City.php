<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image'];

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    protected static function booted(): void
    {
        $flushCache = function () {
            \Illuminate\Support\Facades\Cache::forget('home_cities_with_counts');
            \Illuminate\Support\Facades\Cache::forget('home_platform_stats');
            \Illuminate\Support\Facades\Cache::forget('active_cities_with_hotel_count');
        };

        static::saved($flushCache);
        static::deleted($flushCache);
    }
}
