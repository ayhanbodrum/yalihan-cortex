# 🧪 Market Intelligence - Test Rehberi

**Tarih:** 2025-11-29  
**Versiyon:** 1.0.0  
**Durum:** ✅ Test Edilebilir

---

## 🎯 TEST EDİLEBİLİRLİK

### ✅ Test Edilebilir Özellikler

1. **API Endpoints** - Tüm endpoint'ler test edilebilir
2. **Model Metodları** - İlan yaşı analizi test edilebilir
3. **Query Scopes** - Yorgun/yeni ilan filtreleme test edilebilir
4. **Veri Senkronizasyonu** - Test verisi ile sync endpoint test edilebilir

---

## 🚀 HIZLI TEST

### 1. Otomatik Test Script'i

```bash
php tests/manual/test-market-intelligence.php
```

**Bu script şunları test eder:**
- ✅ Aktif bölgeleri getir endpoint'i
- ✅ Veri senkronizasyonu endpoint'i (test verisi ile)
- ✅ İlan yaşı analizi metodları
- ✅ Query scopes (yorgun/yeni ilan filtreleme)

### 2. Manuel cURL Testleri

#### Test 1: Aktif Bölgeleri Getir

```bash
curl -X GET "http://127.0.0.1:8000/api/admin/market-intelligence/active-regions" \
  -H "Accept: application/json"
```

**Beklenen Yanıt:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "il_id": 7,
            "il_adi": "Antalya",
            "ilce_id": 123,
            "ilce_adi": "Muratpaşa",
            "is_active": true,
            "priority": 1
        }
    ],
    "message": "Aktif bölgeler listelendi"
}
```

#### Test 2: Veri Senkronizasyonu (Test Verisi)

```bash
curl -X POST "http://127.0.0.1:8000/api/admin/market-intelligence/sync" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "source": "sahibinden",
    "region": {
        "il_id": 7,
        "ilce_id": 123
    },
    "listings": [
        {
            "external_id": "TEST_123456",
            "url": "https://sahibinden.com/ilan/test-123456",
            "title": "Test İlan - Deniz Manzaralı 3+1 Daire",
            "price": 1500000,
            "currency": "TRY",
            "location_il": "Antalya",
            "location_ilce": "Muratpaşa",
            "location_mahalle": "Konyaaltı",
            "m2_brut": 120,
            "m2_net": 100,
            "room_count": "3+1",
            "listing_date": "2025-11-15",
            "snapshot_data": {
                "test": true
            }
        }
    ]
}'
```

**Beklenen Yanıt:**
```json
{
    "success": true,
    "data": {
        "synced_count": 1,
        "new_count": 1,
        "updated_count": 0,
        "source": "sahibinden"
    },
    "message": "1 ilan senkronize edildi (1 yeni, 0 güncellendi)"
}
```

---

## 🔧 PHP ARTISAN TINKER İLE TEST

### 1. İlan Yaşı Analizi

```php
php artisan tinker

// İlan bul
$listing = \App\Models\MarketListing::first();

// İlan yaşını hesapla
$age = $listing->getAgeInDays();
// => 45 (gün)

// Yorgun mu?
$isTired = $listing->isTired();
// => true (30+ gün)

// Kategori
$category = $listing->getAgeCategory();
// => "yorgun"
```

### 2. Query Scopes Testi

```php
// Yorgun ilanlar (30+ gün)
$tiredListings = \App\Models\MarketListing::tired()->get();
echo "Yorgun ilan sayısı: " . $tiredListings->count();

// Yeni ilanlar (0-7 gün)
$newListings = \App\Models\MarketListing::new()->get();
echo "Yeni ilan sayısı: " . $newListings->count();

// Belirli yaş aralığı (15-45 gün)
$listings = \App\Models\MarketListing::ageBetween(15, 45)->get();
echo "15-45 gün arası ilan sayısı: " . $listings->count();
```

### 3. Test Verisi Oluşturma

```php
// Yeni test ilanı oluştur
$listing = \App\Models\MarketListing::create([
    'source' => 'sahibinden',
    'external_id' => 'TEST_' . time(),
    'title' => 'Test İlan',
    'price' => 1500000,
    'currency' => 'TRY',
    'location_il' => 'Antalya',
    'location_ilce' => 'Muratpaşa',
    'm2_brut' => 120,
    'm2_net' => 100,
    'room_count' => '3+1',
    'listing_date' => now()->subDays(45), // 45 gün önce (yorgun)
    'status' => 1,
]);

// İlan yaşını kontrol et
echo "İlan yaşı: " . $listing->getAgeInDays() . " gün";
echo "Yorgun mu? " . ($listing->isTired() ? 'Evet' : 'Hayır');
echo "Kategori: " . $listing->getAgeCategory();
```

---

## ✅ YALIHAN BEKÇİ UYUMLULUK KONTROLÜ

### Context7 Standartları Kontrolü

#### ✅ ResponseService Kullanımı

**Kontrol:** Tüm API endpoint'leri `ResponseService` kullanıyor mu?

**Sonuç:** ✅ **UYUMLU**

```php
// ✅ DOĞRU
return ResponseService::success($data, 'Mesaj');
return ResponseService::error('Hata mesajı');
return ResponseService::validationError($errors);

