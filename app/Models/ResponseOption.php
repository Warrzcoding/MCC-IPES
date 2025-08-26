<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_type',
        'option_value',
        'option_label',
        'option_order'
    ];

    protected $casts = [
        'option_order' => 'integer',
        'created_at' => 'datetime'
    ];

    // Get options for a specific response type
    public static function getOptionsForType($responseType)
    {
        return static::where('response_type', $responseType)
                    ->orderBy('option_order')
                    ->get();
    }

    // Get all response types
    public static function getResponseTypes()
    {
        return static::distinct()->pluck('response_type');
    }
} 