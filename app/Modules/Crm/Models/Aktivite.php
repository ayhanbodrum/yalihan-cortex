<?php

namespace App\Modules\Crm\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Emlak\Models\Ilan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktivite extends Model
{
    use HasFactory;

    /**
     * Tablo adı
     *
     * @var string
     */
    protected $table = 'aktiviteler';

    /**
     * Toplu atama yapılabilecek alanlar
     *
     * @var array
     */
    protected $fillable = [
        'kisi_id',       // Değiştirildi: musteri_id -> kisi_id
        'danisman_id',   // Değiştirildi: user_id -> danisman_id
        'tip',           // Değiştirildi: tur -> tip
        'baslik',
        'aciklama',
        'baslangic_tarihi', // Değiştirildi: tarih -> baslangic_tarihi
        'bitis_tarihi',
        'status',
        'sonuc',         // Şimdilik korundu
        'ilan_id',       // Şimdilik korundu
    ];

    /**
     * Otomatik tip dönüşümü yapılacak alanlar
     *
     * @var array
     */
    protected $casts = [
        'baslangic_tarihi' => 'datetime',
        'bitis_tarihi' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Aktivitenin ilişkili olduğu kişi
     */
    public function kisi()
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
    }

    /**
     * Aktiviteyi oluşturan danışman (hem User hem de Danisman modelinden)
     */
    public function danisman()
    {
        // Önce User modelinden kontrol et
        $userDanisman = User::find($this->danisman_id);
        if ($userDanisman) {
            return $userDanisman;
        }

        // User modelinde bulunamazsa, Danışman modelinden kontrol et
        try {
            $danismanModel = \App\Modules\Danisman\Models\Danisman::find($this->danisman_id);
            if ($danismanModel) {
                return (object) [
                    'id' => $danismanModel->id,
                    'name' => $danismanModel->ad.' '.$danismanModel->soyad,
                    'email' => $danismanModel->email,
                    'phone_number' => $danismanModel->telefon,
                    'source' => 'danisman_model',
                ];
            }
        } catch (\Exception $e) {
            // Danışman modeli bulunamazsa null döndür
        }

        return null;
    }

    /**
     * User modeli ile danışman ilişkisi (Eloquent için)
     */
    public function userDanisman()
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * Aktivitenin ilişkili olduğu ilan
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }

    /**
     * Bugünkü aktiviteleri filtreler
     */
    public function scopeBugun($query)
    {
        return $query->whereDate('baslangic_tarihi', now()->toDateString());
    }

    /**
     * Gelecekteki aktiviteleri filtreler
     */
    public function scopeGelecek($query)
    {
        return $query->where('baslangic_tarihi', '>', now());
    }

    /**
     * Geçmişteki aktiviteleri filtreler
     */
    public function scopeGecmis($query)
    {
        return $query->where('baslangic_tarihi', '<', now());
    }

    /**
     * Tamamlanmış aktiviteleri filtreler
     */
    public function scopeTamamlandi($query)
    {
        return $query->where('status', self::DURUM_TAMAMLANDI);
    }

    /**
     * Bekleyen aktiviteleri filtreler
     */
    public function scopeBekliyor($query)
    {
        return $query->where('status', self::DURUM_BEKLEMEDE);
    }

    /**
     * Aktivite tipleri
     */
    const TIP_GORUSME = 'gorusme';

    const TIP_RANDEVU = 'randevu';

    const TIP_ARAMA = 'arama';

    const TIP_EMAIL = 'email';

    const TIP_DIGER = 'diger';

    /**
     * Aktivite statusları
     */
    const DURUM_BEKLEMEDE = 'beklemede';

    const DURUM_TAMAMLANDI = 'tamamlandi';

    const DURUM_IPTAL = 'iptal';
}
