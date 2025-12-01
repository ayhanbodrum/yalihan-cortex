# 📊 Pazar İstihbaratı (Market Intelligence) Altyapısı

**Tarih:** 2025-11-27  
**Durum:** ✅ Tamamlandı  
**Context7 Uyumluluk:** %100

---

## 🎯 Amaç

Dış kaynaklardan (Sahibinden, Hepsiemlak, Emlakjet) çekilecek piyasa verilerini saklamak için `market_listings` tablosunu ve modelini oluşturmak. Bu tablo, n8n botlarının dolduracağı "Büyük Veri" deposu olacak.

---

## 📋 Oluşturulan Dosyalar

### 1. Migration
**Dosya:** `database/migrations/2025_11_27_011644_create_market_listings_table.php`

**Özellikler:**
- ✅ Context7 standartlarına uygun
- ✅ `status`: TinyInteger (boolean) - 1: Yayında, 0: Kalktı/Satıldı
- ✅ JSON alanlar: `snapshot_data`, `price_history`
- ✅ Unique constraint: `['source', 'external_id']`
- ✅ Performans için optimize edilmiş index'ler

**Alanlar:**
```php
- id: BigIncrements
- source: String (Enum: 'sahibinden', 'hepsiemlak', 'emlakjet') - Index
- external_id: String - Index
- url: String (500 karakter)
- title: String (500 karakter)
- price: Decimal(15, 2)
- currency: String (Default: 'TRY')
- location_il: String (100 karakter)
- location_ilce: String (100 karakter)
- location_mahalle: String (100 karakter)
- m2_brut: Integer
- m2_net: Integer
- room_count: String (20 karakter, örn: '3+1')
- listing_date: Date
- last_seen_at: Timestamp
- status: TinyInteger (Default: 1) - Index
- snapshot_data: JSON
- price_history: JSON
- timestamps
```

**Index'ler:**
- `source` (tek)
- `external_id` (tek)
- `['source', 'external_id']` (composite, unique constraint için)
- `status`
- `last_seen_at`
- `['location_il', 'location_ilce']` (composite)

**Unique Constraint:**
- `['source', 'external_id']` - Aynı kaynaktan aynı external_id sadece bir kez olabilir

### 2. Model
**Dosya:** `app/Models/MarketListing.php`

**Özellikler:**
- ✅ Context7 standartlarına uygun
- ✅ Tüm alanlar `$fillable` içinde
- ✅ JSON alanlar array olarak cast edilmiş
- ✅ `status` boolean olarak cast edilmiş
- ✅ Scope'lar: `active()`, `source()`, `lastSeenAfter()`, `lastSeenBefore()`
- ✅ Helper metodlar: `addPriceHistory()`, `isActive()`, `isInactive()`

**Casts:**
```php
'price' => 'decimal:2',
'm2_brut' => 'integer',
'm2_net' => 'integer',
'listing_date' => 'date',
'last_seen_at' => 'datetime',
'status' => 'boolean', // Context7: tinyInteger boolean
'snapshot_data' => 'array',
'price_history' => 'array',
```

**Scope'lar:**
- `scopeActive()` - Aktif ilanlar (status = 1)
- `scopeSource($source)` - Belirli kaynaktan gelen ilanlar
- `scopeLastSeenAfter($date)` - Son görülen tarih (sonra)
- `scopeLastSeenBefore($date)` - Son görülen tarih (önce)

**Helper Metodlar:**
- `addPriceHistory($price, $date)` - Fiyat geçmişine kayıt ekleme
- `isActive()` - İlanın aktif olup olmadığını kontrol
- `isInactive()` - İlanın pasif olup olmadığını kontrol

---

## 🔍 Context7 Uyumluluk Kontrolü

### ✅ Uyumlu Alanlar:
- `status`: TinyInteger (boolean cast) ✅
- JSON alanlar: Array cast ile uyumlu ✅
- Index'ler: Performans için optimize edilmiş ✅

