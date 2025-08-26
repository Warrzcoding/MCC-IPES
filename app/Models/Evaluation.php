<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    public $timestamps = false;

    protected $fillable = [
        'staff_id',
        'question_id',
        'response',
        'user_id',
        'comments',
        'response_score',
        'academic_year_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'response_score' => 'integer'
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 