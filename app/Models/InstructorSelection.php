<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstructorSelection extends Model
{
    protected $fillable = [
        'user_id',
        'staff_id',
        'academic_year_id',
        'staff_type',
        'selection_stage',
        'selection_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
