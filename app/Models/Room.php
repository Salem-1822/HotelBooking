<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'room_number',
        'type',
        'price_per_night',
        'capacity',
        'bed_type',
        'floor',
        'size',
        'description',
        'main_image',
        'gallery_images',
        'status',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'price_per_night' => 'decimal:2',
        'size' => 'decimal:2',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Resolved main image URL with fallback.
     */
    public function getMainImageUrlAttribute(): string
    {
        if (!empty($this->main_image)) {
            if (\Illuminate\Support\Str::startsWith($this->main_image, ['http://', 'https://'])) {
                return $this->main_image;
            }

            return asset('storage/' . ltrim($this->main_image, '/'));
        }

        return 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800&h=600&fit=crop';
    }

    /**
     * Safe deletion of room images from public disk.
     */
    public function deleteImageFiles(): void
    {
        if ($this->main_image && !\Illuminate\Support\Str::startsWith($this->main_image, ['http://', 'https://'])) {
            $path = ltrim($this->main_image, '/');
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
        }
    }
}
