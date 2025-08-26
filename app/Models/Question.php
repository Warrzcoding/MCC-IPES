<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $table = 'questions';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'description',
        'staff_type',
        'response_type',
        'academic_year_id',
        'is_open'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'is_open' => 'boolean'
    ];

    // Relationships
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    // Scopes
    public function scopeForStaffType($query, $staffType)
    {
        return $query->where('staff_type', $staffType);
    }

    public function scopeOpen($query)
    {
        return $query->where('is_open', true);
    }
} 