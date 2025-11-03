<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MusteriEtiket extends Model
{
    use HasFactory;

    protected $table = 'musteri_etiketler';

    protected $fillable = [
        'ad',
        'renk',
        'aciklama',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Bu etiketi kullanan müşteriler
     */
    public function kisiler(): BelongsToMany
    {
        return $this->belongsToMany(Kisi::class, 'kisi_etiket', 'etiket_id', 'kisi_id')
            ->withTimestamps();
    }

    /**
     * Aktif etiketler
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Pasif etiketler
     */
    public function scopePasif($query)
    {
        return $query->where('status', false);
    }
}
