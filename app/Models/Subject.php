<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'sub_code',
        'sub_name', 
        'sub_department',
        'sub_year',
        'section',
        'semester',
        'assign_instructor',
        'subject_type'
    ];

    protected $table = 'subjects';

    // Since assign_instructor now stores the full name directly, 
    // we don't need a relationship anymore
    // The instructor name can be accessed directly via $subject->assign_instructor
}
