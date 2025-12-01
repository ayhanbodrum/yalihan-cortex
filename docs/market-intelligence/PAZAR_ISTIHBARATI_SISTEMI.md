# 📊 Pazar İstihbaratı (Market Intelligence) Sistemi - Kapsamlı Dokümantasyon

**Tarih:** 2025-11-27  
**Versiyon:** 1.0.0  
**Durum:** ✅ Altyapı Hazır

---

## 📋 İÇİNDEKİLER

1. [Sistemin Amacı](#sistemin-amacı)
2. [Hangi Verileri Alır?](#hangi-verileri-alır)
3. [Nasıl Çalışır?](#nasıl-çalışır)
4. [Veri Yapısı](#veri-yapısı)
5. [Nasıl Gösterir?](#nasıl-gösterir)
6. [Kullanım Senaryoları](#kullanım-senaryoları)
7. [Dashboard Görselleştirmeleri](#dashboard-görselleştirmeleri)
8. [Teknik Detaylar](#teknik-detaylar)

---

## 🎯 SİSTEMİN AMACI

Pazar İstihbaratı sistemi, **dış emlak portallarından (Sahibinden, Hepsiemlak, Emlakjet) otomatik olarak ilan verilerini çekip saklayarak**, kendi ilanlarınızı piyasa ile karşılaştırmanızı sağlar.

### 🔍 Ne İş Yapar?

1. **Rakip Analizi:** Rakip ilanların fiyatlarını, özelliklerini ve lokasyonlarını takip eder
2. **Fiyat Karşılaştırması:** Kendi ilanlarınızın piyasa ortalamasına göre durumunu gösterir
3. **Piyasa Trend Analizi:** Fiyat değişimlerini zaman içinde takip eder
4. **Lokasyon Bazlı İstatistikler:** Bölge bazında ortalama fiyatları hesaplar
5. **Fiyat Önerileri:** AI destekli fiyat optimizasyon önerileri sunar

---

## 📥 HANGİ VERİLERİ ALIR?

### 1. Temel İlan Bilgileri

| Veri | Açıklama | Örnek |
|------|----------|-------|
| **Başlık** | İlan başlığı | "Deniz Manzaralı 3+1 Daire" |
| **Fiyat** | İlan fiyatı | 1.800.000 TL |
| **Para Birimi** | Fiyat birimi | TRY, USD, EUR |
| **URL** | İlan linki | https://sahibinden.com/ilan/123456 |

### 2. Lokasyon Bilgileri

| Veri | Açıklama | Örnek |
|------|----------|-------|
| **İl** | İl adı | Antalya |
| **İlçe** | İlçe adı | Muratpaşa |
| **Mahalle** | Mahalle adı | Konyaaltı |

### 3. Özellik Bilgileri

| Veri | Açıklama | Örnek |
|------|----------|-------|
| **Brüt Metrekare** | Toplam alan | 120 m² |
| **Net Metrekare** | Kullanılabilir alan | 100 m² |
| **Oda Sayısı** | Oda + salon sayısı | 3+1, 4+1, 5+2 |

### 4. Tarih Bilgileri

| Veri | Açıklama | Örnek |
|------|----------|-------|
| **İlan Tarihi** | İlanın yayınlandığı tarih | 2025-11-20 |
| **Son Görülme** | En son kontrol edildiği tarih | 2025-11-27 14:30:00 |

### 5. Durum Bilgileri

| Veri | Açıklama | Değerler |
|------|----------|----------|
| **Durum** | İlan aktif mi? | 1: Yayında, 0: Kalktı/Satıldı |
| **Kaynak** | Hangi siteden geldiği | sahibinden, hepsiemlak, emlakjet |
| **External ID** | O sitedeki ilan ID'si | "123456" |

### 6. Fiyat Geçmişi (JSON)

Zaman içinde fiyat değişimlerini tutar:

```json
[
  {
    "date": "2025-11-20",
    "price": 1500000
  },
  {
    "date": "2025-11-25",
    "price": 1450000
  },
  {
    "date": "2025-11-27",
    "price": 1450000
  }
]
```

### 7. Ham Veri (JSON)

İlanın tüm detaylarını saklar:

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
    "room_count": "3+1",
    "bina_yasi": 5,
    "kat": 3,
    "toplam_kat": 8
  },
  "raw_data": {
    // n8n'den gelen tüm ham veri
  }
}
```

---

## ⚙️ NASIL ÇALIŞIR?

### 1. VERİ TOPLAMA AŞAMASI (n8n Botları)

```
┌─────────────────────────────────────────┐
│  n8n Workflow (Otomatik Bot)           │
│  ├─ Sahibinden.com tarama               │
│  │  └─ Her saat başı çalışır            │
│  ├─ Hepsiemlak.com tarama               │
│  │  └─ Her saat başı çalışır            │
│  └─ Emlakjet.com tarama                 │
│     └─ Her saat başı çalışır            │
└─────────────────────────────────────────┘
                    ↓
         [Web Scraping / API Çekimi]
                    ↓
┌─────────────────────────────────────────┐
│  Veri İşleme                            │
│  ├─ Yeni ilanları tespit et             │
│  ├─ Mevcut ilanların fiyat değişimini   │
│  │  kontrol et                           │
│  ├─ Kalkan/satılan ilanları işaretle    │
│  └─ JSON formatına çevir                │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│  Webhook → Laravel API                  │
│  POST /api/admin/market-intelligence/  │
│       sync                              │
│  {                                      │
│    "source": "sahibinden",             │
│    "external_id": "123456",            │
│    "title": "Satılık Daire",           │
│    "price": 1500000,                    │
│    ...                                  │
│  }                                      │
└─────────────────────────────────────────┘
```

**n8n Bot'unun Yaptığı İşler:**
- ✅ Her saat başı dış portalları tarar
- ✅ Yeni ilanları tespit eder
- ✅ Mevcut ilanların fiyat değişikliklerini kontrol eder
- ✅ Kalkan/satılan ilanları işaretler
- ✅ Verileri JSON formatında Laravel'e gönderir

### 2. VERİ SAKLAMA AŞAMASI (Laravel Backend)

```
┌─────────────────────────────────────────┐
│  MarketIntelligenceController           │
│  ├─ Webhook'tan veri alır                │
│  ├─ Veriyi doğrular (Validation)        │
│  ├─ Mevcut kaydı kontrol eder           │
│  │  (source + external_id)              │
│  └─ MarketListing modeline kaydeder     │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│  MarketListing Model                    │
│  ├─ updateOrCreate() kullanır          │
│  ├─ Fiyat değiştiyse price_history'ye  │
│  │  ekler                                │
│  └─ last_seen_at günceller              │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│  market_listings Tablosu                 │
│  (yalihan_market veritabanında)          │
│  ├─ source: 'sahibinden'                │
│  ├─ external_id: '123456'                │
│  ├─ price: 1500000                       │
│  ├─ price_history: [{date, price}]     │
│  ├─ snapshot_data: {ham veri}           │
│  └─ last_seen_at: 2025-11-27 14:30:00   │
└─────────────────────────────────────────┘
```

**Veritabanında Saklanan Veriler:**
- ✅ İlan başlığı, fiyat, lokasyon
- ✅ Metrekare, oda sayısı
- ✅ Fiyat geçmişi (zaman içinde değişimler)
- ✅ Ham veri (snapshot_data - tüm detaylar)
- ✅ Son görülme tarihi (last_seen_at)
- ✅ Durum (aktif/pasif)

### 3. VERİ ANALİZİ AŞAMASI (AI Destekli)

```
┌─────────────────────────────────────────┐
│  Kendi İlanınız (Ilan Model)           │
│  ├─ ID: 123                             │
│  ├─ Fiyat: 1.800.000 TL                 │
│  ├─ Lokasyon: Antalya, Muratpaşa        │
│  ├─ Özellikler: 3+1, 120 m²            │
│  └─ Kategori: Konut                     │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│  Market Intelligence Service            │
│  ├─ 1. Benzer ilanları bulur            │
│  │  └─ Aynı lokasyon (il, ilçe)         │
│  │  └─ Benzer özellikler (oda, m²)      │
│  │  └─ Aynı kategori                     │
│  │                                      │
│  ├─ 2. Ortalama fiyatı hesaplar          │
│  │  └─ Benzer ilanların ortalama fiyatı │
│  │                                      │
│  ├─ 3. Fiyat farkını analiz eder        │
│  │  └─ Kendi fiyatınız vs Piyasa ort.   │
│  │  └─ Yüzde farkı hesaplar             │
│  │                                      │
│  └─ 4. AI önerisi üretir                │
│     └─ YalihanCortex kullanır           │
│     └─ Fiyat optimizasyon önerisi       │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│  Analiz Sonucu                          │
│  ├─ Piyasa Ortalaması: 1.650.000 TL     │
│  ├─ Kendi Fiyatınız: 1.800.000 TL        │
│  ├─ Fark: %9 pahalı                     │
│  ├─ Benzer İlan Sayısı: 12              │
│  └─ AI Önerisi:                         │
│     ├─ Aksiyon: İndirim yap             │
│     ├─ Yüzde: %5                        │
│     ├─ Önerilen Fiyat: 1.710.000 TL     │
│     └─ Güven Skoru: %87                 │
└─────────────────────────────────────────┘
```

---

## 🗄️ VERİ YAPISI

### Veritabanı Tablosu: `market_listings`

**Veritabanı:** `yalihan_market` (ayrı veritabanı)

| Alan | Tip | Açıklama | Örnek |
|------|-----|----------|-------|
| `id` | BigInt | Primary Key | 1 |
| `source` | String(50) | Kaynak site | "sahibinden" |
| `external_id` | String(255) | O sitedeki ID | "123456" |
| `url` | String(500) | İlan linki | "https://..." |
| `title` | String(500) | İlan başlığı | "Deniz Manzaralı..." |
| `price` | Decimal(15,2) | Fiyat | 1500000.00 |
| `currency` | String(10) | Para birimi | "TRY" |
| `location_il` | String(100) | İl adı | "Antalya" |
| `location_ilce` | String(100) | İlçe adı | "Muratpaşa" |
| `location_mahalle` | String(100) | Mahalle adı | "Konyaaltı" |
| `m2_brut` | Integer | Brüt metrekare | 120 |
| `m2_net` | Integer | Net metrekare | 100 |
| `room_count` | String(20) | Oda sayısı | "3+1" |
| `listing_date` | Date | İlan tarihi | "2025-11-20" |
| `last_seen_at` | Timestamp | Son görülme | "2025-11-27 14:30:00" |
| `status` | TinyInt | Durum | 1 (aktif) |
| `snapshot_data` | JSON | Ham veri | {...} |
| `price_history` | JSON | Fiyat geçmişi | [{...}] |
| `created_at` | Timestamp | Oluşturulma | "2025-11-20 10:00:00" |
| `updated_at` | Timestamp | Güncellenme | "2025-11-27 14:30:00" |

**Index'ler:**
- `source` (tek)
- `external_id` (tek)
- `['source', 'external_id']` (composite, unique)
- `status`
- `last_seen_at`
- `['location_il', 'location_ilce']` (composite)

---

## 🎨 NASIL GÖSTERİR?

### 1. Dashboard Ana Sayfası

**Lokasyon:** `/admin/market-intelligence/dashboard`

**Gösterilen Bilgiler:**

```
┌─────────────────────────────────────────────────────────┐
│  📊 Pazar İstihbaratı Dashboard                         │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  📈 Genel İstatistikler                                 │
│  ├─ Toplam İlan: 1.234                                  │
│  ├─ Aktif İlan: 1.156                                   │
│  ├─ Bugün Yeni: 45                                      │
│  └─ Fiyat Değişen: 23                                   │
│                                                          │
│  📊 Kaynak Dağılımı                                      │
│  ├─ Sahibinden: 567 ilan (46%)                          │
│  ├─ Hepsiemlak: 412 ilan (33%)                          │
│  └─ Emlakjet: 255 ilan (21%)                            │
│                                                          │
│  📍 Lokasyon İstatistikleri                              │
│  ├─ Antalya: 456 ilan                                   │
│  ├─ İstanbul: 312 ilan                                  │
│  └─ İzmir: 234 ilan                                      │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

### 2. Fiyat Karşılaştırma Sayfası

**Lokasyon:** `/admin/market-intelligence/compare/{ilan_id}`

**Gösterilen Bilgiler:**

```
┌─────────────────────────────────────────────────────────┐
│  💰 İlan Fiyat Karşılaştırması                         │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  📋 İlan Bilgileri                                      │
│  ├─ Başlık: Deniz Manzaralı 3+1 Daire                  │
│  ├─ Lokasyon: Antalya, Muratpaşa, Konyaaltı            │
│  ├─ Özellikler: 3+1, 120 m²                            │
│  └─ Kendi Fiyatınız: 1.800.000 TL                       │
│                                                          │
│  📊 Piyasa Karşılaştırması                              │
│  ├─ Piyasa Ortalaması: 1.650.000 TL                     │
│  ├─ En Düşük: 1.400.000 TL                              │
│  ├─ En Yüksek: 2.100.000 TL                             │
│  └─ Fark: %9 pahalı (150.000 TL)                       │
│                                                          │
│  📈 Fiyat Grafiği                                       │
│  Kendi İlanınız:  ████████████████████ 1.800.000 TL    │
│  Piyasa Ort.:     ████████████████ 1.650.000 TL         │
│  En Düşük:        ████████████ 1.400.000 TL            │
│  En Yüksek:       ████████████████████████ 2.100.000 TL │
│                                                          │
│  🤖 AI Önerisi                                          │
│  ├─ Aksiyon: İndirim yap                                │
│  ├─ Yüzde: %5                                           │
│  ├─ Önerilen Fiyat: 1.710.000 TL                        │
│  ├─ Güven Skoru: %87                                    │
│  └─ Gerekçe: Piyasaya göre %9 pahalısınız. %5          │
│     indirimle satılabilir fiyata ulaşabilirsiniz.       │
│                                                          │
│  📋 Benzer İlanlar (12 adet)                            │
│  ├─ 1. 3+1, 115 m² - 1.650.000 TL (Sahibinden)         │
│  ├─ 2. 3+1, 125 m² - 1.700.000 TL (Hepsiemlak)         │
│  └─ ...                                                 │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

### 3. Fiyat Trend Grafiği

**Gösterilen Bilgiler:**

```
Fiyat (TL)
2.000.000 │                    ●
          │              ●
1.500.000 │    ●
          │
          └─────────────────────────
           20 Kas  25 Kas  27 Kas

Açıklama:
● = Fiyat değişimi
Trend: Düşüş (-3.33%)
```

### 4. Lokasyon Bazlı İstatistikler

**Gösterilen Bilgiler:**

```
┌─────────────────────────────────────────────────────────┐
│  📍 Antalya - Muratpaşa İstatistikleri                 │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  📊 Oda Tipine Göre Dağılım                            │
│  ├─ 3+1 Daireler: 45 ilan                               │
│  │  ├─ Ortalama Fiyat: 1.650.000 TL                    │
│  │  ├─ m² Başına: 13.750 TL                             │
│  │  ├─ En Düşük: 1.400.000 TL                           │
│  │  └─ En Yüksek: 2.100.000 TL                          │
│  │                                                       │
│  ├─ 4+1 Daireler: 32 ilan                               │
│  │  ├─ Ortalama Fiyat: 2.100.000 TL                    │
│  │  ├─ m² Başına: 15.000 TL                             │
│  │  ├─ En Düşük: 1.800.000 TL                           │
│  │  └─ En Yüksek: 2.500.000 TL                          │
│  │                                                       │
│  └─ Villa: 12 ilan                                      │
│     ├─ Ortalama Fiyat: 4.500.000 TL                     │
│     ├─ m² Başına: 25.000 TL                             │
│     ├─ En Düşük: 3.500.000 TL                           │
│     └─ En Yüksek: 6.000.000 TL                          │
│                                                          │
│  📈 Trend Analizi                                       │
│  ├─ Son 7 Gün: +2.5% artış                             │
│  ├─ Son 30 Gün: +5.2% artış                             │
│  └─ Tahmin: Önümüzdeki ay %3-5 artış bekleniyor        │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

### 5. Rakip İlan Takibi

**Gösterilen Bilgiler:**

```
┌─────────────────────────────────────────────────────────┐
│  👁️ Takip Edilen Rakip İlanlar                         │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  📋 İlan #1: Deniz Manzaralı 3+1                       │
│  ├─ Kaynak: Sahibinden                                  │
│  ├─ Fiyat: 1.650.000 TL                                 │
│  ├─ Son Değişiklik: 25 Kasım (-50.000 TL)              │
│  ├─ Trend: ⬇️ Düşüş                                     │
│  └─ Durum: ✅ Aktif                                     │
│                                                          │
│  📋 İlan #2: Merkezi Konum 4+1                          │
│  ├─ Kaynak: Hepsiemlak                                  │
│  ├─ Fiyat: 2.100.000 TL                                 │
│  ├─ Son Değişiklik: 20 Kasım (Değişmedi)              │
│  ├─ Trend: ➡️ Sabit                                     │
│  └─ Durum: ✅ Aktif                                     │
│                                                          │
│  📋 İlan #3: Lüks Villa                                 │
│  ├─ Kaynak: Emlakjet                                   │
│  ├─ Fiyat: 4.500.000 TL                                 │
│  ├─ Son Değişiklik: 27 Kasım (Kalktı)                 │
│  ├─ Trend: ❌ Kalktı                                    │
│  └─ Durum: ❌ Pasif                                      │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## 💼 KULLANIM SENARYOLARI

### Senaryo 1: Fiyat Karşılaştırması

**Durum:** Antalya'da 3+1, 120 m² bir daireniz var, fiyatı 1.800.000 TL.

**Sistem Ne Yapar:**
1. `market_listings` tablosundan aynı bölgedeki benzer ilanları bulur
2. Ortalama fiyatı hesaplar (örn: 1.650.000 TL)
3. Farkı hesaplar: %9 pahalı
4. AI önerisi: "%5 indirim yapın, satılabilir fiyat: 1.710.000 TL"

**Kod Örneği:**
```php
// Market Intelligence Service
$ilan = Ilan::find(123);
$marketAnalysis = $marketIntelligenceService->analyzePrice($ilan);

// Sonuç:
[
    'our_price' => 1800000,
    'market_avg' => 1650000,
    'price_difference_percent' => 9.09,
    'recommendation' => [
        'action' => 'reduce',
        'percentage' => 5,
        'suggested_price' => 1710000,
        'message' => 'Piyasaya göre %9 pahalısınız. %5 indirimle satılabilir.'
    ]
]
```

### Senaryo 2: Fiyat Trend Takibi

**Durum:** Bir ilanın fiyatı zaman içinde nasıl değişiyor?

**Sistem Ne Yapar:**
1. `price_history` JSON alanından fiyat geçmişini çeker
2. Grafik oluşturur (zaman → fiyat)
3. Trend analizi yapar (artış/azalış)

**Kod Örneği:**
```php
$listing = MarketListing::where('source', 'sahibinden')
    ->where('external_id', '123456')
    ->first();

// Fiyat geçmişi
$priceHistory = $listing->price_history;
// [
//   {date: '2025-11-20', price: 1500000},
//   {date: '2025-11-25', price: 1450000}, // İndirim yapmış
//   {date: '2025-11-27', price: 1450000}
// ]

// Trend analizi
$trend = $marketIntelligenceService->analyzePriceTrend($priceHistory);
// ['direction' => 'down', 'percentage' => -3.33, 'message' => 'Fiyat düşüyor']
```

### Senaryo 3: Lokasyon Bazlı İstatistikler

**Durum:** Antalya Muratpaşa bölgesinde ortalama fiyat ne kadar?

**Sistem Ne Yapar:**
1. `market_listings` tablosundan Antalya Muratpaşa ilanlarını filtreler
2. Ortalama fiyatı hesaplar
3. Metrekare başına ortalama fiyatı hesaplar
4. İstatistikleri gösterir

**Kod Örneği:**
```php
$stats = MarketListing::where('location_il', 'Antalya')
    ->where('location_ilce', 'Muratpaşa')
    ->where('status', 1) // Aktif ilanlar
    ->get()
    ->groupBy(function($listing) {
        return $listing->room_count; // 3+1, 4+1, vs.
    })
    ->map(function($group) {
        return [
            'count' => $group->count(),
            'avg_price' => $group->avg('price'),
            'avg_price_per_m2' => $group->avg(function($item) {
                return $item->price / ($item->m2_net ?? 1);
            })
        ];
    });

// Sonuç:
// [
//   '3+1' => ['count' => 45, 'avg_price' => 1650000, 'avg_price_per_m2' => 13750],
//   '4+1' => ['count' => 32, 'avg_price' => 2100000, 'avg_price_per_m2' => 15000]
// ]
```

### Senaryo 4: Rakip İlan Takibi

**Durum:** Belirli bir rakip ilanı takip etmek istiyorsunuz.

**Sistem Ne Yapar:**
1. `external_id` ile ilanı bulur
2. Fiyat değişikliklerini `price_history`'ye ekler
3. Kalktı mı kontrol eder (`status` = 0)
4. Bildirim gönderir (fiyat değişti, kalktı, vs.)

**Kod Örneği:**
```php
// n8n bot'u her saat kontrol eder
$listing = MarketListing::where('source', 'sahibinden')
    ->where('external_id', '123456')
    ->first();

// Fiyat değişti mi?
if ($listing->price != $newPrice) {
    // Fiyat geçmişine ekle
    $listing->addPriceHistory($newPrice);
    $listing->price = $newPrice;
    $listing->save();
    
    // Bildirim gönder
    $notificationService->sendPriceChangeAlert($listing);
}

// İlan kalktı mı?
if ($listing->status == 1 && $isRemoved) {
    $listing->status = 0; // Pasif
    $listing->save();
    
    // Bildirim gönder
    $notificationService->sendListingRemovedAlert($listing);
}
```

---

## 📊 DASHBOARD GÖRSELLEŞTİRMELERİ

### 1. Fiyat Karşılaştırma Grafiği (Bar Chart)

```
Kendi İlanınız:  ████████████████████ 1.800.000 TL
Piyasa Ort.:     ████████████████ 1.650.000 TL
En Düşük:        ████████████ 1.400.000 TL
En Yüksek:       ████████████████████████ 2.100.000 TL
```

**Kullanılan Teknoloji:** Chart.js (Bar Chart)

### 2. Fiyat Trend Grafiği (Line Chart)

```
Fiyat (TL)
2.000.000 │                    ●
          │              ●
1.500.000 │    ●
          │
          └─────────────────────────
           20 Kas  25 Kas  27 Kas
```

**Kullanılan Teknoloji:** Chart.js (Line Chart)

### 3. Lokasyon Haritası (Heat Map)

```
Antalya - Muratpaşa
├─ 3+1 Daireler: 45 ilan, Ort: 1.650.000 TL
├─ 4+1 Daireler: 32 ilan, Ort: 2.100.000 TL
└─ Villa: 12 ilan, Ort: 4.500.000 TL
```

**Kullanılan Teknoloji:** Google Maps API veya Leaflet.js

### 4. Kaynak Dağılımı (Pie Chart)

```
Sahibinden: 46%  ████████████████
Hepsiemlak: 33% ████████████
Emlakjet:   21%  ████████
```

**Kullanılan Teknoloji:** Chart.js (Doughnut Chart)

---

## 🔧 TEKNİK DETAYLAR

### Veri Akışı

```
1. n8n Bot (Her saat)
   ↓
2. Web Scraping (Sahibinden, Hepsiemlak, Emlakjet)
   ↓
3. Veri İşleme (JSON formatına çevirme)
   ↓
4. Laravel Webhook (POST /api/admin/market-intelligence/sync)
   ↓
5. MarketIntelligenceController
   ↓
6. MarketListing Model (updateOrCreate)
   ↓
7. market_listings Tablosu (yalihan_market veritabanı)
```

### Model Kullanımı

```php
use App\Models\MarketListing;

// Aktif ilanları getir
$activeListings = MarketListing::active()->get();

// Sahibinden'den gelen ilanlar
$sahibindenListings = MarketListing::source('sahibinden')->get();

// Son 7 günde görülen ilanlar
$recentListings = MarketListing::lastSeenAfter(now()->subDays(7))->get();

// Fiyat geçmişine kayıt ekle
$listing->addPriceHistory(1500000, '2025-11-27');
```

### AI Entegrasyonu

**YalihanCortex ile Entegrasyon:**
```php
// Market Intelligence Service
$analysis = $yalihanCortex->analyzeMarketCompetition($ilan);

// AI Önerileri:
[
    'price_recommendation' => [
        'action' => 'reduce',
        'percentage' => 5,
        'suggested_price' => 1710000,
        'reason' => 'Piyasaya göre %9 pahalısınız. %5 indirimle satılabilir.',
        'confidence' => 0.87
    ],
    'market_position' => 'above_average',
    'competitor_count' => 12,
    'similar_listings' => [...]
]
```

---

## 📈 İSTATİSTİKLER VE RAPORLAR

### Günlük Rapor

```
📊 Pazar İstihbaratı - Günlük Rapor
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

🆕 Yeni İlanlar: 45
📈 Fiyat Artışı: 12 ilan
📉 Fiyat Düşüşü: 8 ilan
❌ Kalkan İlanlar: 5

📍 Antalya - Muratpaşa
   ├─ 3+1 Ortalama: 1.650.000 TL
   ├─ 4+1 Ortalama: 2.100.000 TL
   └─ Villa Ortalama: 4.500.000 TL

💡 Öneriler:
   - İlan #123: %5 indirim öneriliyor
   - İlan #456: Fiyat uygun, koruyun
```

---

## 🚀 SONRAKI ADIMLAR

1. **n8n Bot Geliştirme:**
   - Sahibinden scraper
   - Hepsiemlak scraper
   - Emlakjet scraper

2. **Dashboard Geliştirme:**
   - Fiyat karşılaştırma grafikleri
   - Lokasyon haritası
   - Trend analizi

3. **AI Önerileri:**
   - Otomatik fiyat önerileri
   - Piyasa durumu analizi
   - Satış tahminleri

4. **Bildirimler:**
   - Fiyat değişikliği bildirimleri
   - Yeni rakip ilan bildirimleri
   - Piyasa trend bildirimleri

---

## ✅ ÖZET

**Pazar İstihbaratı Sistemi:**
- ✅ Dış portallardan otomatik veri çekme
- ✅ Fiyat karşılaştırması ve analiz
- ✅ Piyasa trend takibi
- ✅ AI destekli fiyat önerileri
- ✅ Lokasyon bazlı istatistikler
- ✅ Rakip ilan takibi

**Faydaları:**
- 💰 Doğru fiyatlandırma
- 📊 Piyasa bilgisi
- 🎯 Rekabet avantajı
- 🤖 Otomatik analiz
- 📈 Satış artışı

---

**Son Güncelleme:** 2025-11-27  
**Durum:** ✅ Altyapı Hazır, Dashboard Geliştirilecek







