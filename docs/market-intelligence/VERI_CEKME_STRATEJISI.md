# 📊 Market Intelligence - Veri Çekme Stratejisi

**Tarih:** 2025-11-27  
**Versiyon:** 1.0  
**Durum:** ✅ Strateji Belirlendi

---

## 🎯 SORU: Tüm Türkiye mi, Yoksa Seçili Bölgeler mi?

**CEVAP:** **Kullanıcı Panelden İl-İlçe Seçerek Sadece Seçilen Bölgeleri Çeker**

### Neden Bu Strateji?

1. **Kaynak Verimliliği:** Tüm Türkiye'yi çekmek gereksiz kaynak kullanımı
2. **Odaklanmış Analiz:** Sadece iş yaptığınız bölgeleri takip edersiniz
3. **Maliyet Kontrolü:** n8n bot'ları sadece seçilen bölgeleri tarar
4. **Hızlı Güncelleme:** Daha az veri = Daha hızlı güncelleme
5. **Kullanıcı Kontrolü:** Her kullanıcı kendi bölgelerini seçer

---

## 🏗️ SİSTEM MİMARİSİ

### 1. Panel Yapılandırması

**Lokasyon:** `/admin/market-intelligence/settings`

**Kullanıcı Arayüzü:**

```
┌─────────────────────────────────────────────────────────┐
│  📊 Pazar İstihbaratı - Bölge Seçimi                   │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ✅ Aktif Bölgeler (Çekilecek Bölgeler)                │
│                                                          │
│  📍 Antalya                                             │
│     ├─ ✅ Muratpaşa                                     │
│     │   ├─ ✅ Konyaaltı                                │
│     │   ├─ ✅ Lara                                     │
│     │   └─ ✅ Kepez                                    │
│     ├─ ✅ Alanya                                        │
│     │   ├─ ✅ Mahmutlar                                │
│     │   └─ ✅ Oba                                      │
│     └─ ❌ Kaş (Pasif)                                  │
│                                                          │
│  📍 İstanbul                                            │
│     ├─ ✅ Kadıköy                                      │
│     ├─ ✅ Beşiktaş                                     │
│     └─ ❌ Üsküdar (Pasif)                              │
│                                                          │
│  ➕ Yeni Bölge Ekle                                     │
│                                                          │
│  [💾 Kaydet] [🔄 Tümünü Aktif Et] [❌ Tümünü Pasif Et] │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

### 2. Veritabanı Yapısı

**Yeni Tablo:** `market_intelligence_settings`

```sql
CREATE TABLE market_intelligence_settings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NULL COMMENT 'Kullanıcı bazlı ayar (NULL = Global)',
    il_id BIGINT NOT NULL,
    ilce_id BIGINT NULL COMMENT 'NULL = Tüm ilçeler',
    mahalle_id BIGINT NULL COMMENT 'NULL = Tüm mahalleler',
    is_active TINYINT(1) DEFAULT 1 COMMENT '1: Aktif, 0: Pasif',
    priority INT DEFAULT 0 COMMENT 'Öncelik (yüksek = önce çek)',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_il_id (il_id),
    INDEX idx_is_active (is_active),
    UNIQUE KEY unique_location (user_id, il_id, ilce_id, mahalle_id)
);
```

**Örnek Veriler:**

```sql
-- Global ayar (Tüm kullanıcılar için)
INSERT INTO market_intelligence_settings (user_id, il_id, ilce_id, mahalle_id, is_active) 
VALUES (NULL, 7, 123, NULL, 1); -- Antalya, Muratpaşa (tüm mahalleler)