### ✅ Yasaklı Alanlar Kontrolü:
- ❌ `order` - YOK
- ❌ `aktif` - YOK
- ❌ `enabled` - YOK
- ❌ `is_active` - YOK
- ❌ `musteri_id` - YOK
- ❌ `sehir_id` - YOK (String olarak `location_il` kullanıldı - dış kaynak olduğu için)

### ✅ Standartlar:
- Pure Tailwind CSS: N/A (Backend)
- ResponseService: N/A (Model)
- Database field naming: ✅ Context7 uyumlu

---

## 💡 Kullanım Örnekleri

### Temel Kullanım:
```php
// Aktif ilanları getir
$activeListings = MarketListing::active()->get();

// Sahibinden'den gelen ilanlar
$sahibindenListings = MarketListing::source('sahibinden')->get();

// Son 7 günde görülen ilanlar
$recentListings = MarketListing::lastSeenAfter(now()->subDays(7))->get();

// Fiyat geçmişine kayıt ekle
$listing->addPriceHistory(1500000, '2025-11-27');

// İlan durumunu kontrol et
if ($listing->isActive()) {
    // İlan hala yayında
}
```

### n8n Entegrasyonu:
```php
// n8n webhook'tan gelen veri
$data = request()->all();

// Yeni ilan kaydı
MarketListing::updateOrCreate(
    [
        'source' => 'sahibinden',
        'external_id' => $data['external_id'],
    ],
    [
        'url' => $data['url'],
        'title' => $data['title'],
        'price' => $data['price'],
        'currency' => $data['currency'] ?? 'TRY',
        'location_il' => $data['location_il'],
        'location_ilce' => $data['location_ilce'],
        'location_mahalle' => $data['location_mahalle'],
        'm2_brut' => $data['m2_brut'],
        'm2_net' => $data['m2_net'],
        'room_count' => $data['room_count'],
        'listing_date' => $data['listing_date'],
        'last_seen_at' => now(),
        'status' => 1,
        'snapshot_data' => $data, // Ham veri
    ]
);
```

---

## 🚀 Sonraki Adımlar

1. **n8n Webhook Endpoint'i:**
   - `app/Http/Controllers/Api/MarketIntelligenceController.php` oluştur
   - `POST /api/admin/market-intelligence/sync` endpoint'i ekle
   - `ResponseService::success()` kullan

2. **Job Oluşturma:**
   - `app/Jobs/SyncMarketListing.php` oluştur
   - n8n'den gelen verileri işle
   - Fiyat değişikliklerini `price_history`'ye ekle

3. **Scheduled Task:**
   - `app/Console/Commands/SyncMarketListings.php` oluştur
   - Günlük/haftalık senkronizasyon
   - Pasif ilanları işaretle

4. **Analiz Dashboard:**
   - Piyasa analizi için controller/view
   - Fiyat trend grafikleri
   - Lokasyon bazlı istatistikler

---

## 📊 Veri Yapısı

### price_history JSON Formatı:
```json
[
  {
    "date": "2025-11-27",
    "price": 1500000
  },
  {
    "date": "2025-11-28",
    "price": 1550000
  }
]
```

### snapshot_data JSON Formatı:
```json
{
  "external_id": "123456",
  "title": "Satılık Daire",
  "price": 1500000,
  "currency": "TRY",
  "location": {
    "il": "Antalya",
    "ilce": "Muratpaşa",
    "mahalle": "Konyaaltı"
  },
  "properties": {
    "m2_brut": 120,
    "m2_net": 100,
    "room_count": "3+1"
  },
  "raw_data": {
    // Ham veri (n8n'den gelen tüm veri)
  }
}
```

---

## ✅ Doğrulama

- ✅ Context7 validation: PASSED (0 violations)
- ✅ Linter errors: 0 errors
- ✅ Database schema: Optimize edilmiş index'ler
- ✅ Model: Tüm scope'lar ve helper metodlar çalışıyor
- ✅ Yalıhan Bekçi kuralları: Uyumlu

---

**Son Güncelleme:** 2025-11-27  
**Durum:** Production'a hazır ✅







