<?php

namespace App\Modules\Crm\Models;

use App\Models\User;
use App\Modules\Emlak\Models\Ilan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Randevu extends Model
{
    use HasFactory;

    /**
     * Tablo adı
     *
     * @var string
     */
    protected $table = 'randevular';

    /**
     * Toplu atama yapılabilecek alanlar
     *
     * @var array
     */
    protected $fillable = [
        'musteri_id',
        'user_id',
        'ilan_id',
        'baslangic',
        'bitis',
        'baslik',
        'aciklama',
        'konum',
        'status',
        'notlar',
        'hatirlatma_gonder',
        'hatirlatma_dakika',
    ];

    /**
     * Otomatik tip dönüşümü yapılacak alanlar
     *
     * @var array
     */
    protected $casts = [
        'baslangic' => 'datetime',
        'bitis' => 'datetime',
        'hatirlatma_gonder' => 'boolean',
        'hatirlatma_dakika' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Randevunun ilişkili olduğu müşteri
     */
    public function musteri()
    {
        return $this->belongsTo(Musteri::class, 'musteri_id');
    }

    /**
     * Randevuyu oluşturan kullanıcı (danışman)
     */
    public function danisman()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Randevunun ilişkili olduğu ilan
     */
    public function ilan()
    {
        return $this->belongsTo(Ilan::class, 'ilan_id');
    }

    /**
     * Bugünkü randevuları filtreler
     */
    public function scopeBugun($query)
    {
        return $query->whereDate('baslangic', now()->toDateString());
    }

    /**
     * Gelecekteki randevuları filtreler
     */
    public function scopeGelecek($query)
    {
        return $query->where('baslangic', '>', now());
    }

    /**
     * Geçmişteki randevuları filtreler
     */
    public function scopeGecmis($query)
    {
        return $query->where('baslangic', '<', now());
    }

    /**
     * Planlanmış randevuları filtreler
     */
    public function scopePlanlandi($query)
    {
        return $query->where('status', 'Planlandı');
    }

    /**
     * Tamamlanmış randevuları filtreler
     */
    public function scopeTamamlandi($query)
    {
        return $query->where('status', 'Tamamlandı');
    }

    /**
     * İptal edilmiş randevuları filtreler
     */
    public function scopeIptalEdildi($query)
    {
        return $query->where('status', 'İptal Edildi');
    }

    /**
     * Ertelenmiş randevuları filtreler
     */
    public function scopeErtelendi($query)
    {
        return $query->where('status', 'Ertelendi');
    }
}
