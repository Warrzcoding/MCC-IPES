<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IdChecker extends Model
{
    protected $table = 'idchecker';
    
    protected $fillable = [
        'id_number',
        'fname',
        'mname',
        'lname',
        'course',
        'year',
        'section',
        'gender'
    ];
    
    public $timestamps = false;
}
