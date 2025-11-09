<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupLog extends Model
{
    protected $fillable = [
        'job_name',
        'status',
        'storage_path',
        'size_mb',
        'started_at',
        'completed_at',
        'duration_seconds',
        'initiated_by',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'size_mb' => 'decimal:2',
    ];
}
