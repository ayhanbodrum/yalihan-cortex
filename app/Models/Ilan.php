<?php

namespace App\Models;

use App\Enums\IlanStatus;
use App\Enums\YayinTipi;
use App\Traits\Filterable;
use App\Traits\HasFeatures;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

/**
 * App\Models\Ilan
 *
 * @property int $id
 * @property string $baslik
 * @property string|null $aciklama
 * @property float $fiyat
 * @property string $para_birimi
 * @property Carbon|null $ilan_tarihi
 * @property string $status
 * @property int|null $proje_id
 *
 * // İlişkisel Alanlar
 * @property int|null $ilan_sahibi_id
 * @property int|null $ilgili_kisi_id
 * @property int|null $danisman_id
 * @property int|null $ulke_id
 * @property int|null $il_id
 * @property int|null $ilce_id
 * @property int|null $mahalle_id
 * @property int|null $ana_kategori_id
 * @property int|null $alt_kategori_id
 *
 * // Analitik, SEO ve CRM Alanları
 * @property string|null $slug
 * @property int $view_count
 * @property int $favorite_count
 * @property Carbon|null $son_islem_tarihi
 * @property float|null $son_islem_fiyati
 * @property string|null $islem_tipi // 'satis', 'kiralama'
 *
 * // Diğer Alanlar
 * @property string|null $youtube_video_url
 * @property string|null $sanal_tur_url
 * @property string|null $ada_no
 * @property string|null $parsel_no
 * @property float|null $latitude
 * @property float|null $longitude
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 * // İlişkiler (Relationships)
 * @property-read Kisi|null $ilanSahibi
 * @property-read Kisi|null $ilgiliKisi
 * @property-read User|null $danisman
 * @property-read Ulke|null $ulke
 * @property-read Il|null $il
 * @property-read Ilce|null $ilce
 * @property-read Mahalle|null $mahalle
 * @property-read IlanKategori|null $anaKategori
 * @property-read IlanKategori|null $altKategori
 * @property-read \Illuminate\Database\Eloquent\Collection|IlanPriceHistory[] $fiyatGecmisi
 * @property-read int|null $fiyat_gecmisi_count
 * @property-read \Illuminate\Database\Eloquent\Collection|IlanFotografi[] $fotograflar
 * @property-read int|null $fotograflar_count
 * @property-read mixed $kapak_fotografi
 */
class Ilan extends Model
{
    use Filterable, HasFactory, HasFeatures, SoftDeletes;

    protected $table = 'ilanlar';

    /**
     * Searchable fields for Filterable trait
     *
     * @var array
     */
    protected $searchable = ['baslik', 'aciklama'];

