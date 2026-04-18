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
}
