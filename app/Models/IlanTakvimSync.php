<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IlanTakvimSync extends Model
{
    use HasFactory;

    protected $table = 'ilan_takvim_sync';

    protected $fillable = [
        'ilan_id',
        'platform',
        'external_calendar_id',
        'external_listing_id',
        'sync_enabled',
        'auto_sync',
        'last_sync_at',
        'next_sync_at',
        'sync_interval_minutes',
        'sync_settings',
        'api_key',
        'api_secret',
        'sync_status',
        'last_error',
        'last_error_at',
        'sync_count',
        'error_count',
    ];

    protected $casts = [
        'sync_enabled' => 'boolean',
        'auto_sync' => 'boolean',
        'last_sync_at' => 'datetime',
        'next_sync_at' => 'datetime',
        'last_error_at' => 'datetime',
        'sync_settings' => 'array',
        'sync_interval_minutes' => 'integer',
        'sync_count' => 'integer',
        'error_count' => 'integer',
    ];

    public function ilan()
    {
        return $this->belongsTo(Ilan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('sync_enabled', true)
            ->where('sync_status', 'active');
    }

    public function scopePlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeNeedsSync($query)
    {
        return $query->where('sync_enabled', true)
            ->where(function ($q) {
                $q->whereNull('next_sync_at')
                    ->orWhere('next_sync_at', '<=', now());
            });
    }

    public function markAsSynced()
    {
        $this->update([
            'last_sync_at' => now(),
            'next_sync_at' => now()->addMinutes($this->sync_interval_minutes),
            'sync_status' => 'active',
            'last_error' => null,
            'error_count' => 0,
            'sync_count' => $this->sync_count + 1,
        ]);
    }

    public function markAsFailed($error)
    {
        $this->update([
            'sync_status' => 'failed',
            'last_error' => $error,
            'last_error_at' => now(),
            'error_count' => $this->error_count + 1,
        ]);
    }
}
