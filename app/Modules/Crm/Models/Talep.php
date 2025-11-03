<?php

namespace App\Modules\Crm\Models;

use App\Modules\Auth\Models\User;
use App\Modules\BaseModule\Models\BaseModel;
use App\Modules\Emlak\Models\Feature;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $kisi_id
 * @property int|null $danisman_id
 * @property string $talep_turu
 * @property string $emlak_turu
 * @property string|null $butce_min
 * @property string|null $butce_max
 * @property string $para_birimi
 * @property string|null $il
 * @property string|null $ilce
 * @property string|null $mahalle
 * @property string|null $aciklama
 * @property string $status
 * @property string $oncelik
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $danisman
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Modules\Crm\Models\IlanTalepEslesme> $eslesme
 * @property-read int|null $eslesme_count
 * @property-read \App\Modules\Crm\Models\Kisi $kisi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Feature> $ozellikler
 * @property-read int|null $ozellikler_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereAciklama($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereButceMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereButceMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereDanismanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereDurum($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereEmlakTuru($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereIl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereIlce($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereKisiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereMahalle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereOncelik($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereParaBirimi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereTalepTuru($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Talep withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Talep extends BaseModel
{
    use HasFactory, SoftDeletes;

    /**
     * İlişkilendirilmiş tablo adı
     */
    protected $table = 'talepler';

    /**
     * Toplu atanabilir alanlar
     */
    protected $fillable = [
        'kisi_id',
        'danisman_id',
        'baslik',
        'aciklama',
        'ilan_turu', // Konut, İşyeri, Arsa vb.
        'yayinlama_tipi', // Satılık, Kiralık
        'min_fiyat',
        'max_fiyat',
        'para_birimi',
        'il',
        'ilce',
        'mahalle',
        'min_alan',
        'max_alan',
        'min_oda',
        'max_oda',
        'status', // Yeni, İşlemde, Tamamlandı, İptal vb.
        'oncelik', // Yüksek, Normal, Düşük
        'talep_tipi',
        'emlak_tipi',
        'butce_min',
        'butce_max',
        'metrekare_min',
        'metrekare_max',
        'oda_sayisi',
    ];

    /**
     * Cast edilecek özellikler
     */
    protected $casts = [
        'min_fiyat' => 'float',
        'max_fiyat' => 'float',
        'min_alan' => 'float',
        'max_alan' => 'float',
        'min_oda' => 'float',
        'max_oda' => 'float',
    ];

    /**
     * Talebin ilişkili olduğu müşteri
     */
    public function kisi()
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
    }

    /**
     * Talebi yöneten danışman (hem User hem de Danisman modelinden)
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
     * Talep için istenilen özellikler
     */
    public function ozellikler()
    {
        return $this->belongsToMany(Feature::class, 'talep_feature', 'talep_id', 'feature_id')
            ->withPivot('deger')
            ->withTimestamps();
    }

    /**
     * Talep için eşleştirilen ilanlar
     */
    public function eslesme()
    {
        return $this->hasMany(IlanTalepEslesme::class, 'talep_id');
    }
}
