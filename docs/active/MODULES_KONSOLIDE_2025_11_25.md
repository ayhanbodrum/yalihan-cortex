# 🏗️ EmlakPro Modülleri - Konsolide Dokümantasyon

**Son Güncelleme:** 25 Kasım 2025  
**Context7 Standardı:** C7-MODULES-KONSOLIDE-2025-11-25  
**Modül Sayısı:** 2 Aktif Modül

---

## 📋 İÇİNDEKİLER

1. [Arsa Modülü](#arsa-modulu)
2. [Yazlık Kiralama Modülü](#yazlik-kiralama-modulu)
3. [Modül Entegrasyonu](#modul-entegrasyonu)
4. [API Endpoint'leri](#api-endpointleri)
5. [Database Şeması](#database-semasi)

---

## 🏞️ ARSA MODÜLÜ

### Genel Bakış

Arsa modülü, emlak sisteminde arsa (land) ilanlarının yönetimi, değerleme, hesaplama ve TKGM entegrasyonu için kapsamlı bir sistemdir.

**Version:** 2.0.0  
**Context7 Standard:** C7-ARSA-MODULE-2025-11-20  
**Durum:** ✅ Aktif ve Kullanımda

### Özellikler

- ✅ **13 Arsa özellik alanı** (İmar Durumu, Ada/Parsel, KAKS/TAKS, vb.)
- ✅ **TKGM Parsel Sorgulama** entegrasyonu
- ✅ **Arsa değerleme algoritması**
- ✅ **KAKS/TAKS hesaplama sistemi**
- ✅ **Karşılaştırmalı analiz**
- ✅ **ROI hesaplamaları**
- ✅ **Vergi hesaplamaları**

### Arsa Özellik Alanları

```php
// Arsa Modülü Özellikleri
'imar_durumu' => 'string',           // İmarlı, İmarsız, Tarla
'ada_no' => 'integer',               // TKGM Ada Numarası
'parsel_no' => 'integer',            // TKGM Parsel Numarası
'kaks' => 'decimal',                 // Kat Alanları Katsayısı
'taks' => 'decimal',                 // Taban Alanı Katsayısı
'gabari' => 'integer',               // Maksimum Yükseklik (m)
'metrekare' => 'integer',            // Arsa Metrekaresi
'imar_hakki' => 'integer',           // İnşaat Hakkı (m²)
'elektrik_durumu' => 'boolean',      // Elektrik Var/Yok
'su_durumu' => 'boolean',            // Su Var/Yok
'dogalgaz_durumu' => 'boolean',      // Doğalgaz Var/Yok
'yol_durumu' => 'string',            // Asfalt, Stabilize, Toprak
'emsaller' => 'json'                 // Emsal Değerler
```

### TKGM Entegrasyonu

```php
// TKGM API Entegrasyonu
class TKGMService {
    public function parselSorgula($il, $ilce, $ada, $parsel) {
        // TKGM Web Servisi çağrısı
        // Parsel bilgilerini getirir
        // İmar durumu, malik bilgisi, vb.
    }

    public function imarDurumuSorgula($parselId) {
        // İmar planı durumu
        // Zonning bilgisi
        // İnşaat kısıtları
    }
}
```

### Arsa Değerleme Algoritması

```php
public function arsaDegerleme($arsaData) {
    $baseValue = $arsaData['metrekare'] * $this->getLocationMultiplier();

    // KAKS/TAKS çarpanları
    $imarValue = $baseValue * ($arsaData['kaks'] * 0.3 + $arsaData['taks'] * 0.2);

    // Konum çarpanları
    $locationValue = $imarValue * $this->getProximityScore($arsaData);

    // Altyapı çarpanları
    $infrastructureValue = $locationValue * $this->getInfrastructureScore($arsaData);

    return [
        'base_value' => $baseValue,
        'imar_adjusted' => $imarValue,
        'location_adjusted' => $locationValue,
        'final_value' => $infrastructureValue
    ];
}
```

---

## 🏖️ YAZLIK KİRALAMA MODÜLÜ

### Genel Bakış

Yazlık kiralama modülü, Airbnb/Booking.com/VRBO standartlarında modern bir vacation rental (tatil kiralama) sistemidir.

**Version:** 2.0.0  
**Context7 Standard:** C7-VACATION-RENTAL-2025-11-20  
**Durum:** ✅ Aktif ve Kullanımda

### Özellikler

- ✅ **14 Yazlık özellik alanı** (Günlük/Haftalık/Aylık fiyat, Min/Max konaklama, vb.)
- ✅ **Sezonluk fiyatlandırma sistemi**
- ✅ **Rezervasyon yönetimi**
- ✅ **Doluluk durumu takibi**
- ✅ **Misafir ve konaklama yönetimi**
- ✅ **Revenue analytics**
- ✅ **Multi-currency support** (TRY, USD, EUR)

### Yazlık Özellik Alanları

```php
// Yazlık Kiralama Özellikleri
'gunluk_fiyat' => 'decimal',          // Günlük kiralama fiyatı
'haftalik_fiyat' => 'decimal',        // Haftalık kiralama fiyatı
'aylik_fiyat' => 'decimal',           // Aylık kiralama fiyatı
'min_konaklama' => 'integer',         // Minimum konaklama süresi (gün)
'max_konaklama' => 'integer',         // Maksimum konaklama süresi (gün)
'yatak_sayisi' => 'integer',          // Toplam yatak sayısı
'misafir_kapasitesi' => 'integer',    // Maksimum misafir sayısı
'deniz_mesafesi' => 'integer',        // Denize uzaklık (m)
'havuz' => 'boolean',                 // Havuz var/yok
'klima' => 'boolean',                 // Klima var/yok
'wifi' => 'boolean',                  // WiFi var/yok
'otopark' => 'boolean',               // Otopark var/yok
'pet_friendly' => 'boolean',          // Evcil hayvan kabul
'cleaning_fee' => 'decimal'           // Temizlik ücreti
```

### Sezonluk Fiyatlandırma

```php
class YazlikFiyatlandirma extends Model {
    protected $fillable = [
        'ilan_id',
        'sezon_tipi',      // 'yaz', 'ara_sezon', 'kis'
        'baslangic_tarihi',
        'bitis_tarihi',
        'gunluk_fiyat',
        'haftalik_fiyat',
        'aylik_fiyat',
        'minimum_konaklama',
        'extra_guest_fee',  // Ekstra misafir ücreti
        'currency'
    ];

    public function sezonFiyatiHesapla($checkin, $checkout, $guest_count) {
        // Sezona göre fiyat hesaplama
        // Misafir sayısı çarpanı
        // Minimum konaklama kontrolü
    }
}
```

### Rezervasyon Sistemi

```php
class YazlikRezervasyonu extends Model {
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CHECKEDIN = 'checked_in';
    const STATUS_CHECKEDOUT = 'checked_out';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'ilan_id',
        'misafir_id',
        'checkin_date',
        'checkout_date',
        'guest_count',
        'total_amount',
        'currency',
        'status',
        'special_requests',
        'cleaning_fee',
        'extra_fees'
    ];

    public function availabilityCheck($checkin, $checkout) {
        // Müsaitlik kontrolü
        // Overlapping rezervasyon kontrolü
    }
}
```

---

## 🔗 MODÜL ENTEGRASYONU

### API Endpoint'leri

```php
// Arsa Modülü API
Route::group(['prefix' => 'arsa', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/properties', [ArsaController::class, 'index']);
    Route::post('/properties', [ArsaController::class, 'store']);
    Route::get('/valuation/{id}', [ArsaController::class, 'valuation']);
    Route::post('/tkgm/parsel-sorgula', [TKGMController::class, 'parselSorgula']);
});

// Yazlık Kiralama API
Route::group(['prefix' => 'vacation-rental', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/properties', [VacationRentalController::class, 'index']);
    Route::post('/properties', [VacationRentalController::class, 'store']);
    Route::get('/availability/{id}', [VacationRentalController::class, 'checkAvailability']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/pricing/{id}', [VacationRentalController::class, 'calculatePricing']);
});
```

### Service Layer Entegrasyonu

```php
// Modül Servis Entegrasyonu
class ModuleIntegrationService {
    public function getIlanWithModuleData($ilanId) {
        $ilan = Ilan::find($ilanId);

        if ($ilan->ana_kategori_id === 2) { // Arsa
            $ilan->load('arsaOzellikleri');
            $ilan->valuation = app(ArsaValuationService::class)->calculate($ilan);
        }

        if ($ilan->alt_kategori_id === 8) { // Yazlık
            $ilan->load(['yazlikOzellikleri', 'rezervasyonlar']);
            $ilan->availability = app(VacationRentalService::class)->getAvailability($ilan);
        }

        return $ilan;
    }
}
```

---

## 💾 DATABASE ŞEMASI

### Arsa Tabloları

```sql
-- Arsa özellikleri tablosu
CREATE TABLE arsa_ozellikleri (
    id BIGINT UNSIGNED PRIMARY KEY,
    ilan_id BIGINT UNSIGNED NOT NULL,
    imar_durumu VARCHAR(50),
    ada_no INT,
    parsel_no INT,
    kaks DECIMAL(3,2),
    taks DECIMAL(3,2),
    gabari INT,
    metrekare INT,
    imar_hakki INT,
    elektrik_durumu TINYINT(1) DEFAULT 0,
    su_durumu TINYINT(1) DEFAULT 0,
    dogalgaz_durumu TINYINT(1) DEFAULT 0,
    yol_durumu VARCHAR(50),
    emsaller JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (ilan_id) REFERENCES ilanlar(id) ON DELETE CASCADE,
    INDEX idx_ada_parsel (ada_no, parsel_no),
    INDEX idx_imar_durumu (imar_durumu)
);
```

### Yazlık Tabloları

```sql
-- Yazlık özellikleri tablosu
CREATE TABLE yazlik_ozellikleri (
    id BIGINT UNSIGNED PRIMARY KEY,
    ilan_id BIGINT UNSIGNED NOT NULL,
    gunluk_fiyat DECIMAL(10,2),
    haftalik_fiyat DECIMAL(10,2),
    aylik_fiyat DECIMAL(10,2),
    min_konaklama INT DEFAULT 1,
    max_konaklama INT DEFAULT 30,
    yatak_sayisi INT,
    misafir_kapasitesi INT,
    deniz_mesafesi INT,
    havuz TINYINT(1) DEFAULT 0,
    klima TINYINT(1) DEFAULT 0,
    wifi TINYINT(1) DEFAULT 0,
    otopark TINYINT(1) DEFAULT 0,
    pet_friendly TINYINT(1) DEFAULT 0,
    cleaning_fee DECIMAL(8,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (ilan_id) REFERENCES ilanlar(id) ON DELETE CASCADE,
    INDEX idx_fiyat_range (gunluk_fiyat, haftalik_fiyat),
    INDEX idx_konaklama (min_konaklama, max_konaklama)
);

-- Yazlık fiyatlandırma tablosu
CREATE TABLE yazlik_fiyatlandirma (
    id BIGINT UNSIGNED PRIMARY KEY,
    ilan_id BIGINT UNSIGNED NOT NULL,
    sezon_tipi ENUM('yaz','ara_sezon','kis') NOT NULL,
    baslangic_tarihi DATE NOT NULL,
    bitis_tarihi DATE NOT NULL,
    gunluk_fiyat DECIMAL(10,2),
    haftalik_fiyat DECIMAL(10,2),
    aylik_fiyat DECIMAL(10,2),
    minimum_konaklama INT DEFAULT 1,
    extra_guest_fee DECIMAL(6,2),
    currency ENUM('TRY','USD','EUR') DEFAULT 'TRY',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (ilan_id) REFERENCES ilanlar(id) ON DELETE CASCADE,
    INDEX idx_sezon_tarih (sezon_tipi, baslangic_tarihi, bitis_tarihi),
    INDEX idx_ilan_sezon (ilan_id, sezon_tipi)
);

-- Yazlık rezervasyonları tablosu
CREATE TABLE yazlik_rezervasyonlari (
    id BIGINT UNSIGNED PRIMARY KEY,
    ilan_id BIGINT UNSIGNED NOT NULL,
    misafir_id BIGINT UNSIGNED,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    guest_count INT NOT NULL,
    total_amount DECIMAL(10,2),
    currency ENUM('TRY','USD','EUR') DEFAULT 'TRY',
    status ENUM('pending','confirmed','checked_in','checked_out','cancelled') DEFAULT 'pending',
    special_requests TEXT,
    cleaning_fee DECIMAL(8,2),
    extra_fees DECIMAL(8,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (ilan_id) REFERENCES ilanlar(id) ON DELETE CASCADE,
    FOREIGN KEY (misafir_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_tarih_range (checkin_date, checkout_date),
    INDEX idx_ilan_status (ilan_id, status),
    INDEX idx_misafir (misafir_id)
);
```

---

## 📚 KAYNAK DOSYALAR (BİRLEŞTİRİLDİ)

Bu dokümanda şu dosyalar birleştirilmiştir:

1. `docs/modules/arsa-modulu.md`
2. `docs/modules/yazlik-kiralama.md`

**Context7 Compliance:** ✅ C7-MODULES-KONSOLIDE-2025-11-25  
**Tarih:** 25 Kasım 2025
