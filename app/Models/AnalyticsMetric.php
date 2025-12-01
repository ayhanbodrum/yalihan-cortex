<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'metric_type',
        'metric_name',
        'metric_data',
        'metric_value',
        'source',
        'severity',
        'recorded_at',
    ];

    protected $casts = [
        'metric_data' => 'array',
        'metric_value' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    // Scopes for easy querying
    public function scopeByType($query, $type)
    {
        return $query->where('metric_type', $type);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('recorded_at', '>=', now()->subHours($hours));
    }

    public function scopeToday($query)
    {
        return $query->whereDate('recorded_at', today());
    }
}
