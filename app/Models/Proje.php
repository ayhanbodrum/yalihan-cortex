<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proje extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projeler';

    protected $fillable = [
        'proje_adi',
        'aciklama',
        'baslangic_tarihi',
        'bitis_tarihi',
        'status',
        'oncelik',
        'user_id',
        'takim_id',
        'budget',
        'gerceklesen_maliyet',
        'progress',
        'notlar',
        'tags',
        'status'
    ];

    protected $casts = [
        'baslangic_tarihi' => 'date',
        'bitis_tarihi' => 'date',
        'budget' => 'decimal:2',
        'gerceklesen_maliyet' => 'decimal:2',
        'progress' => 'integer',
        'tags' => 'array',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // İlişkiler
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function takim()
    {
        return $this->belongsTo(\App\Modules\TakimYonetimi\Models\TakimUyesi::class, 'takim_id');
    }

    public function gorevler()
    {
        return $this->hasMany(\App\Modules\TakimYonetimi\Models\Gorev::class);
    }

    public function dosyalar()
    {
        return $this->hasMany(\App\Modules\TakimYonetimi\Models\GorevDosya::class);
    }

    // Accessor'lar
    public function getDurumLabelAttribute()
    {
        $statuslar = [
            'planlama' => 'Planlama',
            'devam_ediyor' => 'Devam Ediyor',
            'tamamlandi' => 'Tamamlandı',
            'iptal' => 'İptal',
            'beklemede' => 'Beklemede'
        ];

        return $statuslar[$this->status] ?? 'Bilinmiyor';
    }

    public function getOncelikLabelAttribute()
    {
        $oncelikler = [
            'dusuk' => 'Düşük',
            'orta' => 'Orta',
            'yuksek' => 'Yüksek',
            'kritik' => 'Kritik'
        ];

        return $oncelikler[$this->oncelik] ?? 'Bilinmiyor';
    }

    public function getKalanGunAttribute()
    {
        if (!$this->bitis_tarihi) {
            return null;
        }

        $now = now();
        $bitis = $this->bitis_tarihi;

        if ($bitis < $now) {
            return 0; // Süresi geçmiş
        }

        return $now->diffInDays($bitis);
    }

    public function getProgressYuzdeAttribute()
    {
        return $this->progress . '%';
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDurum($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByOncelik($query, $oncelik)
    {
        return $query->where('oncelik', $oncelik);
    }

    public function scopeGecmis($query)
    {
        return $query->where('bitis_tarihi', '<', now());
    }

    public function scopeYaklasan($query, $gun = 7)
    {
        return $query->where('bitis_tarihi', '<=', now()->addDays($gun))
                    ->where('bitis_tarihi', '>', now());
    }

    // Metodlar
    public function updateProgress()
    {
        $toplamGorev = $this->gorevler()->count();

        if ($toplamGorev == 0) {
            $this->update(['progress' => 0]);
            return;
        }

        $tamamlananGorev = $this->gorevler()->where('status', 'tamamlandi')->count();
        $progress = round(($tamamlananGorev / $toplamGorev) * 100);

        $this->update(['progress' => $progress]);
    }

    public function isGecmis()
    {
        return $this->bitis_tarihi && $this->bitis_tarihi < now();
    }

    public function isYaklasan($gun = 7)
    {
        return $this->bitis_tarihi &&
               $this->bitis_tarihi <= now()->addDays($gun) &&
               $this->bitis_tarihi > now();
    }
}