-- Kullanıcı bazlı ayar
INSERT INTO market_intelligence_settings (user_id, il_id, ilce_id, mahalle_id, is_active) 
VALUES (1, 34, 456, 789, 1); -- İstanbul, Kadıköy, Moda (sadece bu kullanıcı için)
```

### 3. n8n Bot Yapılandırması

**n8n Workflow Yapısı:**

```
┌─────────────────────────────────────────────────────────┐
│  n8n Market Intelligence Bot                          │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  1. Laravel API'den Aktif Bölgeleri Çek               │
│     GET /api/admin/market-intelligence/active-regions │
│                                                          │
│     Response:                                          │
│     [                                                   │
│       {                                                 │
│         "il_id": 7,                                     │
│         "il_adi": "Antalya",                           │
│         "ilce_id": 123,                                 │
│         "ilce_adi": "Muratpaşa",                       │
│         "mahalle_id": null,                             │
│         "priority": 1                                   │
│       },                                                │
│       {                                                 │
│         "il_id": 34,                                    │
│         "il_adi": "İstanbul",                           │
│         "ilce_id": 456,                                 │
│         "ilce_adi": "Kadıköy",                         │
│         "mahalle_id": 789,                              │
│         "priority": 2                                  │
│       }                                                 │
│     ]                                                   │
│                                                          │
│  2. Her Bölge İçin Döngü                               │
│     FOR EACH region IN active_regions:                 │
│                                                          │
│     a. Sahibinden.com'u tara                           │
│        - İl: region.il_adi                             │
│        - İlçe: region.ilce_adi (varsa)                 │
│        - Mahalle: region.mahalle_adi (varsa)          │
│                                                          │
│     b. Hepsiemlak.com'u tara                           │
│        - Aynı filtreler                                │
│                                                          │
│     c. Emlakjet.com'u tara                             │
│        - Aynı filtreler                                │
│                                                          │
│  3. Bulunan İlanları Laravel'e Gönder                 │
│     POST /api/admin/market-intelligence/sync           │
│     {                                                   │
│       "source": "sahibinden",                          │
│       "region": {                                       │
│         "il_id": 7,                                     │
│         "ilce_id": 123                                  │
│       },                                                │
│       "listings": [...]                                │
│     }                                                   │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 🔄 ÇALIŞMA AKIŞI

### Senaryo 1: Kullanıcı Yeni Bölge Ekler

```
1. Kullanıcı Panelden Bölge Seçer
   └─ /admin/market-intelligence/settings
      └─ "Antalya > Muratpaşa > Konyaaltı" seçer
      └─ [💾 Kaydet] tıklar

2. Laravel Backend
   └─ MarketIntelligenceSettings::create([
        'user_id' => auth()->id(),
        'il_id' => 7,
        'ilce_id' => 123,
        'mahalle_id' => 456,
        'is_active' => 1
      ])

3. n8n Bot (Her saat çalışır)
   └─ GET /api/admin/market-intelligence/active-regions
   └─ Yeni bölgeyi görür
   └─ Sahibinden/Hepsiemlak/Emlakjet'i tarar
   └─ Bulunan ilanları Laravel'e gönderir

4. Laravel Backend
   └─ MarketListing::updateOrCreate(...)
   └─ Veritabanına kaydeder
```

### Senaryo 2: Kullanıcı Bölgeyi Pasif Yapar

```
1. Kullanıcı Panelden Bölgeyi Pasif Yapar
   └─ "Antalya > Muratpaşa > Konyaaltı" ❌ (Pasif)

2. Laravel Backend
   └─ MarketIntelligenceSettings::where(...)
      ->update(['is_active' => 0])

3. n8n Bot (Sonraki çalışmada)
   └─ GET /api/admin/market-intelligence/active-regions
   └─ Bu bölgeyi görmez (is_active = 0)
   └─ Bu bölgeyi taramaz
```

### Senaryo 3: Global vs Kullanıcı Bazlı Ayarlar

**Global Ayar (user_id = NULL):**
- Tüm kullanıcılar için geçerli
- Admin tarafından yönetilir
- Örnek: "Antalya > Muratpaşa" (tüm kullanıcılar için)

**Kullanıcı Bazlı Ayar (user_id = 1):**
- Sadece o kullanıcı için geçerli
- Kullanıcı kendi bölgelerini seçer
- Örnek: "İstanbul > Kadıköy > Moda" (sadece bu kullanıcı için)

**Birleşik Sonuç:**
```php
// n8n bot'unun çekeceği bölgeler
$activeRegions = MarketIntelligenceSettings::where('is_active', 1)
    ->where(function($query) {
        $query->whereNull('user_id') // Global
            ->orWhere('user_id', auth()->id()); // Kullanıcı bazlı
    })
    ->get();
```

---

## 📋 API ENDPOINT'LERİ

### 1. Aktif Bölgeleri Getir

**Endpoint:** `GET /api/admin/market-intelligence/active-regions`

**Açıklama:** n8n bot'unun hangi bölgeleri tarayacağını döndürür

