<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'city_id',
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

    /**
     * Scope a query to only include visible (non-system) admins.
     */
    public function scopeVisibleAdmins($query)
    {
        return $query->whereNotIn('role', ['super_admin', 'city_admin']);
    }
}
