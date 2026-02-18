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
        'selection_count',
        'is_locked'
    ];

    protected $casts = [
        'is_locked' => 'boolean',
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

    /**
     * Save or update instructor selections for a user in an academic year
     */
    public static function saveSelection($userId, $academicYearId, $staffIds, $staffType, $isLocked = false)
    {
        // Clear existing selections for this user and academic year if saving new ones or locking
        // If locking, we want to replace whatever was there before to avoid duplicates or orphans
        self::where('user_id', $userId)
            ->where('academic_year_id', $academicYearId)
            ->where('staff_type', $staffType)
            ->delete();

        // Create new records for each staff
        foreach ($staffIds as $staffId) {
            self::create([
                'user_id' => $userId,
                'staff_id' => $staffId,
                'academic_year_id' => $academicYearId,
                'staff_type' => $staffType,
                'selection_stage' => $isLocked ? 'locked' : 'selection',
                'is_locked' => $isLocked,
            ]);
        }
    }

    /**
     * Get locked selections for a user in an academic year
     */
    public static function getLockedSelection($userId, $academicYearId)
    {
        return self::where('user_id', $userId)
            ->where('academic_year_id', $academicYearId)
            ->where(function($q) {
                $q->where('is_locked', true)
                  ->orWhere('is_locked', 1)
                  ->orWhere('selection_stage', 'locked');
            })
            ->with('staff')
            ->get();
    }

    /**
     * Check if user has locked selection for academic year
     */
    public static function hasLockedSelection($userId, $academicYearId)
    {
        if (!$academicYearId) {
            // If no AY provided, check if ANY locked selection exists for this user
            return self::where('user_id', $userId)
                ->where(function($q) {
                    $q->where('is_locked', true)
                      ->orWhere('is_locked', 1)
                      ->orWhere('selection_stage', 'locked');
                })
                ->exists();
        }

        return self::where('user_id', $userId)
            ->where('academic_year_id', $academicYearId)
            ->where(function($q) {
                $q->where('is_locked', true)
                  ->orWhere('is_locked', 1)
                  ->orWhere('selection_stage', 'locked');
            })
            ->exists();
    }

    /**
     * Get locked selection grouped by staff type
     */
    public static function getLockedSelectionByType($userId, $academicYearId)
    {
        $locked = self::getLockedSelection($userId, $academicYearId);
        
        return [
            'teaching' => $locked->where('staff_type', 'teaching')->values(),
            'non-teaching' => $locked->where('staff_type', 'non-teaching')->values(),
        ];
    }

    /**
     * Lock selections for a user
     */
    public static function lockSelection($userId, $academicYearId)
    {
        return self::where('user_id', $userId)
            ->where('academic_year_id', $academicYearId)
            ->update([
                'is_locked' => true,
                'selection_stage' => 'locked',
            ]);
    }

    /**
     * Clear selections for a user (for edit)
     */
    public static function clearSelection($userId, $academicYearId)
    {
        return self::where('user_id', $userId)
            ->where('academic_year_id', $academicYearId)
            ->delete();
    }
}
