<?php

namespace App\Modules\TakimYonetimi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GorevTakip extends Model
{
    use HasFactory;

    protected $table = 'gorev_takip';

    protected $fillable = [
        'gorev_id',
        'user_id',
        'status',
        'notlar',
        'baslangic_zamani',
        'bitis_zamani',
        'harcanan_sure',
        'dosyalar',
    ];

    protected $casts = [
        'baslangic_zamani' => 'datetime',
        'bitis_zamani' => 'datetime',
        'harcanan_sure' => 'integer',
        'dosyalar' => 'array',
    ];

    // Enum değerleri
    public static function getDurumlar(): array
    {
        return ['basladi', 'devam_ediyor', 'tamamlandi', 'durduruldu'];
    }

    // Relationships
    public function gorev(): BelongsTo
    {
        return $this->belongsTo(Gorev::class, 'gorev_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['basladi', 'devam_ediyor']);
    }

    public function scopeDurum($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeGorev($query, $gorevId)
    {
        return $query->where('gorev_id', $gorevId);
    }

    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Accessors
    public function getDurumEtiketiAttribute(): string
    {
        $etiketler = [
            'basladi' => '<span class="badge bg-info">Başladı</span>',
            'devam_ediyor' => '<span class="badge bg-primary">Devam Ediyor</span>',
            'tamamlandi' => '<span class="badge bg-success">Tamamlandı</span>',
            'durduruldu' => '<span class="badge bg-warning">Durduruldu</span>',
        ];

        return $etiketler[$this->status] ?? $etiketler['basladi'];
    }

    public function getHarcananSureSaatAttribute(): string
    {
        if (! $this->harcanan_sure) {
            return '0 saat';
        }

        $saat = floor($this->harcanan_sure / 60);
        $dakika = $this->harcanan_sure % 60;

        if ($saat > 0 && $dakika > 0) {
            return "{$saat} saat {$dakika} dakika";
        } elseif ($saat > 0) {
            return "{$saat} saat";
        } else {
            return "{$dakika} dakika";
        }
    }

    public function getHarcananSureDakikaAttribute(): int
    {
        return $this->harcanan_sure ?? 0;
    }

    // Methods
    public function baslat(): bool
    {
        if ($this->status !== 'basladi') {
            return false;
        }

        $this->update([
            'status' => 'devam_ediyor',
            'baslangic_zamani' => now(),
        ]);

        return true;
    }

    public function durdur(?string $sebep = null): bool
    {
        if (! in_array($this->status, ['basladi', 'devam_ediyor'])) {
            return false;
        }

        $this->update([
            'status' => 'durduruldu',
            'notlar' => $sebep ? ($this->notlar."\n\nDurdurulma Sebebi: ".$sebep) : $this->notlar,
        ]);

        return true;
    }

    public function tamamla(?string $notlar = null): bool
    {
        if (! in_array($this->status, ['basladi', 'devam_ediyor'])) {
            return false;
        }

        $bitisZamani = now();
        $harcananSure = 0;

        if ($this->baslangic_zamani) {
            $harcananSure = $bitisZamani->diffInMinutes($this->baslangic_zamani);
        }

        $this->update([
            'status' => 'tamamlandi',
            'bitis_zamani' => $bitisZamani,
            'harcanan_sure' => $harcananSure,
            'notlar' => $notlar ? ($this->notlar."\n\nTamamlanma Notu: ".$notlar) : $this->notlar,
        ]);

        return true;
    }

    public function statusMi(): bool
    {
        return in_array($this->status, ['basladi', 'devam_ediyor']);
    }

    public function tamamlanabilirMi(): bool
    {
        return in_array($this->status, ['basladi', 'devam_ediyor']);
    }

    public function durdurulabilirMi(): bool
    {
        return in_array($this->status, ['basladi', 'devam_ediyor']);
    }
}
