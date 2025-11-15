<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IlanDemirbas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ilan_demirbas';

    protected $fillable = [
        'ilan_id',
        'demirbas_id',
        'brand', // ✅ Context7: İlan bazında marka override
        'model',
        'quantity',
        'notes',
        'display_order', // ✅ Context7: order → display_order
        'status', // ✅ Context7: boolean status
    ];

    protected $casts = [
        'status' => 'boolean', // ✅ Context7: boolean status
        'display_order' => 'integer', // ✅ Context7: display_order
        'quantity' => 'integer',
    ];

    /**
     * İlan ilişkisi
     */
    public function ilan(): BelongsTo
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }

    /**
     * Demirbaş ilişkisi
     */
    public function demirbas(): BelongsTo
    {
        return $this->belongsTo(Demirbas::class, 'demirbas_id');
    }

    /**
     * Aktif scope
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
