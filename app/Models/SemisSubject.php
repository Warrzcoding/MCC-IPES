<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemisSubject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'semester',
        'department', 
        'year',
        'subcode',
        'subname',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'semester' => 'string',
        'department' => 'string',
        'year' => 'string',
    ];

    /**
     * Get all available departments.
     *
     * @return array
     */
    public static function getDepartments(): array
    {
        return ['BSIT', 'BSBA', 'BSHM', 'BSED', 'BEED'];
    }

    /**
     * Get all available years.
     *
     * @return array
     */
    public static function getYears(): array
    {
        return ['1ST YEAR', '2ND YEAR', '3RD YEAR', '4TH YEAR'];
    }

    /**
     * Get all available semesters.
     *
     * @return array
     */
    public static function getSemesters(): array
    {
        return ['1', '2'];
    }
}
