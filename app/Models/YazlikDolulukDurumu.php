<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YazlikDolulukDurumu extends Model
{
    use HasFactory;

    protected $table = 'yazlik_doluluk_durumlari';

    protected $fillable = [
        'ilan_id',
        'tarih',
        'status',
        'aciklama',
        'ozel_fiyat',
        'rezervasyon_id',
        'created_by',
    ];

    protected $casts = [
        'tarih' => 'date',
        'ozel_fiyat' => 'array',
    ];

    public function ilan()
    {
        return $this->belongsTo(Ilan::class);
    }

    public function rezervasyon()
    {
        return $this->belongsTo(YazlikRezervasyon::class, 'rezervasyon_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeMusait($query)
    {
        return $query->where('status', 'musait');
    }

    public function scopeRezerve($query)
    {
        return $query->where('status', 'rezerve');
    }

    public function scopeBloke($query)
    {
        return $query->where('status', 'bloke');
    }

    public function scopeByIlan($query, $ilanId)
    {
        return $query->where('ilan_id', $ilanId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tarih', [$startDate, $endDate]);
    }

    public function isMusait()
    {
        return ($this->status ?? null) === 'musait';
    }

    public function isRezerve()
    {
        return ($this->status ?? null) === 'rezerve';
    }

    public function isBloke()
    {
        return in_array(($this->status ?? null), ['bloke', 'bakim', 'temizlik', 'kapali']);
    }

    public function setRezerve($rezervasyonId = null, $aciklama = null)
    {
        $this->update([
            'status' => 'rezerve',
            'rezervasyon_id' => $rezervasyonId,
            'aciklama' => $aciklama,
        ]);
    }

    public function setMusait()
    {
        $this->update([
            'status' => 'musait',
            'rezervasyon_id' => null,
            'aciklama' => null,
        ]);
    }

    public function setBloke($aciklama = null)
    {
        $this->update([
            'status' => 'bloke',
            'aciklama' => $aciklama,
        ]);
    }
}
