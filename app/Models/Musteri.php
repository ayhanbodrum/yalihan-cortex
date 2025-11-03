<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Musteri extends Model
{
    use HasFactory;

    protected $table = 'musteriler';

    protected $fillable = [
        'ad',
        'soyad',
        'telefon',
        'email',
        'ulke_id',
        'il_id',
        'ilce_id',
        'mahalle_id',
        'adres',
        'il',
        'ilce',
        'tc_kimlik',
        'dogum_tarihi',
        'meslek',
        'gelir_duzeyi',
        'medeni_status',
        'notlar',
        'status',
        'kaynak',
        'user_id',
        'danisman_id', // eksikti
        'musteri_tipi',
        'kisi_tipi',
        // Gelişmiş CRM alanları
        'vergi_no',
        'vergi_dairesi',
        'banka_bilgileri',
        'acil_status_iletisim',
        'tercihler',
        'risk_profili',
        'musteri_segmenti',
        'son_aktivite',
        'toplam_islem',
        'toplam_islem_tutari',
        'memnuniyet_skoru'
    ];

    protected $casts = [
        'dogum_tarihi' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // İlişkiler
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function danisman()
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    public function ulke()
    {
        return $this->belongsTo(Ulke::class);
    }

    public function il()
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    public function ilce()
    {
        return $this->belongsTo(Ilce::class);
    }

    public function mahalle()
    {
        return $this->belongsTo(Mahalle::class);
    }

    public function ilanlar()
    {
        return $this->hasMany(Ilan::class, 'kisi_id');
    }

    public function aktiviteler()
    {
        return $this->hasMany(MusteriAktivite::class);
    }

    public function notlar()
    {
        return $this->hasMany(MusteriNot::class);
    }

    public function etiketler()
    {
        return $this->belongsToMany(Etiket::class, 'musteri_etiketler', 'musteri_id', 'etiket_id');
    }

    // Accessor'lar
    public function getTamAdAttribute()
    {
        return $this->ad . ' ' . $this->soyad;
    }

    public function getDurumLabelAttribute()
    {
        $statuslar = [
            'Aktif' => 'Aktif',
            'Pasif' => 'Pasif',
            'Potansiyel' => 'Potansiyel'
        ];

        return $statuslar[$this->status] ?? 'Bilinmiyor';
    }

    // Scope'lar
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopePotansiyel($query)
    {
        return $query->where('status', 'Potansiyel');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
