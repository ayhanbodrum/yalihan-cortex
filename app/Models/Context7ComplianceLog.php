<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Context7ComplianceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'violation_type',
        'violation_description',
        'file_path',
        'line_number',
        'violation_context',
        'auto_fixed',
        'fix_description',
        'severity',
        'source',
        'detected_at',
        'fixed_at',
    ];

    protected $casts = [
        'violation_context' => 'array',
        'auto_fixed' => 'boolean',
        'detected_at' => 'datetime',
        'fixed_at' => 'datetime',
    ];

    // Scopes
    public function scopeByViolationType($query, $type)
    {
        return $query->where('violation_type', $type);
    }

    public function scopeAutoFixed($query, $fixed = true)
    {
        return $query->where('auto_fixed', $fixed);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeUnfixed($query)
    {
        return $query->where('auto_fixed', false)->whereNull('fixed_at');
    }

    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('detected_at', '>=', now()->subHours($hours));
    }

    public function scopeByFile($query, $filePath)
    {
        return $query->where('file_path', 'LIKE', "%{$filePath}%");
    }

    // Accessors
    public function getIsFixedAttribute()
    {
        return $this->auto_fixed || ! is_null($this->fixed_at);
    }

    public function getFixDurationAttribute()
    {
        if ($this->fixed_at && $this->detected_at) {
            return $this->detected_at->diffInMinutes($this->fixed_at);
        }

        return null;
    }
}
