<?php

namespace App\Modules\Crm\Models;

use App\Modules\Emlak\Models\Ilan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Musteri extends Model
{
    use HasFactory;

    /**
     * Tablo adı
     *
     * @var string
     */
    protected $table = 'musteriler';

    /**
     * Toplu atama yapılabilecek alanlar
     *
     * @var array
     */
    protected $fillable = [
        'ad',
        'soyad',
        'telefon',
        'email',
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
        'facebook_profile',
        'twitter_profile',
        'instagram_profile',
        'linkedin_profile',
    ];

    /**
     * Otomatik tip dönüşümü yapılacak alanlar
     *
     * @var array
     */
    protected $casts = [
        'dogum_tarihi' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Müşterinin danışmanı (kullanıcı)
     */
    public function danisman()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Müşterinin talepleri
     */
    public function talepler()
    {
        return $this->hasMany(Talep::class, 'musteri_id');
    }

    /**
     * Müşterinin ilgilendiği ilanlar
     */
    public function ilgilenilenIlanlar()
    {
        return $this->belongsToMany(Ilan::class, 'musteri_ilan', 'musteri_id', 'ilan_id')
            ->withPivot('status', 'notlar')
            ->withTimestamps();
    }

    /**
     * Müşterinin randevuları
     */
    public function randevular()
    {
        return $this->hasMany(Randevu::class, 'musteri_id');
    }

    /**
     * Müşterinin aktiviteleri
     */
    public function aktiviteler()
    {
        return $this->hasMany(Aktivite::class, 'musteri_id');
    }

    /**
     * Müşterinin tam adını döndürür
     */
    public function getFullNameAttribute()
    {
        return "{$this->ad} {$this->soyad}";
    }

    /**
     * Müşterinin tam adresini döndürür
     */
    public function getFullAddressAttribute()
    {
        $address = $this->adres;
        if ($this->ilce) {
            $address .= ", {$this->ilce}";
        }
        if ($this->il) {
            $address .= ", {$this->il}";
        }

        return $address;
    }

    /**
     * Aktif müşterileri filtreler
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Pasif müşterileri filtreler
     */
    public function scopePasif($query)
    {
        return $query->where('status', 'Pasif');
    }

    /**
     * Potansiyel müşterileri filtreler
     */
    public function scopePotansiyel($query)
    {
        return $query->where('status', 'Potansiyel');
    }
}
