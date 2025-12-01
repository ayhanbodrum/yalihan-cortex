<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevelopmentVelocityMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'developer_name',
        'branch_name',
        'commits_count',
        'files_changed',
        'lines_added',
        'lines_deleted',
        'code_quality_score',
        'context7_violations',
        'auto_fixes_applied',
        'test_coverage',
        'feature_tags',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'period_start' => 'datetime',
        'period_end' => 'datetime',
        'feature_tags' => 'array',
        'code_quality_score' => 'float',
        'test_coverage' => 'float',
    ];

    /**
     * Scope for recent velocity metrics
     */
    public function scopeRecent($query, int $hours = 168)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope for specific developer
     */
    public function scopeForDeveloper($query, string $developer)
    {
        return $query->where('developer_name', $developer);
    }

    /**
     * Scope for specific branch
     */
    public function scopeForBranch($query, string $branch)
    {
        return $query->where('branch_name', $branch);
    }

    /**
     * Scope for date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('period_start', [$startDate, $endDate]);
    }

    /**
     * Get net lines changed (added - deleted)
     */
    public function getNetLinesAttribute(): int
    {
        return $this->lines_added - $this->lines_deleted;
    }

    /**
     * Get files per commit ratio
     */
    public function getFilesPerCommitAttribute(): float
    {
        return $this->commits_count > 0 ? $this->files_changed / $this->commits_count : 0;
    }

    /**
     * Get lines per commit ratio
     */
    public function getLinesPerCommitAttribute(): float
    {
        return $this->commits_count > 0 ? $this->lines_added / $this->commits_count : 0;
    }

    /**
     * Get period duration in days
     */
    public function getPeriodDurationAttribute(): int
    {
        return $this->period_start->diffInDays($this->period_end) + 1;
    }

    /**
     * Get commits per day
     */
    public function getCommitsPerDayAttribute(): float
    {
        $duration = $this->period_duration;

        return $duration > 0 ? $this->commits_count / $duration : 0;
    }

    /**
     * Get productivity score based on multiple factors
     */
    public function getProductivityScoreAttribute(): float
    {
        if ($this->commits_count == 0) {
            return 0;
        }

        // Base metrics
        $commitsPerDay = $this->commits_per_day;
        $filesPerCommit = $this->files_per_commit;
        $linesPerCommit = $this->lines_per_commit;

        // Calculate component scores (0-100)
        $commitScore = min(100, $commitsPerDay * 20); // Ideal: 5 commits/day
        $fileScore = min(100, $filesPerCommit * 10);   // Ideal: 10 files/commit
        $lineScore = min(100, $linesPerCommit / 5);    // Ideal: 500 lines/commit

        // Quality factor (penalty for violations)
        $qualityPenalty = ($this->context7_violations / max(1, $this->commits_count)) * 10;

        $score = ($commitScore * 0.3 + $fileScore * 0.3 + $lineScore * 0.4) - $qualityPenalty;

        return max(0, min(100, round($score, 2)));
    }

    /**
     * Get quality assessment
     */
    public function getQualityAssessmentAttribute(): string
    {
        $score = $this->productivity_score;

        return match (true) {
            $score >= 90 => 'excellent',
            $score >= 75 => 'good',
            $score >= 50 => 'fair',
            $score >= 25 => 'poor',
            default => 'very_poor'
        };
    }

    /**
     * Check if this is a high-performance period
     */
    public function isHighPerformance(): bool
    {
        return $this->productivity_score >= 80
            && $this->code_quality_score >= 75
            && $this->context7_violations <= 3;
    }

    /**
     * Get violation rate per commit
     */
    public function getViolationRateAttribute(): float
    {
        return $this->commits_count > 0 ? $this->context7_violations / $this->commits_count : 0;
    }

    /**
     * Get auto-fix rate
     */
    public function getAutoFixRateAttribute(): float
    {
        return $this->context7_violations > 0
            ? ($this->auto_fixes_applied / $this->context7_violations * 100)
            : 100;
    }
}
