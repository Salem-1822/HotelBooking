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

    /**
     * Accessor for resolved image URL with fallback.
     */
    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (\Illuminate\Support\Str::startsWith($this->image, ['http://', 'https://'])) {
                return $this->image;
            }

            return asset('storage/' . ltrim($this->image, '/'));
        }

        return 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?w=600&h=400&fit=crop';
    }

    /**
     * Safe deletion of city image file from public disk.
     */
    public function deleteImageFile(): void
    {
        if ($this->image && !\Illuminate\Support\Str::startsWith($this->image, ['http://', 'https://'])) {
            $path = ltrim($this->image, '/');
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
        }
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