    /**
     * The attributes that are mass assignable.
     *
     * Context7 Compliance: Tüm field'lar database ile senkronize edildi (6 Kasım 2025)
     *
     * Field Kategorileri:
     * ✅ REQUIRED: Zorunlu field'lar (validation'da kontrol edilir)
     * ⚠️ CONDITIONAL: Koşullu gerekli (kategori/ilan tipine göre)
     * 🔵 OPTIONAL: Opsiyonel field'lar
     * 🟡 LEGACY: Eski sistemden kalan, deprecated field'lar
     * 🔴 EXCLUDED: Model'de yok ama database'de var (auto-managed: id, created_at, updated_at, deleted_at)
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // ======================================================================
        // ✅ REQUIRED FIELDS - Temel İlan Bilgileri
        // ======================================================================
        'baslik',                    // ✅ REQUIRED: İlan başlığı (varchar(255), NOT NULL)
        'aciklama',                  // ✅ REQUIRED: İlan açıklaması (text, NULL allowed)
        'fiyat',                     // ✅ REQUIRED: Ana fiyat bilgisi (decimal(15,2), NULL allowed)
        'price_text',                // ✅ REQUIRED: Fiyatın yazıyla gösterimi
        'para_birimi',               // ✅ REQUIRED: Para birimi (varchar(10), NOT NULL, default: TRY)
        'status',                    // ✅ REQUIRED: İlan durumu (varchar(255), NOT NULL, default: 'Aktif')
        'crm_only',
        'il_id',                     // ✅ REQUIRED: İl bilgisi (bigint unsigned, NULL allowed)
        'ilce_id',                   // ✅ REQUIRED: İlçe bilgisi (bigint unsigned, NULL allowed)
        'mahalle_id',                // ✅ REQUIRED: Mahalle bilgisi (bigint unsigned, NULL allowed)
        'ana_kategori_id',           // ✅ REQUIRED: Ana kategori (bigint unsigned, NULL allowed)
        'alt_kategori_id',          // ✅ REQUIRED: Alt kategori (bigint unsigned, NULL allowed)
        'yayin_tipi_id',            // ✅ REQUIRED: Yayın tipi (bigint unsigned, NULL allowed)

        // ======================================================================
        // ⚠️ CONDITIONAL FIELDS - Kategori/Tip Bazlı Gerekli Alanlar
        // ======================================================================

        // Arsa İçin Gerekli (kategori = arsa)
        'ada_no',                    // ⚠️ CONDITIONAL: Arsa için gerekli (varchar(50), NULL allowed)
        'parsel_no',                 // ⚠️ CONDITIONAL: Arsa için gerekli (varchar(50), NULL allowed)
        'ada_parsel',                // ⚠️ CONDITIONAL: Arsa için gerekli (varchar(100), NULL allowed)
        'imar_statusu',              // ⚠️ CONDITIONAL: Arsa için önemli (varchar(100), NULL allowed)
        'alan_m2',                   // ⚠️ CONDITIONAL: Arsa için gerekli (decimal(12,2), NULL allowed)
        'yola_cephe',                // ⚠️ CONDITIONAL: Arsa için önemli (tinyint(1), NOT NULL, default: 0)
        'altyapi_elektrik',          // ⚠️ CONDITIONAL: Arsa için önemli (tinyint(1), NOT NULL, default: 0)
        'altyapi_su',                // ⚠️ CONDITIONAL: Arsa için önemli (tinyint(1), NOT NULL, default: 0)
        'altyapi_dogalgaz',          // ⚠️ CONDITIONAL: Arsa için önemli (tinyint(1), NOT NULL, default: 0)
        'kaks',                      // ⚠️ CONDITIONAL: Arsa için önemli (decimal(5,2), NULL allowed)
        'taks',                      // ⚠️ CONDITIONAL: Arsa için önemli (decimal(5,2), NULL allowed)
        'gabari',                    // ⚠️ CONDITIONAL: Arsa için önemli (decimal(5,2), NULL allowed)

        // Daire/Villa İçin Gerekli (kategori = daire, villa)
        'oda_sayisi',                // ⚠️ CONDITIONAL: Daire/Villa için gerekli (int, NULL allowed)
        'banyo_sayisi',              // ⚠️ CONDITIONAL: Daire/Villa için gerekli (int, NULL allowed)
        'salon_sayisi',              // ⚠️ CONDITIONAL: Daire/Villa için önemli (int, NULL allowed)
        'net_m2',                    // ⚠️ CONDITIONAL: Daire/Villa için gerekli (decimal(10,2), NULL allowed)
        'brut_m2',                   // ⚠️ CONDITIONAL: Daire/Villa için gerekli (decimal(10,2), NULL allowed)
        'kat',                       // ⚠️ CONDITIONAL: Daire/Villa için önemli (int, NULL allowed)
        'toplam_kat',                // ⚠️ CONDITIONAL: Daire/Villa için önemli (int, NULL allowed)
        'bina_yasi',                 // ⚠️ CONDITIONAL: Daire/Villa için önemli (year, NULL allowed)
        'isitma',                    // ⚠️ CONDITIONAL: Daire/Villa için önemli (varchar(255), NULL allowed)
        'isinma_tipi',               // ⚠️ CONDITIONAL: Daire/Villa için önemli (varchar(255), NULL allowed)
        'esyali',                    // ⚠️ CONDITIONAL: Daire/Villa için önemli (tinyint(1), NOT NULL, default: 0)
        'site_ozellikleri',          // ⚠️ CONDITIONAL: Site içi için önemli (json, NULL allowed)
        'aidat',                     // ⚠️ CONDITIONAL: Daire/Villa için önemli (varchar(255), NULL allowed)

        // Yazlık Kiralama İçin Gerekli (kategori = yazlık)
        'gunluk_fiyat',              // ⚠️ CONDITIONAL: Yazlık için gerekli (decimal(10,2), NULL allowed)
        'haftalik_fiyat',            // ⚠️ CONDITIONAL: Yazlık için önemli (decimal(10,2), NULL allowed)
        'aylik_fiyat',               // ⚠️ CONDITIONAL: Yazlık için önemli (decimal(10,2), NULL allowed)
        'sezonluk_fiyat',            // ⚠️ CONDITIONAL: Yazlık için önemli (decimal(10,2), NULL allowed)
        'min_konaklama',             // ⚠️ CONDITIONAL: Yazlık için önemli (int, NULL allowed)
        'max_misafir',               // ⚠️ CONDITIONAL: Yazlık için önemli (int, NULL allowed)
        'temizlik_ucreti',           // ⚠️ CONDITIONAL: Yazlık için önemli (decimal(10,2), NULL allowed)
        'havuz',                     // ⚠️ CONDITIONAL: Yazlık için önemli (tinyint(1), NOT NULL, default: 0)
        'havuz_turu',                // ⚠️ CONDITIONAL: Havuz varsa gerekli (varchar(50), NULL allowed)
        'havuz_boyut',               // ⚠️ CONDITIONAL: Havuz varsa önemli (varchar(50), NULL allowed)
        'havuz_derinlik',            // ⚠️ CONDITIONAL: Havuz varsa önemli (decimal(5,2), NULL allowed)
        'sezon_baslangic',           // ⚠️ CONDITIONAL: Yazlık için önemli (date, NULL allowed)
        'sezon_bitis',               // ⚠️ CONDITIONAL: Yazlık için önemli (date, NULL allowed)
        'elektrik_dahil',            // ⚠️ CONDITIONAL: Yazlık için önemli (tinyint(1), NOT NULL, default: 0)
        'su_dahil',                  // ⚠️ CONDITIONAL: Yazlık için önemli (tinyint(1), NOT NULL, default: 0)

        // İşyeri İçin Gerekli (kategori = isyeri)
        'isyeri_tipi',               // ⚠️ CONDITIONAL: İşyeri için gerekli (varchar(255), NULL allowed)
        'kira_bilgisi',              // ⚠️ CONDITIONAL: İşyeri için önemli (text, NULL allowed)
        'ciro_bilgisi',              // ⚠️ CONDITIONAL: İşyeri için önemli (decimal(15,2), NULL allowed)
        'ruhsat_durumu',             // ⚠️ CONDITIONAL: İşyeri için önemli (varchar(255), NULL allowed)
        'personel_kapasitesi',       // ⚠️ CONDITIONAL: İşyeri için önemli (int, NULL allowed)
        'isyeri_cephesi',            // ⚠️ CONDITIONAL: İşyeri için önemli (int, NULL allowed)

        // ======================================================================
        // 🔵 OPTIONAL FIELDS - Opsiyonel Bilgiler
        // ======================================================================

        // İlişkisel Alanlar
        'ilan_sahibi_id',            // 🔵 OPTIONAL: İlan sahibi (kisi_id) - NULL allowed
        'ilgili_kisi_id',            // 🔵 OPTIONAL: İlgili kişi (kisi_id) - NULL allowed
        'danisman_id',               // 🔵 OPTIONAL: Danışman (user_id) - NULL allowed
        'user_id',                   // 🔵 OPTIONAL: User (user_id) - NULL allowed - legacy
        'kategori_id',               // 🔵 OPTIONAL: Legacy kategori (bigint unsigned) - deprecated
        'proje_id',                  // 🔵 OPTIONAL: Proje ID - NULL allowed
        'ulke_id',                   // 🔵 OPTIONAL: Ülke ID - NULL allowed

        // Adres Detayları
        'adres',                     // 🔵 OPTIONAL: Tam adres metni (varchar(255), NULL allowed)
        'lat',                       // 🔵 OPTIONAL: Latitude (decimal(10,8), NULL allowed) - database'de 'lat'
        'lng',                       // 🔵 OPTIONAL: Longitude (decimal(11,8), NULL allowed) - database'de 'lng'
        'latitude',                  // 🔵 OPTIONAL: Latitude alias - legacy
        'longitude',                 // 🔵 OPTIONAL: Longitude alias - legacy

        // Yapı Detayları
        'taban_alani',               // 🔵 OPTIONAL: Taban alanı (decimal(12,2), NULL allowed)
        'yola_cephesi',              // 🔵 OPTIONAL: Yola cephesi (decimal(8,2), NULL allowed)

        // İlan Yönetimi
        'ilan_no',                   // 🔵 OPTIONAL: İlan numarası (varchar(255), UNIQUE, NULL allowed)
        'referans_no',               // 🔵 OPTIONAL: Referans numarası (varchar(50), UNIQUE, NULL allowed)
        'dosya_adi',                 // 🔵 OPTIONAL: Dosya adı (varchar(255), NULL allowed)
        'slug',                      // 🔵 OPTIONAL: SEO slug - auto-generated
        'goruntulenme',              // 🔵 OPTIONAL: Görüntülenme sayısı (int, NOT NULL, default: 0)

        // Portal Entegrasyonları
        'sahibinden_id',             // 🔵 OPTIONAL: Sahibinden portal ID (varchar(50), NULL allowed)
        'emlakjet_id',               // 🔵 OPTIONAL: Emlakjet portal ID (varchar(50), NULL allowed)
        'hepsiemlak_id',             // 🔵 OPTIONAL: Hepsiemlak portal ID (varchar(50), NULL allowed)
        'zingat_id',                 // 🔵 OPTIONAL: Zingat portal ID (varchar(50), NULL allowed)
        'hurriyetemlak_id',          // 🔵 OPTIONAL: Hurriyetemlak portal ID (varchar(50), NULL allowed)
        'portal_sync_status',        // 🔵 OPTIONAL: Portal senkronizasyon durumu (json, NULL allowed)
        'portal_pricing',            // 🔵 OPTIONAL: Portal fiyatlandırma bilgileri (json, NULL allowed)

        // Anahtar Yönetimi
        'anahtar_kimde',             // 🔵 OPTIONAL: Anahtar kimde bilgisi (varchar(255), NULL allowed)
        'anahtar_turu',              // 🔵 OPTIONAL: Anahtar türü (enum: mal_sahibi, danisman, kapici, emlakci, yonetici, diger)
        'anahtar_notlari',           // 🔵 OPTIONAL: Anahtar notları (text, NULL allowed)
        'anahtar_ulasilabilirlik',   // 🔵 OPTIONAL: Anahtar ulaşılabilirlik (varchar(100), NULL allowed)
        'anahtar_ek_bilgi',          // 🔵 OPTIONAL: Anahtar ek bilgi (varchar(255), NULL allowed)

        // Medya
        'youtube_video_url',         // 🔵 OPTIONAL: YouTube video URL
        'sanal_tur_url',             // 🔵 OPTIONAL: Sanal tur URL
        'video_url',                 // 🔵 OPTIONAL: Pazarlama videosu URL
        'video_status',              // 🔵 OPTIONAL: Video render durumu (none, queued, rendering, completed, failed)
        'video_last_frame',          // 🔵 OPTIONAL: Render ilerlemesi (0-100)

        // TurkiyeAPI + WikiMapia Integration (5 Kasım 2025)
        'location_type',             // 🔵 OPTIONAL: Lokasyon tipi (mahalle, belde, koy)
        'location_data',             // 🔵 OPTIONAL: TurkiyeAPI data (JSON)
        'wikimapia_place_id',        // 🔵 OPTIONAL: WikiMapia site/place ID
        'environmental_scores',      // 🔵 OPTIONAL: Hesaplanan skorlar (JSON)
        'nearby_places',             // 🔵 OPTIONAL: Yakın yerler özeti (JSON)

        // ======================================================================
        // 🟡 LEGACY FIELDS - Eski Sistemden Kalan (Deprecated)
        // ======================================================================
        'ilan_basligi',              // 🟡 LEGACY: İlan başlığı için - 'baslik' kullanılmalı
        'ilan_aciklamasi',           // 🟡 LEGACY: İlan açıklaması için - 'aciklama' kullanılmalı
        'yayinlama_tipi',            // 🟡 LEGACY: String field - 'yayin_tipi_id' kullanılmalı
        'fiyat_orijinal',            // 🟡 LEGACY: Orijinal fiyat - çoklu para birimi desteği için
        'fiyat_try_cached',          // 🟡 LEGACY: TRY cache fiyatı - çoklu para birimi desteği için
        'para_birimi_orijinal',      // 🟡 LEGACY: Orijinal para birimi - çoklu para birimi desteği için
        'kur_orani',                 // 🟡 LEGACY: Kur oranı - çoklu para birimi desteği için
        'kur_tarihi',                // 🟡 LEGACY: Kur tarihi - çoklu para birimi desteği için
        'ilan_tarihi',               // 🟡 LEGACY: İlan tarihi - 'created_at' kullanılmalı
        'view_count',                // 🟡 LEGACY: Görüntülenme sayısı - 'goruntulenme' kullanılmalı
        'favorite_count',            // 🟡 LEGACY: Favori sayısı - artık kullanılmıyor
        'son_islem_tarihi',          // 🟡 LEGACY: Son işlem tarihi
        'son_islem_fiyati',          // 🟡 LEGACY: Son işlem fiyatı
        'islem_tipi',                // 🟡 LEGACY: İşlem tipi
        'balkon_sayisi',             // 🟡 LEGACY: Balkon sayısı - artık kullanılmıyor
        'brut_alan',                 // 🟡 LEGACY: Brut alan - 'brut_m2' kullanılmalı
        'net_alan',                  // 🟡 LEGACY: Net alan - 'net_m2' kullanılmalı
        'yas',                       // 🟡 LEGACY: Yaş - 'bina_yasi' kullanılmalı
        'isitma_tipi',               // 🟡 LEGACY: Isıtma tipi - 'isinma_tipi' veya 'isitma' kullanılmalı
        'yakit_tipi',                // 🟡 LEGACY: Yakıt tipi - artık kullanılmıyor
        'esya_statusu',              // 🟡 LEGACY: Eşya durumu - 'esyali' boolean kullanılmalı
        'site_icerisinde',           // 🟡 LEGACY: Site içinde - 'site_ozellikleri' kullanılmalı
        'kredi_uygun',               // 🟡 LEGACY: Krediye uygun - artık kullanılmıyor
        'takas_uygun',               // 🟡 LEGACY: Takasa uygun - artık kullanılmıyor
        'tapu_statusu',              // 🟡 LEGACY: Tapu durumu - artık kullanılmıyor
        'hisse_orani',               // 🟡 LEGACY: Hisse oranı - artık kullanılmıyor
        'cephe_sayisi',              // 🟡 LEGACY: Cephe sayısı - artık kullanılmıyor
        'ifraz_durumu',              // 🟡 LEGACY: İfraz durumu - artık kullanılmıyor
        'tapu_durumu',               // 🟡 LEGACY: Tapu durumu - artık kullanılmıyor
        'yol_durumu',                // 🟡 LEGACY: Yol durumu - artık kullanılmıyor
        'ifrazsiz',                  // 🟡 LEGACY: İfrazsiz - artık kullanılmıyor
        'kat_karsiligi',             // 🟡 LEGACY: Kat karşılığı - artık kullanılmıyor
        'tapu_tipi',                 // 🟡 LEGACY: Tapu tipi - artık kullanılmıyor
        'krediye_uygun',             // 🟡 LEGACY: Krediye uygun - artık kullanılmıyor
        'dynamic_fields',            // 🟡 LEGACY: Dinamik field'lar (JSON) - artık kullanılmıyor
        'adres_mahalle',             // 🟡 LEGACY: Adres mahalle - 'mahalle_id' kullanılmalı
        'adres_detay',               // 🟡 LEGACY: Adres detay - 'adres' kullanılmalı
        'sokak',                     // 🟡 LEGACY: Sokak - artık kullanılmıyor
        'cadde',                     // 🟡 LEGACY: Cadde - artık kullanılmıyor
        'bulvar',                    // 🟡 LEGACY: Bulvar - artık kullanılmıyor
        'bina_no',                   // 🟡 LEGACY: Bina numarası - artık kullanılmıyor
        'daire_no',                  // 🟡 LEGACY: Daire numarası - artık kullanılmıyor
        'posta_kodu',                // 🟡 LEGACY: Posta kodu - artık kullanılmıyor
        'nearby_distances',          // 🟡 LEGACY: Yakın mesafeler (JSON) - artık kullanılmıyor
        'boundary_geojson',          // 🟡 LEGACY: Boundary GeoJSON - artık kullanılmıyor
        'boundary_area',             // 🟡 LEGACY: Boundary alanı - artık kullanılmıyor
        'elektrik_altyapisi',        // 🟡 LEGACY: Elektrik altyapısı - 'altyapi_elektrik' kullanılmalı
        'su_altyapisi',              // 🟡 LEGACY: Su altyapısı - 'altyapi_su' kullanılmalı
        'dogalgaz_altyapisi',        // 🟡 LEGACY: Doğalgaz altyapısı - 'altyapi_dogalgaz' kullanılmalı
        'havuz_var',                 // 🟡 LEGACY: Havuz var - 'havuz' boolean kullanılmalı
        'ozel_notlar',               // 🟡 LEGACY: Özel notlar - artık kullanılmıyor
        'musteri_notlari',           // 🟡 LEGACY: Müşteri notları - artık kullanılmıyor
        'indirimli_fiyat',           // 🟡 LEGACY: İndirimli fiyat - artık kullanılmıyor
        'indirim_notlari',           // 🟡 LEGACY: İndirim notları - artık kullanılmıyor
        'sahip_ozel_notlari',        // 🟡 LEGACY: Sahip özel notları - artık kullanılmıyor
        'sahip_iletisim_tercihi',     // 🟡 LEGACY: Sahip iletişim tercihi - artık kullanılmıyor
        'eids_onayli',               // 🟡 LEGACY: EİDS onaylı - artık kullanılmıyor
        'eids_onay_tarihi',          // 🟡 LEGACY: EİDS onay tarihi - artık kullanılmıyor
        'eids_belge_no',             // 🟡 LEGACY: EİDS belge no - artık kullanılmıyor

        // ======================================================================
        // 🔴 EXCLUDED FIELDS - Auto-managed (Model'de yok ama database'de var)
        // ======================================================================
        // 'id' - Auto-increment primary key
        // 'created_at' - Auto-managed timestamp
        // 'updated_at' - Auto-managed timestamp
        // 'deleted_at' - Soft delete timestamp
    ];

    /**
     * The attributes that should be cast.
     *
     * Context7 Compliance: Tüm field'lar database type'larına göre cast edildi (6 Kasım 2025)
     *
     * @var array<string, string>
     */
    protected $casts = [
        // ======================================================================
        // ✅ REQUIRED FIELDS - Casts
        // ======================================================================
        'fiyat' => 'float',                          // ✅ REQUIRED: decimal(15,2) → float
        'status' => 'string',
        'crm_only' => 'boolean',
        'para_birimi' => 'string',                   // ✅ REQUIRED: varchar(10) → string
        'baslik' => 'string',                        // ✅ REQUIRED: varchar(255) → string
        'aciklama' => 'string',                      // ✅ REQUIRED: text → string

        // ======================================================================
        // ⚠️ CONDITIONAL FIELDS - Casts
        // ======================================================================

        // Arsa İçin
        'ada_no' => 'string',                        // ⚠️ CONDITIONAL: varchar(50) → string
        'parsel_no' => 'string',                     // ⚠️ CONDITIONAL: varchar(50) → string
        'ada_parsel' => 'string',                    // ⚠️ CONDITIONAL: varchar(100) → string
        'imar_statusu' => 'string',                  // ⚠️ CONDITIONAL: varchar(100) → string
        'alan_m2' => 'float',                        // ⚠️ CONDITIONAL: decimal(12,2) → float
        'yola_cephe' => 'boolean',                   // ⚠️ CONDITIONAL: tinyint(1) → boolean
        'altyapi_elektrik' => 'boolean',             // ⚠️ CONDITIONAL: tinyint(1) → boolean
        'altyapi_su' => 'boolean',                   // ⚠️ CONDITIONAL: tinyint(1) → boolean
        'altyapi_dogalgaz' => 'boolean',             // ⚠️ CONDITIONAL: tinyint(1) → boolean
        'kaks' => 'float',                           // ⚠️ CONDITIONAL: decimal(5,2) → float
        'taks' => 'float',                           // ⚠️ CONDITIONAL: decimal(5,2) → float
        'gabari' => 'float',                         // ⚠️ CONDITIONAL: decimal(5,2) → float
        'taban_alani' => 'float',                    // ⚠️ CONDITIONAL: decimal(12,2) → float
        'yola_cephesi' => 'float',                   // ⚠️ CONDITIONAL: decimal(8,2) → float

        // Daire/Villa İçin
        'oda_sayisi' => 'integer',                   // ⚠️ CONDITIONAL: int → integer
        'banyo_sayisi' => 'integer',                 // ⚠️ CONDITIONAL: int → integer
        'salon_sayisi' => 'integer',                 // ⚠️ CONDITIONAL: int → integer
        'net_m2' => 'float',                         // ⚠️ CONDITIONAL: decimal(10,2) → float
        'brut_m2' => 'float',                        // ⚠️ CONDITIONAL: decimal(10,2) → float
        'kat' => 'integer',                          // ⚠️ CONDITIONAL: int → integer
        'toplam_kat' => 'integer',                   // ⚠️ CONDITIONAL: int → integer
        'bina_yasi' => 'integer',                    // ⚠️ CONDITIONAL: year → integer
        'isitma' => 'string',                        // ⚠️ CONDITIONAL: varchar(255) → string
        'isinma_tipi' => 'string',                   // ⚠️ CONDITIONAL: varchar(255) → string
        'esyali' => 'boolean',                       // ⚠️ CONDITIONAL: tinyint(1) → boolean
        'site_ozellikleri' => 'array',                // ⚠️ CONDITIONAL: json → array
        'aidat' => 'string',                         // ⚠️ CONDITIONAL: varchar(255) → string

        // Yazlık Kiralama İçin
        'gunluk_fiyat' => 'float',                   // ⚠️ CONDITIONAL: decimal(10,2) → float
        'haftalik_fiyat' => 'float',                 // ⚠️ CONDITIONAL: decimal(10,2) → float
        'aylik_fiyat' => 'float',                    // ⚠️ CONDITIONAL: decimal(10,2) → float
        'sezonluk_fiyat' => 'float',                 // ⚠️ CONDITIONAL: decimal(10,2) → float
        'min_konaklama' => 'integer',                // ⚠️ CONDITIONAL: int → integer
        'max_misafir' => 'integer',                  // ⚠️ CONDITIONAL: int → integer
        'temizlik_ucreti' => 'float',                // ⚠️ CONDITIONAL: decimal(10,2) → float
        'havuz' => 'boolean',                        // ⚠️ CONDITIONAL: tinyint(1) → boolean
        'havuz_turu' => 'string',                    // ⚠️ CONDITIONAL: varchar(50) → string
        'havuz_boyut' => 'string',                   // ⚠️ CONDITIONAL: varchar(50) → string
        'havuz_derinlik' => 'float',                  // ⚠️ CONDITIONAL: decimal(5,2) → float
        'sezon_baslangic' => 'date',                 // ⚠️ CONDITIONAL: date → date
        'sezon_bitis' => 'date',                     // ⚠️ CONDITIONAL: date → date
        'elektrik_dahil' => 'boolean',               // ⚠️ CONDITIONAL: tinyint(1) → boolean
        'su_dahil' => 'boolean',                     // ⚠️ CONDITIONAL: tinyint(1) → boolean

        // İşyeri İçin
        'isyeri_tipi' => 'string',                   // ⚠️ CONDITIONAL: varchar(255) → string
        'kira_bilgisi' => 'string',                  // ⚠️ CONDITIONAL: text → string
        'ciro_bilgisi' => 'float',                   // ⚠️ CONDITIONAL: decimal(15,2) → float
        'ruhsat_durumu' => 'string',                 // ⚠️ CONDITIONAL: varchar(255) → string
        'personel_kapasitesi' => 'integer',          // ⚠️ CONDITIONAL: int → integer
        'isyeri_cephesi' => 'integer',               // ⚠️ CONDITIONAL: int → integer

        // ======================================================================
        // 🔵 OPTIONAL FIELDS - Casts
        // ======================================================================

        // İlişkisel Alanlar
        'ilan_sahibi_id' => 'integer',               // 🔵 OPTIONAL: bigint unsigned → integer
        'ilgili_kisi_id' => 'integer',               // 🔵 OPTIONAL: bigint unsigned → integer
        'danisman_id' => 'integer',                  // 🔵 OPTIONAL: bigint unsigned → integer
        'user_id' => 'integer',                      // 🔵 OPTIONAL: bigint unsigned → integer
        'kategori_id' => 'integer',                  // 🔵 OPTIONAL: bigint unsigned → integer (legacy)
        'proje_id' => 'integer',                     // 🔵 OPTIONAL: bigint unsigned → integer
        'ulke_id' => 'integer',                      // 🔵 OPTIONAL: bigint unsigned → integer
        'il_id' => 'integer',                        // 🔵 OPTIONAL: bigint unsigned → integer
        'ilce_id' => 'integer',                      // 🔵 OPTIONAL: bigint unsigned → integer
        'mahalle_id' => 'integer',                   // 🔵 OPTIONAL: bigint unsigned → integer
        'ana_kategori_id' => 'integer',              // 🔵 OPTIONAL: bigint unsigned → integer
        'alt_kategori_id' => 'integer',              // 🔵 OPTIONAL: bigint unsigned → integer
        'yayin_tipi_id' => 'integer',                // 🔵 OPTIONAL: bigint unsigned → integer

        // Adres Detayları
        'adres' => 'string',                         // 🔵 OPTIONAL: varchar(255) → string
        'lat' => 'float',                            // 🔵 OPTIONAL: decimal(10,8) → float
        'lng' => 'float',                            // 🔵 OPTIONAL: decimal(11,8) → float
        'latitude' => 'float',                       // 🔵 OPTIONAL: decimal(10,8) → float (legacy)
        'longitude' => 'float',                      // 🔵 OPTIONAL: decimal(11,8) → float (legacy)

        // Çevresel Bilgiler (POI & Tags)
        'environment_pois' => 'array',               // 🔵 OPTIONAL: json → array (POI listesi)
        'environment_tags' => 'array',               // 🔵 OPTIONAL: json → array (Çevresel etiketler)

        // İlan Yönetimi
        'ilan_no' => 'string',                       // 🔵 OPTIONAL: varchar(255) → string
        'referans_no' => 'string',                   // 🔵 OPTIONAL: varchar(50) → string
        'dosya_adi' => 'string',                     // 🔵 OPTIONAL: varchar(255) → string
        'slug' => 'string',                          // 🔵 OPTIONAL: varchar(255) → string
        'goruntulenme' => 'integer',                 // 🔵 OPTIONAL: int → integer

        // Anahtar Yönetimi
        'anahtar_kimde' => 'string',                 // 🔵 OPTIONAL: varchar(255) → string
        'anahtar_turu' => 'string',                  // 🔵 OPTIONAL: enum → string
        'anahtar_notlari' => 'string',               // 🔵 OPTIONAL: text → string
        'anahtar_ulasilabilirlik' => 'string',       // 🔵 OPTIONAL: varchar(100) → string
        'anahtar_ek_bilgi' => 'string',              // 🔵 OPTIONAL: varchar(255) → string

        // Medya
        'youtube_video_url' => 'string',             // 🔵 OPTIONAL: varchar(255) → string
        'sanal_tur_url' => 'string',                 // 🔵 OPTIONAL: varchar(255) → string
        'video_url' => 'string',                     // 🔵 OPTIONAL: varchar(255) → string
        'video_status' => 'string',                  // 🔵 OPTIONAL: varchar(50) → string
        'video_last_frame' => 'integer',             // 🔵 OPTIONAL: tinyint → integer

        // TurkiyeAPI + WikiMapia Integration
        'location_type' => 'string',                 // 🔵 OPTIONAL: varchar(255) → string
        'location_data' => 'array',                  // 🔵 OPTIONAL: json → array
        'nearby_places' => 'array',                  // 🔵 OPTIONAL: json → array
        'wikimapia_place_id' => 'string',            // 🔵 OPTIONAL: varchar(255) → string
        'environmental_scores' => 'array',           // 🔵 OPTIONAL: json → array
        'price_text' => 'string',                    // 🔵 OPTIONAL: varchar(255) → string

        // Portal Entegrasyonları
        'sahibinden_id' => 'string',                 // 🔵 OPTIONAL: varchar(50) → string
        'emlakjet_id' => 'string',                   // 🔵 OPTIONAL: varchar(50) → string
        'hepsiemlak_id' => 'string',                 // 🔵 OPTIONAL: varchar(50) → string
        'zingat_id' => 'string',                     // 🔵 OPTIONAL: varchar(50) → string
        'hurriyetemlak_id' => 'string',              // 🔵 OPTIONAL: varchar(50) → string
        'portal_sync_status' => 'array',             // 🔵 OPTIONAL: json → array
        'portal_pricing' => 'array',                 // 🔵 OPTIONAL: json → array

        // ======================================================================
        // 🟡 LEGACY FIELDS - Casts
        // ======================================================================
        'ilan_tarihi' => 'datetime',                 // 🟡 LEGACY: datetime
        'son_islem_tarihi' => 'datetime',            // 🟡 LEGACY: datetime
        'fiyat_orijinal' => 'float',                 // 🟡 LEGACY: float
        'fiyat_try_cached' => 'float',                // 🟡 LEGACY: float
        'para_birimi_orijinal' => 'string',          // 🟡 LEGACY: string
        'kur_orani' => 'float',                      // 🟡 LEGACY: float
        'kur_tarihi' => 'date',                      // 🟡 LEGACY: date
        'view_count' => 'integer',                   // 🟡 LEGACY: integer
        'favorite_count' => 'integer',               // 🟡 LEGACY: integer
        'son_islem_fiyati' => 'float',               // 🟡 LEGACY: float
        'islem_tipi' => 'string',                    // 🟡 LEGACY: string
        'brut_alan' => 'float',                      // 🟡 LEGACY: float
        'net_alan' => 'float',                       // 🟡 LEGACY: float
        'yas' => 'integer',                          // 🟡 LEGACY: integer
        'isitma_tipi' => 'string',                   // 🟡 LEGACY: string
        'yakit_tipi' => 'string',                    // 🟡 LEGACY: string
        'esya_statusu' => 'string',                  // 🟡 LEGACY: string
        'site_icerisinde' => 'boolean',              // 🟡 LEGACY: boolean
        'kredi_uygun' => 'boolean',                  // 🟡 LEGACY: boolean
        'takas_uygun' => 'boolean',                  // 🟡 LEGACY: boolean
        'yayinlama_tipi' => 'string',                // 🟡 LEGACY: string
        'havuz_var' => 'boolean',                    // 🟡 LEGACY: boolean
        'elektrik_altyapisi' => 'boolean',           // 🟡 LEGACY: boolean
        'su_altyapisi' => 'boolean',                 // 🟡 LEGACY: boolean
        'dogalgaz_altyapisi' => 'boolean',           // 🟡 LEGACY: boolean
        'dynamic_fields' => 'array',                 // 🟡 LEGACY: array
        'nearby_distances' => 'array',                // 🟡 LEGACY: array
        'boundary_geojson' => 'array',               // 🟡 LEGACY: array
        'boundary_area' => 'float',                  // 🟡 LEGACY: float
    ];

