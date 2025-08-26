<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'staff_id',
        'full_name',
        'email',
        'phone',
        'department',
        'staff_type',
        'image_path',
        'profile_image'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Helper method to get the image URL
    public function getImageUrlAttribute()
    {
        if ($this->image_path && file_exists(public_path($this->image_path))) {
            return asset($this->image_path);
        }
        return null;
    }

    // Helper method to get the first letter of the name for avatar
    public function getInitialAttribute()
    {
        return strtoupper(substr($this->full_name, 0, 1));
    }
} 