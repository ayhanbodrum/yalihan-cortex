<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Dashboard Widget Model
 * 
 * Context7 Standardı: C7-DASHBOARD-WIDGET-2025-11-05
 * 
 * Kullanıcıların dashboard'larına ekleyebileceği widget'ları yönetir
 */
class DashboardWidget extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'dashboard_widgets';

    protected $fillable = [
        'name',
        'type',
        'data_source',
        'position_x',
        'position_y',
        'width',
        'height',
        'settings',
        'user_id',
        'is_active',
        'order',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'position_x' => 'integer',
        'position_y' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Aktif widget'lar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Belirli kullanıcının widget'ları
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Sıralı widget'lar
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('position_y')->orderBy('position_x');
    }

    /**
     * Widget tipine göre veri kaynağından veri çek
     */
    public function getData()
    {
        return match($this->type) {
            'stat' => $this->getStatData(),
            'chart' => $this->getChartData(),
            'table' => $this->getTableData(),
            'activity' => $this->getActivityData(),
            default => null,
        };
    }

    /**
     * İstatistik widget verisi
     */
    protected function getStatData()
    {
        return match($this->data_source) {
            'ilanlar' => [
                'total' => \App\Models\Ilan::count(),
                'active' => \App\Models\Ilan::where('status', 'Aktif')->count(),
                'pending' => \App\Models\Ilan::where('status', 'Beklemede')->count(),
            ],
            'musteriler' => [
                'total' => \App\Models\Kisi::count(),
                'active' => \App\Models\Kisi::where('status', 'Aktif')->count(),
            ],
            'talepler' => [
                'total' => \App\Models\Talep::count(),
                'active' => \App\Models\Talep::where('status', 'Aktif')->count(),
            ],
            default => null,
        };
    }

    /**
     * Grafik widget verisi
     */
    protected function getChartData()
    {
        // Chart data implementation
        return [];
    }

    /**
     * Tablo widget verisi
     */
    protected function getTableData()
    {
        // Table data implementation
        return [];
    }

    /**
     * Aktivite widget verisi
     */
    protected function getActivityData()
    {
        // Activity data implementation
        return [];
    }
}
