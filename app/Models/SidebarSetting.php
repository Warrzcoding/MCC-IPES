<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SidebarSetting extends Model
{
    protected $fillable = [
        'admin_id',
        'disabled_features',
    ];

    protected $casts = [
        'disabled_features' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get disabled features for a specific admin
     */
    public static function getDisabledFeaturesForAdmin(int $adminId): array
    {
        $setting = self::where('admin_id', $adminId)->first();
        return $setting ? $setting->disabled_features : [];
    }

    /**
     * Set disabled features for a specific admin
     */
    public static function setDisabledFeaturesForAdmin(int $adminId, array $features): void
    {
        self::updateOrCreate(
            ['admin_id' => $adminId],
            ['disabled_features' => $features]
        );
    }
}
