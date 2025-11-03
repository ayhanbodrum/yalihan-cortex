<?php

namespace App\Modules\TakimYonetimi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GorevDosya extends Model
{
    use HasFactory;

    protected $table = 'gorev_dosyalar';

    protected $fillable = [
        'gorev_id',
        'user_id',
        'dosya_adi',
        'dosya_yolu',
        'dosya_tipi',
        'dosya_boyutu',
        'aciklama',
    ];

    protected $casts = [
        'dosya_boyutu' => 'integer',
    ];

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
    public function scopeGorev($query, $gorevId)
    {
        return $query->where('gorev_id', $gorevId);
    }

    public function scopeUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDosyaTipi($query, $tip)
    {
        return $query->where('dosya_tipi', $tip);
    }

    // Accessors
    public function getDosyaBoyutuFormatliAttribute(): string
    {
        if (! $this->dosya_boyutu) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $this->dosya_boyutu;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2).' '.$units[$unit];
    }

    public function getDosyaUzantisiAttribute(): string
    {
        return pathinfo($this->dosya_adi, PATHINFO_EXTENSION);
    }

    public function getDosyaTipiIconAttribute(): string
    {
        $iconlar = [
            'pdf' => 'fas fa-file-pdf text-danger',
            'doc' => 'fas fa-file-word text-primary',
            'docx' => 'fas fa-file-word text-primary',
            'xls' => 'fas fa-file-excel text-success',
            'xlsx' => 'fas fa-file-excel text-success',
            'ppt' => 'fas fa-file-powerpoint text-warning',
            'pptx' => 'fas fa-file-powerpoint text-warning',
            'jpg' => 'fas fa-file-image text-info',
            'jpeg' => 'fas fa-file-image text-info',
            'png' => 'fas fa-file-image text-info',
            'gif' => 'fas fa-file-image text-info',
            'txt' => 'fas fa-file-alt text-secondary',
            'zip' => 'fas fa-file-archive text-warning',
            'rar' => 'fas fa-file-archive text-warning',
        ];

        $uzanti = strtolower($this->getDosyaUzantisiAttribute());

        return $iconlar[$uzanti] ?? 'fas fa-file text-secondary';
    }

    public function getIndirmeUrlAttribute(): string
    {
        return route('admin.gorevler.dosya.indir', $this->id);
    }

    public function getGoruntulemeUrlAttribute(): string
    {
        if (in_array(strtolower($this->getDosyaUzantisiAttribute()), ['jpg', 'jpeg', 'png', 'gif', 'pdf'])) {
            return route('admin.gorevler.dosya.goruntule', $this->id);
        }

        return $this->getIndirmeUrlAttribute();
    }

    // Methods
    public function resimMi(): bool
    {
        return in_array(strtolower($this->getDosyaUzantisiAttribute()), ['jpg', 'jpeg', 'png', 'gif']);
    }

    public function dokumanMi(): bool
    {
        return in_array(strtolower($this->getDosyaUzantisiAttribute()), ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt']);
    }

    public function arsivMi(): bool
    {
        return in_array(strtolower($this->getDosyaUzantisiAttribute()), ['zip', 'rar', '7z']);
    }

    public function goruntulenebilirMi(): bool
    {
        return $this->resimMi() || strtolower($this->getDosyaUzantisiAttribute()) === 'pdf';
    }

    public function dosyaVarMi(): bool
    {
        return file_exists(storage_path('app/'.$this->dosya_yolu));
    }

    public function dosyayiSil(): bool
    {
        if ($this->dosyaVarMi()) {
            return unlink(storage_path('app/'.$this->dosya_yolu));
        }

        return true;
    }
}