**Response:**
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
      "mahalle_id": null,
      "mahalle_adi": null,
      "is_active": 1,
      "priority": 1
    },
    {
      "id": 2,
      "il_id": 34,
      "il_adi": "İstanbul",
      "ilce_id": 456,
      "ilce_adi": "Kadıköy",
      "mahalle_id": 789,
      "mahalle_adi": "Moda",
      "is_active": 1,
      "priority": 2
    }
  ]
}
```

### 2. Bölge Ayarlarını Kaydet

**Endpoint:** `POST /api/admin/market-intelligence/settings`

**Request:**
```json
{
  "regions": [
    {
      "il_id": 7,
      "ilce_id": 123,
      "mahalle_id": 456,
      "is_active": 1,
      "priority": 1
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Bölge ayarları kaydedildi",
  "data": {
    "saved_count": 1
  }
}
```

### 3. Veri Senkronizasyonu (n8n → Laravel)

**Endpoint:** `POST /api/admin/market-intelligence/sync`

**Request:**
```json
{
  "source": "sahibinden",
  "region": {
    "il_id": 7,
    "ilce_id": 123,
    "mahalle_id": 456
  },
  "listings": [
    {
      "external_id": "123456",
      "url": "https://sahibinden.com/ilan/123456",
      "title": "Deniz Manzaralı 3+1 Daire",
      "price": 1500000,
      "currency": "TRY",
      "location_il": "Antalya",
      "location_ilce": "Muratpaşa",
      "location_mahalle": "Konyaaltı",
      "m2_brut": 120,
      "m2_net": 100,
      "room_count": "3+1",
      "listing_date": "2025-11-20",
      "snapshot_data": {
        // Ham veri
      }
    }
  ]
}
```

---

## 🎯 ÖNCELİK SİSTEMİ

**Priority Alanı:**
- **1-10:** Yüksek Öncelik (Her saat çekilir)
- **11-50:** Orta Öncelik (Her 2 saatte bir çekilir)
- **51-100:** Düşük Öncelik (Her 4 saatte bir çekilir)

**Örnek:**
```php
// Yüksek öncelikli bölgeler (her saat)
$highPriority = MarketIntelligenceSettings::where('is_active', 1)
    ->whereBetween('priority', [1, 10])
    ->get();

// Orta öncelikli bölgeler (her 2 saatte bir)
$mediumPriority = MarketIntelligenceSettings::where('is_active', 1)
    ->whereBetween('priority', [11, 50])
    ->get();
```

---

## 📊 İSTATİSTİKLER

**Dashboard Göstergeleri:**

```
┌─────────────────────────────────────────────────────────┐
│  📊 Pazar İstihbaratı - Genel Bakış                   │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  📍 Aktif Bölge Sayısı: 15                             │
│     ├─ Antalya: 5 bölge                                │
│     ├─ İstanbul: 7 bölge                               │
│     └─ İzmir: 3 bölge                                  │
│                                                          │
│  📊 Toplam İlan: 1.234                                 │
│     ├─ Bugün Yeni: 45                                 │
│     ├─ Son 7 Gün: 234                                 │
│     └─ Son 30 Gün: 567                                │
│                                                          │
│  ⏱️ Son Güncelleme: 2 saat önce                       │
│                                                          │
│  🔄 Sonraki Güncelleme: 1 saat sonra                   │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## ✅ AVANTAJLAR

1. **Kaynak Verimliliği:** Sadece seçilen bölgeler taranır
2. **Kullanıcı Kontrolü:** Her kullanıcı kendi bölgelerini yönetir
3. **Ölçeklenebilirlik:** Yeni bölgeler kolayca eklenebilir
4. **Maliyet Kontrolü:** n8n bot'ları sadece gerekli bölgeleri tarar
5. **Hızlı Güncelleme:** Daha az veri = Daha hızlı işlem

---

## 🚀 SONRAKI ADIMLAR

1. **Settings Tablosu Oluştur:**
   - Migration dosyası
   - Model oluşturma

2. **Controller Geliştirme:**
   - `MarketIntelligenceSettingsController`
   - `active-regions` endpoint
   - `settings` endpoint

3. **Panel Arayüzü:**
   - Bölge seçim sayfası
   - Aktif/pasif toggle
   - Öncelik ayarlama

4. **n8n Bot Güncelleme:**
   - Aktif bölgeleri çekme
   - Bölge bazlı tarama
   - Priority sistemi

---

**Son Güncelleme:** 2025-11-27  
**Durum:** ✅ Strateji Belirlendi, Implementation Bekliyor






