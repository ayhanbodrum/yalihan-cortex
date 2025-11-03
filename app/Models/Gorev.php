<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gorev extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gorevler';

    protected $fillable = [
        'baslik',
        'aciklama',
        'oncelik',
        'status',
        'tip',
        'deadline',
        'tahmini_sure',
        'admin_id',
        'danisman_id',
        'musteri_id',
        'proje_id',
        'tags',
        'metadata'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'tahmini_sure' => 'integer', // dakika cinsinden
        'tags' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // İlişkiler
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function danisman()
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    public function musteri()
    {
        return $this->belongsTo(User::class, 'musteri_id');
    }

    public function proje()
    {
        return $this->belongsTo(Proje::class);
    }

    public function takip()
    {
        return $this->hasMany(\App\Modules\TakimYonetimi\Models\GorevTakip::class);
    }

    public function dosyalar()
    {
        return $this->hasMany(\App\Modules\TakimYonetimi\Models\GorevDosya::class);
    }

    // Accessor'lar
    public function getDurumLabelAttribute()
    {
        $statuslar = [
            'beklemede' => 'Beklemede',
            'devam_ediyor' => 'Devam Ediyor',
            'tamamlandi' => 'Tamamlandı',
            'iptal' => 'İptal',
            'askida' => 'Askıda'
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

    public function getTahminiSureSaatAttribute()
    {
        return round($this->tahmini_sure / 60, 1);
    }

    public function getGerceklesenSureSaatAttribute()
    {
        return round($this->gerceklesen_sure / 60, 1);
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

    public function scopeByAtayan($query, $userId)
    {
        return $query->where('atayan_id', $userId);
    }

    public function scopeByDurum($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByOncelik($query, $oncelik)
    {
        return $query->where('oncelik', $oncelik);
    }

    public function scopeByProje($query, $projeId)
    {
        return $query->where('proje_id', $projeId);
    }

    public function scopeGecmis($query)
    {
        return $query->where('bitis_tarihi', '<', now());
    }

    public function scopeYaklasan($query, $gun = 3)
    {
        return $query->where('bitis_tarihi', '<=', now()->addDays($gun))
                    ->where('bitis_tarihi', '>', now())
                    ->where('status', '!=', 'tamamlandi');
    }

    public function scopeGecikmis($query)
    {
        return $query->where('bitis_tarihi', '<', now())
                    ->where('status', '!=', 'tamamlandi');
    }

    // Metodlar
    public function isGecmis()
    {
        return $this->bitis_tarihi && $this->bitis_tarihi < now();
    }

    public function isYaklasan($gun = 3)
    {
        return $this->bitis_tarihi &&
               $this->bitis_tarihi <= now()->addDays($gun) &&
               $this->bitis_tarihi > now() &&
               $this->status != 'tamamlandi';
    }

    public function isGecikmis()
    {
        return $this->isGecmis() && $this->status != 'tamamlandi';
    }

    public function tamamla()
    {
        $this->update([
            'status' => 'tamamlandi',
            'bitis_tarihi' => now()
        ]);
    }

    public function iptalEt()
    {
        $this->update([
            'status' => 'iptal'
        ]);
    }

    public function baslat()
    {
        $this->update([
            'status' => 'devam_ediyor',
            'baslangic_tarihi' => now()
        ]);
    }

    public function askiyaAl()
    {
        $this->update([
            'status' => 'askida'
        ]);
    }

    public function addTakip($aciklama, $userId = null)
    {
        return $this->takip()->create([
            'aciklama' => $aciklama,
            'user_id' => $userId ?? auth()->id(),
            'tarih' => now()
        ]);
    }

    /**
     * Görev atanabilir mi kontrolü
     */
    public function atanabilirMi()
    {
        // Görev beklemede veya askıda ise atanabilir
        return in_array($this->status, ['beklemede', 'askida']);
    }

    /**
     * Görev gecikti mi kontrolü
     */
    public function geciktiMi()
    {
        // Deadline varsa ve geçmişse, görev tamamlanmamışsa gecikmiş
        return $this->deadline &&
               $this->deadline < now() &&
               !in_array($this->status, ['tamamlandi', 'iptal']);
    }

    /**
     * Deadline yaklaşıyor mu kontrolü
     */
    public function deadlineYaklasiyorMu($gun = 3)
    {
        // Deadline varsa ve yaklaşıyorsa, görev tamamlanmamışsa yaklaşıyor
        return $this->deadline &&
               $this->deadline <= now()->addDays($gun) &&
               $this->deadline > now() &&
               !in_array($this->status, ['tamamlandi', 'iptal']);
    }

    /**
     * Görev statusuna göre renk döndür
     */
    public function getDurumRengiAttribute()
    {
        $renkler = [
            'beklemede' => 'warning',
            'devam_ediyor' => 'info',
            'tamamlandi' => 'success',
            'iptal' => 'danger',
            'askida' => 'secondary'
        ];

        return $renkler[$this->status] ?? 'secondary';
    }

    /**
     * Öncelik rengini döndür
     */
    public function getOncelikRengiAttribute()
    {
        $renkler = [
            'dusuk' => 'success',
            'orta' => 'warning',
            'yuksek' => 'danger',
            'kritik' => 'danger'
        ];

        return $renkler[$this->oncelik] ?? 'secondary';
    }
}
