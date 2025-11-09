<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DateTimeInterface;
class SidebarSetting extends Model
{
    protected $fillable = [
        'admin_id',
        'disabled_features',
    ];

    protected $casts = [
        'disabled_features' => 'array',
    ];

    /**
     * Handle JSON decoding errors
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * Custom accessor for disabled_features to handle JSON errors
     */
    public function getDisabledFeaturesAttribute($value)
    {
        try {
            return json_decode($value, true) ?? [];
        } catch (\Exception $e) {
            \Log::error('Error decoding disabled_features JSON: ' . $e->getMessage());
            return [];
        }
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get disabled features for a specific admin
     */
    public static function getDisabledFeaturesForAdmin(int $adminId): array
    {
        try {
            $setting = self::where('admin_id', $adminId)->first();
            return $setting ? $setting->disabled_features : [];
        } catch (\Exception $e) {
            \Log::error('Error in getDisabledFeaturesForAdmin: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Set disabled features for a specific admin
     */
    public static function setDisabledFeaturesForAdmin(int $adminId, array $features): void
    {
        try {
            self::updateOrCreate(
                ['admin_id' => $adminId],
                ['disabled_features' => $features]
            );
        } catch (\Exception $e) {
            \Log::error('Error in setDisabledFeaturesForAdmin: ' . $e->getMessage());
            // Don't throw, just log
        }
    }
}