    // ======================================================================
    // İLİŞKİLER (RELATIONSHIPS)
    // ======================================================================

    /**
     * İlanın sahibini (Mülk Sahibi) döndürür.
     */
    public function ilanSahibi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class, 'ilan_sahibi_id');
    }

    /**
     * İlanla ilgilenen kişiyi (Emlakçı, Kiracı adayı vb.) döndürür.
     */
    public function ilgiliKisi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class, 'ilgili_kisi_id');
    }

    /**
     * İlanın danışmanı ilişkisi
     */
    public function danisman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * User modeli ile danışman ilişkisi (Eloquent için)
     */
    public function userDanisman(): BelongsTo
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    // --- Adres İlişkileri ---

    public function ulke(): BelongsTo
    {
        return $this->belongsTo(Ulke::class, 'ulke_id');
    }

    public function il(): BelongsTo
    {
        return $this->belongsTo(Il::class, 'il_id');
    }

    public function ilce(): BelongsTo
    {
        return $this->belongsTo(Ilce::class, 'ilce_id');
    }

    public function mahalle(): BelongsTo
    {
        return $this->belongsTo(Mahalle::class, 'mahalle_id');
    }

    // --- Kategori İlişkileri ---

    public function anaKategori(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'ana_kategori_id');
    }

    public function altKategori(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'alt_kategori_id');
    }

    /**
     * Legacy parentKategori ilişkisi (geriye uyumluluk için)
     */
    public function parentKategori(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'parent_kategori_id');
    }

    /**
     * Yayın tipi ilişkisi
     * ✅ Context7: yayin_tipi_id → IlanKategoriYayinTipi tablosundan (ilan_kategori_yayin_tipleri)
     * ⚠️ DEPRECATED: Eski sistem (ilan_kategorileri seviye=2) artık kullanılmıyor
     */
    public function yayinTipi(): BelongsTo
    {
        // ✅ Context7: ilan_kategori_yayin_tipleri tablosunu kullan
        return $this->belongsTo(\App\Models\IlanKategoriYayinTipi::class, 'yayin_tipi_id');
    }

    /**
     * Yayın tipi ilişkisi (Legacy - ilan_kategorileri seviye=2)
     * ⚠️ DEPRECATED: Artık kullanılmıyor, yayinTipi() kullanılmalı
     */
    public function yayinTipiLegacy(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'yayin_tipi_id')
            ->where('seviye', 2);
    }

    /**
     * Demirbaşlar ilişkisi (pivot)
     * ✅ Context7: İlan ile demirbaşlar arasındaki ilişki
     */
    public function demirbaslar()
    {
        return $this->belongsToMany(Demirbas::class, 'ilan_demirbas', 'ilan_id', 'demirbas_id')
            ->withPivot(['brand', 'model', 'quantity', 'notes', 'display_order', 'status'])
            ->wherePivot('status', true)
            ->withTimestamps()
            ->orderByPivot('display_order');
    }

    /**
     * Demirbaşlar ilişkisi (tümü - status filtresi olmadan)
     */
    public function tumDemirbaslar()
    {
        return $this->belongsToMany(Demirbas::class, 'ilan_demirbas', 'ilan_id', 'demirbas_id')
            ->withPivot(['brand', 'model', 'quantity', 'notes', 'display_order', 'status'])
            ->withTimestamps()
            ->orderByPivot('display_order');
    }

    // --- Diğer İlişkiler ---

    /**
     * İlanın fiyat geçmişini döndürür.
     */
    public function fiyatGecmisi(): HasMany
    {
        return $this->hasMany(IlanPriceHistory::class, 'ilan_id')->orderBy('created_at', 'desc');
    }

    /**
     * İlanın fotoğraflarını döndürür.
     */
    public function fotograflar(): HasMany
    {
        return $this->hasMany(IlanFotografi::class, 'ilan_id');
    }

    /**
     * Photo Model ile ilişki (Yeni Photo System)
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class)->ordered();
    }

    /**
     * Öne çıkan fotoğraf (Photo Model)
     */
    public function featuredPhoto()
    {
        return $this->hasOne(Photo::class)->where('is_featured', true);
    }

    /**
     * Events (Rezervasyonlar/Etkinlikler)
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Aktif rezervasyonlar
     */
    public function activeEvents()
    {
        return $this->hasMany(Event::class)->active();
    }

    /**
     * Sezonlar (Fiyatlandırma)
     */
    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }

    /**
     * Aktif sezonlar
     */
    public function activeSeasons()
    {
        return $this->hasMany(Season::class)->active();
    }

    /**
     * Yazlık rezervasyonları
     * Context7: Yazlık kiralama sistemi için rezervasyon ilişkisi
     */
    public function yazlikRezervasyonlar(): HasMany
    {
        return $this->hasMany(YazlikRezervasyon::class);
    }

    /**
     * Yazlık fiyatlandırmaları
     * Context7: Yazlık kiralama sistemi için sezonluk fiyatlandırma ilişkisi
     */
    public function yazlikFiyatlandirma(): HasMany
    {
        return $this->hasMany(YazlikFiyatlandirma::class);
    }

    /**
     * İlanın çevirilerini döndürür.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(IlanTranslation::class);
    }

    /**
     * İlanın kategorisini döndürür (Alt Kategori).
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(IlanKategori::class, 'alt_kategori_id');
    }

    /**
     * İlanın kullanıcısını döndürür.
     * Not: Bu danisman() ile aynı ilişki, tutarlılık için danisman() kullanın
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'danisman_id');
    }

    /**
     * İlanla ilişkili kişiyi döndürür.
     */
    public function kisi(): BelongsTo
    {
        return $this->belongsTo(Kisi::class, 'kisi_id');
    }

    /**
     * İlanın özelliklerini (features) döndürür.
     */
    public function ozellikler(): BelongsToMany
    {
        // TODO: listing_feature ile ilan_feature tablolarını tekilleştir.
        // Plan: Migration oluştur, listing_feature tablosunu ilan_feature'e merge et, eski tabloyu kaldır
        // Not: Bu değişiklik için veri migration gerekli, dikkatli yapılmalı
        return $this->belongsToMany(Feature::class, 'ilan_feature', 'ilan_id', 'feature_id')
            ->withPivot('value')
            ->withTimestamps();
    }

    /**
     * Features relationship (English alias for ozellikler)
     * Context7: English naming standard
     */
    public function features(): BelongsToMany
    {
        return $this->ozellikler();
    }

    /**
     * Geçiş süreci: eski tablonun kullanıldığı kayıtlar için alternatif ilişki.
     */
    public function ozelliklerLegacy(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'listing_feature', 'ilan_id', 'feature_id');
    }

    /**
     * İlanın etiketlerini döndürür.
     */
    public function etiketler(): BelongsToMany
    {
        return $this->belongsToMany(Etiket::class, 'ilan_etiketler')
            ->withPivot(['display_order', 'is_featured'])
            ->orderByPivot('display_order')
            ->withTimestamps();
    }

    public function getDurumAttribute()
    {
        return $this->status instanceof IlanStatus ? $this->status->value : $this->status;
    }

    public function setDurumAttribute($value)
    {
        $this->attributes['status'] = $value;
    }

    public function getAktifAttribute()
    {
        $s = $this->status;
        if ($s instanceof IlanStatus) {
            return $s->isActive();
        }

        return in_array($s, ['yayinda', 'Aktif'], true);
    }

    public function setAktifAttribute($value)
    {
        $this->attributes['status'] = $value ? IlanStatus::YAYINDA->value : IlanStatus::PASIF->value;
    }

    /**
     * İlanın takvim senkronizasyonlarını döndürür.
     */
    public function takvimSync()
    {
        return $this->hasMany(IlanTakvimSync::class, 'ilan_id');
    }

    /**
     * İlanın doluluk durumlarını döndürür (Yazlık için).
     */
    public function dolulukDurumlari()
    {
        return $this->hasMany(YazlikDolulukDurumu::class, 'ilan_id');
    }

    /**
     * İlanın yazlık detaylarını döndürür.
     */
    public function yazlikDetail()
    {
        return $this->hasOne(YazlikDetail::class, 'ilan_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(SiteApartman::class, 'site_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(IlanDocument::class, 'ilan_id')->orderBy('created_at', 'desc');
    }

    public function privateAudits()
    {
        return $this->hasMany(IlanPrivateAudit::class, 'ilan_id');
    }

    // ======================================================================
    // ERİŞİMCİLER & DEĞİŞTİRİCİLER (ACCESSORS & MUTATORS)
    // ======================================================================

    /**
     * Kapak fotoğrafını döndürür.
     */
    public function getKapakFotografiAttribute()
    {
        return $this->fotograflar()->where('kapak_fotografi', true)->first() ?? $this->fotograflar()->first();
    }

    /**
     * Kısa referans numarası (Müşteri için - Frontend)
     *
     * Format: Son 3 hane, 0 ile doldurulmuş
     * Örnek: 001, 234, 567
     *
     * Gemini AI Önerisi: Müşteri tarafında kısa, danışman arama yapınca bulur
     * Context7: REFNOMAT İK Sistemi
     *
     * @return string
     */
    public function getKisaReferansAttribute(): string
    {
        if (!$this->referans_no) {
            return '';
        }

        // YE-SAT-YALKVK-DAİRE-001234 → 234
        $parts = explode('-', $this->referans_no);
        $siraNo = end($parts);

        // Son 3 haneyi al ve 0 ile doldur
        return str_pad(substr($siraNo, -3), 3, '0', STR_PAD_LEFT);
        // Sonuç: 001, 234, 567
    }

    /**
     * Orta referans numarası (Danışman için - Hover/Tooltip)
     *
     * Format: Ref No: 001 Lokasyon Kategori Site (Mal Sahibi)
     * Örnek: Ref No: 001 Yalıkavak Satılık Daire Ülkerler Sitesi (Ahmet Yılmaz)
     *
     * Gemini AI Önerisi: Danışman hover'da görür, kopyalar
     * Yalıhan Bekçi: Frontend görünüm için optimize edilmiş format
     *
     * @return string
     */
    public function getOrtaReferansAttribute(): string
    {
        $parts = [];

        // Kısa referans
        $parts[] = 'Ref No: ' . $this->kisa_referans;

        // Lokasyon
        if ($this->mahalle) {
            $parts[] = $this->mahalle->mahalle_adi;
        } elseif ($this->ilce) {
            $parts[] = $this->ilce->ilce_adi;
        }

        // Yayın Tipi
        if ($this->yayinTipi) {
            $parts[] = $this->yayinTipi->name;
        }

        // Kategori
        if ($this->altKategori) {
            $parts[] = $this->altKategori->name;
        } elseif ($this->anaKategori) {
            $parts[] = $this->anaKategori->name;
        }

        // Site
        if ($this->site) {
            $parts[] = $this->site->name;
        }

        // Mal Sahibi (Parantez içinde)
        if ($this->ilanSahibi) {
            $sahip = trim($this->ilanSahibi->ad . ' ' . $this->ilanSahibi->soyad);
            $parts[] = "({$sahip})";
        }

        return implode(' ', array_filter($parts));
    }

    /**
     * Uzun referans numarası (Sistem için - Dosya Adı)
     *
     * Format: Ref YE-SAT-YALKVK-DAİRE-001234 - Yalıkavak Satılık...
     *
     * Gemini AI Önerisi: Dosya oluşturma ve arşivleme için
     * Context7: REFNOMATİK tam format
     *
     * @return string
     */
    public function getUzunReferansAttribute(): string
    {
        return $this->dosya_adi ?? $this->referans_no ?? '';
    }

    /**
     * Tam adres metnini oluşturur.
     */
    public function getTamAdresAttribute(): string
    {
        $adresParcalari = [
            $this->mahalle->mahalle_adi ?? null,
            $this->ilce->ilce_adi ?? null,
            $this->il->il_adi ?? null,
            $this->ulke->ulke_adi ?? null,
        ];

        return implode(', ', array_filter($adresParcalari));
    }

    /**
     * Owner private data (encrypted JSON)
     * { desired_price_min, desired_price_max, notes }
     */
    public function getOwnerPrivateDataAttribute(): array
    {
        $enc = $this->owner_private_encrypted ?? null;
        if (! $enc) {
            return [];
        }
        try {
            $json = Crypt::decryptString($enc);
            $arr = json_decode($json, true);

            return is_array($arr) ? $arr : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function setOwnerPrivateDataAttribute($value): void
    {
        try {
            $json = json_encode($value ?? [], JSON_UNESCAPED_UNICODE);
            $this->attributes['owner_private_encrypted'] = Crypt::encryptString($json);
        } catch (\Throwable $e) {
            $this->attributes['owner_private_encrypted'] = null;
        }
    }

    // ======================================================================
    // KAPSAMLAR (SCOPES)
    // ======================================================================

    /**
     * Sadece status olan ilanları getiren scope.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['yayinda', 'Aktif']);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['onay_bekliyor', 'Beklemede']);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePublic($query)
    {
        return $query->where('crm_only', false)->whereIn('status', ['yayinda', 'Aktif']);
    }

    /**
     * Belirli bir kategoriye ait ilanları getiren scope.
     */
    public function scopeKategoriyeGore($query, $kategoriId)
    {
        return $query->where('ana_kategori_id', $kategoriId)
            ->orWhere('alt_kategori_id', $kategoriId);
    }

    /**
     * Ana kategoriye göre filtreleme scope'u
     * Context7: Ana kategori ile ilanları getirir
     */
    public function scopeAnaKategoriyeGore($query, $kategoriId)
    {
        return $query->where('ana_kategori_id', $kategoriId);
    }

    /**
     * Alt kategoriye göre filtreleme scope'u
     * Context7: Alt kategori ile ilanları getirir
     */
    public function scopeAltKategoriyeGore($query, $kategoriId)
    {
        return $query->where('alt_kategori_id', $kategoriId);
    }

    /**
     * Yayın tipine göre filtreleme scope'u
     * Context7: Yayın tipi ile ilanları getirir
     */
    public function scopeYayinTipineGore($query, $yayinTipiId)
    {
        return $query->where('yayin_tipi_id', $yayinTipiId);
    }

    /**
     * Ana ve alt kategoriye göre filtreleme scope'u
     * Context7: Hem ana hem alt kategori ile ilanları getirir
     */
    public function scopeKategoriHiyerarsisineGore($query, $anaKategoriId, $altKategoriId = null)
    {
        $query->where('ana_kategori_id', $anaKategoriId);

        if ($altKategoriId) {
            $query->where('alt_kategori_id', $altKategoriId);
        }

        return $query;
    }

    public function scopeSort(Builder $query, ?string $sortBy = null, string $sortDirection = 'desc', string $defaultSort = 'created_at')
    {
        $sortBy = $sortBy ?: $defaultSort;
        $dir = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';
        $query->reorder();
        if ($sortBy === 'fiyat') {
            try {
                $driver = $query->getConnection()->getDriverName();
            } catch (\Throwable $e) {
                $driver = 'mysql';
            }
            if ($driver === 'sqlite') {
                if ($dir === 'desc') {
                    $query->orderByRaw('(0 + fiyat) DESC');
                } else {
                    $query->orderByRaw('(0 + fiyat) ASC');
                }
                $query->orderBy($defaultSort, $dir);
                $query->orderBy('id', $dir);
            } else {
                if ($dir === 'desc') {
                    $query->orderByRaw('(0 + fiyat) DESC');
                } else {
                    $query->orderByRaw('(0 + fiyat) ASC');
                }
                $query->orderBy($defaultSort, $dir);
                $query->orderBy('id', $dir);
            }

            return $query;
        }
        if ($this->getConnection()->getSchemaBuilder()->hasColumn($this->getTable(), $sortBy)) {
            return $query->orderBy($sortBy, $dir);
        }

        return $query->orderByDesc($defaultSort);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug) && ! empty($model->baslik)) {
                $model->slug = Str::slug($model->baslik . '-' . uniqid());
            }
        });
        static::updating(function ($model) {
            if (empty($model->slug) && ! empty($model->baslik)) {
                $model->slug = Str::slug($model->baslik . '-' . uniqid());
            }
        });
    }
}