// ❌ YANLIŞ (Yasaklı)
return response()->json(['success' => true]);
```

**Kontrol Edilen Dosyalar:**
- `app/Http/Controllers/Admin/MarketIntelligenceController.php` - ✅ Tüm metodlar `ResponseService` kullanıyor

#### ✅ Database Field Naming

**Kontrol:** Yasaklı field isimleri kullanılıyor mu?

**Yasaklı:** `enabled`, `aktif`, `durum`, `order`, `musteri_id`, `sehir_id`  
**Zorunlu:** `status`, `display_order`, `kisi_id`, `il_id`

**Sonuç:** ✅ **UYUMLU**

```php
// ✅ DOĞRU
'status' => 1, // tinyInteger boolean
'il_id' => 7,
'kisi_id' => 123,

// ❌ YANLIŞ (Yasaklı)
'enabled' => 1,
'aktif' => 1,
'durum' => 'aktif',
'order' => 1,
'musteri_id' => 123,
'sehir_id' => 7,
```

**Kontrol Edilen Dosyalar:**
- `app/Models/MarketListing.php` - ✅ `status` kullanılıyor (enabled değil)
- `database/migrations/2025_11_27_011644_create_market_listings_table.php` - ✅ `status` tinyInteger

#### ✅ Error Handling

**Kontrol:** Try-catch ve LogService kullanılıyor mu?

**Sonuç:** ✅ **UYUMLU**

```php
// ✅ DOĞRU
try {
    // İşlem
} catch (\Exception $e) {
    LogService::error('Mesaj', [...], $e);
    return ResponseService::serverError('Hata mesajı', $e);
}
```

**Kontrol Edilen Dosyalar:**
- `app/Http/Controllers/Admin/MarketIntelligenceController.php` - ✅ Try-catch ve LogService kullanılıyor

#### ✅ Type Safety

**Kontrol:** Type hints ve null kontrolü var mı?

**Sonuç:** ✅ **UYUMLU**

```php
// ✅ DOĞRU
public function getAgeInDays(): ?int
{
    if (!$this->listing_date) {
        return null;
    }
    return now()->diffInDays($this->listing_date);
}
```

**Kontrol Edilen Dosyalar:**
- `app/Models/MarketListing.php` - ✅ Type hints ve null kontrolü var

#### ✅ Query Scopes

**Kontrol:** Eloquent scopes kullanılıyor mu? (Raw SQL yasak)

**Sonuç:** ✅ **UYUMLU**

```php
// ✅ DOĞRU
public function scopeTired($query)
{
    return $query->whereNotNull('listing_date')
        ->where('listing_date', '<=', now()->subDays(30));
}

// ❌ YANLIŞ (Yasaklı)
DB::select("SELECT * FROM market_listings WHERE ...");
```

**Kontrol Edilen Dosyalar:**
- `app/Models/MarketListing.php` - ✅ Eloquent scopes kullanılıyor

---

## 📊 UYUMLULUK RAPORU

### ✅ Uyumlu Özellikler

| Özellik | Durum | Kontrol |
|---------|-------|---------|
| ResponseService | ✅ | Tüm endpoint'ler |
| Database Fields | ✅ | status, il_id (enabled, sehir_id yok) |
| Error Handling | ✅ | Try-catch + LogService |
| Type Safety | ✅ | Type hints + null kontrolü |
| Query Scopes | ✅ | Eloquent (Raw SQL yok) |
| CSRF Exception | ✅ | Sync endpoint için |

### ❌ Uyumsuz Özellikler

**Bulunamadı!** Tüm özellikler Yalıhan Bekçi kurallarına uyumlu.

---

## 🎯 TEST SONUÇLARI

### Başarılı Testler

1. ✅ **API Endpoints** - Tüm endpoint'ler çalışıyor
2. ✅ **Model Metodları** - İlan yaşı analizi çalışıyor
3. ✅ **Query Scopes** - Yorgun/yeni ilan filtreleme çalışıyor
4. ✅ **Veri Senkronizasyonu** - Test verisi ile sync başarılı

### Beklenen Testler

1. ⏳ **Settings View** - Bölge seçim paneli (henüz oluşturulmadı)
2. ⏳ **n8n Bot Entegrasyonu** - Gerçek veri çekme (henüz entegre edilmedi)
3. ⏳ **Dashboard View** - İstatistikler görüntüleme (henüz oluşturulmadı)

---

## 📚 İLGİLİ DOSYALAR

### Test Dosyaları

- `tests/manual/test-market-intelligence.php` - Otomatik test script'i
- `docs/market-intelligence/TEST_REHBERI.md` - Bu dosya

### Backend Dosyaları

- `app/Http/Controllers/Admin/MarketIntelligenceController.php` - API controller
- `app/Models/MarketListing.php` - Model
- `app/Models/MarketIntelligenceSetting.php` - Settings model
- `routes/admin.php` - API routes

---

## ✅ ÖZET

**Test Edilebilirlik:** ✅ **TAM**
- API endpoint'leri test edilebilir
- Model metodları test edilebilir
- Query scopes test edilebilir

**Yalıhan Bekçi Uyumluluğu:** ✅ **%100 UYUMLU**
- ResponseService kullanılıyor
- Database field naming uyumlu
- Error handling uyumlu
- Type safety uyumlu
- Query scopes uyumlu

**Sonuç:** Sistem test edilebilir ve Yalıhan Bekçi kurallarına tam uyumlu!

---

**Son Güncelleme:** 2025-11-29  
**Versiyon:** 1.0.0  
**Durum:** ✅ Test Edilebilir ve Uyumlu






