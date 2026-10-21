<?php

namespace App\Models;

use App\Notifications\AdminPasswordResetNotification;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profile_image',
        'role',
        'city_id',
        'hotel_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            // No automated hashing to avoid double-hashing with manual Hash::make calls
        ];
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Send the password reset notification to the admin.
     * Overrides the default to use an Admin-specific notification with the
     * correct reset URL (admin.password.reset route).
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new AdminPasswordResetNotification($token));
    }

    /**
     * Scope a query to only include visible (non-system) admins.
     */
    public function scopeVisibleAdmins($query)
    {
        return $query->whereNotIn('role', ['super_admin', 'admin']);
    }

    /**
     * Accessor for profile_image_url.
     */
    public function getProfileImageUrlAttribute(): string
    {
        return $this->getProfilePhotoUrl();
    }

    /**
     * Get the resolved profile photo URL with customizable fallback.
     *
     * Uses asset() instead of Storage::disk()->url() so the generated URL
     * respects the actual request host and port (e.g. http://127.0.0.1:8000)
     * rather than the hardcoded APP_URL value.
     */
    public function getProfilePhotoUrl(string $fallbackBackground = '0F172A', string $fallbackColor = 'fff', ?int $size = null): string
    {
        if (!empty($this->profile_image) && $this->profile_image !== '#') {
            if (\Illuminate\Support\Str::startsWith($this->profile_image, ['http://', 'https://'])) {
                return $this->profile_image;
            }

            // Normalise to the full relative public-disk path (profiles/filename.jpg).
            $path = \Illuminate\Support\Str::startsWith($this->profile_image, 'profiles/')
                ? $this->profile_image
                : 'profiles/' . $this->profile_image;

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                // asset() builds the URL from the current request context,
                // so it correctly includes the development server port.
                return asset('storage/' . $path);
            }

            // Legacy fallback: bare filename stored without the profiles/ prefix
            // and the file might also sit at the root of the public disk.
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_image)) {
                return asset('storage/' . $this->profile_image);
            }
        }

        $sizeParam = $size ? '&size=' . $size : '';
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=' . $fallbackBackground . '&color=' . $fallbackColor . $sizeParam;
    }

    /**
     * Check if the admin has a usable profile photo.
     */
    public function hasProfilePhoto(): bool
    {
        if (empty($this->profile_image) || $this->profile_image === '#') {
            return false;
        }

        if (\Illuminate\Support\Str::startsWith($this->profile_image, ['http://', 'https://'])) {
            return true;
        }

        $path = \Illuminate\Support\Str::startsWith($this->profile_image, 'profiles/')
            ? $this->profile_image
            : 'profiles/' . $this->profile_image;

        return \Illuminate\Support\Facades\Storage::disk('public')->exists($path)
            || \Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_image);
    }

    /**
     * Delete the profile image file safely from the public disk.
     */
    public function deleteProfileImageFile(): void
    {
        if ($this->profile_image && !\Illuminate\Support\Str::startsWith($this->profile_image, ['http://', 'https://']) && $this->profile_image !== '#') {
            $path = \Illuminate\Support\Str::startsWith($this->profile_image, 'profiles/')
                ? $this->profile_image
                : 'profiles/' . $this->profile_image;

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            } elseif (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->profile_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($this->profile_image);
            }
        }
    }
}
