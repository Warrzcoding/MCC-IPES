<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedQuestion extends Model
{
    protected $fillable = [
        'academic_year_id', 'title', 'description', 'staff_type', 'response_type'
    ];
}
