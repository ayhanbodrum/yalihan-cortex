# 📊 Pazar İstihbaratı (Market Intelligence) Sistemi - Detaylı Açıklama

**Tarih:** 2025-11-27  
**Versiyon:** 1.0.0

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

## 🔄 SİSTEMİN ÇALIŞMA MANTIĞI

### 1. VERİ TOPLAMA (n8n Botları)

```
┌─────────────────────────────────────────┐
│  n8n Workflow (Otomatik Bot)           │
│  ├─ Sahibinden.com tarama               │
│  ├─ Hepsiemlak.com tarama               │
│  └─ Emlakjet.com tarama                 │
└─────────────────────────────────────────┘
                    ↓
         [Her 1 saatte bir çalışır]
                    ↓
┌─────────────────────────────────────────┐
│  Webhook → Laravel API                  │
│  POST /api/admin/market-intelligence/  │
│       sync                              │
└─────────────────────────────────────────┘
```

**n8n Bot'unun Yaptığı İşler:**
- Her saat başı dış portalları tarar
- Yeni ilanları tespit eder
- Mevcut ilanların fiyat değişikliklerini kontrol eder
- Kalkan/satılan ilanları işaretler
- Verileri JSON formatında Laravel'e gönderir

### 2. VERİ SAKLAMA (Laravel Backend)

```
┌─────────────────────────────────────────┐
│  MarketIntelligenceController            │
│  ├─ Webhook'tan veri alır                │
│  ├─ Veriyi doğrular                       │
│  └─ MarketListing modeline kaydeder      │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│  market_listings Tablosu                 │
│  ├─ source: 'sahibinden'                │
│  ├─ external_id: '123456'                │
│  ├─ price: 1500000                       │
│  ├─ price_history: [{date, price}]       │
│  └─ snapshot_data: {ham veri}           │
└─────────────────────────────────────────┘
```

**Veritabanında Saklanan Veriler:**
- İlan başlığı, fiyat, lokasyon
- Metrekare, oda sayısı
- Fiyat geçmişi (zaman içinde değişimler)
- Ham veri (snapshot_data - tüm detaylar)
- Son görülme tarihi (last_seen_at)

### 3. VERİ ANALİZİ (AI Destekli)

```
┌─────────────────────────────────────────┐
│  Kendi İlanınız (Ilan Model)           │
│  ├─ Fiyat: 1.800.000 TL                 │
│  ├─ Lokasyon: Antalya, Muratpaşa        │
│  └─ Özellikler: 3+1, 120 m²            │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│  Market Intelligence Service            │
│  ├─ Benzer ilanları bulur               │
│  │  (Aynı lokasyon, benzer özellikler)  │
│  ├─ Ortalama fiyatı hesaplar             │
│  ├─ Fiyat farkını analiz eder           │
│  └─ AI önerisi üretir                   │
└─────────────────────────────────────────┘
                    ↓
┌─────────────────────────────────────────┐
│  Sonuç:                                  │
│  ├─ Piyasa Ortalaması: 1.650.000 TL     │
│  ├─ Fark: %9 pahalı                     │
│  └─ Öneri: %5 indirim yapın             │
└─────────────────────────────────────────┘
```

---

## 💼 PRATİK KULLANIM SENARYOLARI

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

## 🎨 DASHBOARD GÖRSELLEŞTİRMELERİ

### 1. Fiyat Karşılaştırma Grafiği

```
Kendi İlanınız:  ████████████████████ 1.800.000 TL
Piyasa Ort.:     ████████████████ 1.650.000 TL
En Düşük:        ████████████ 1.400.000 TL
En Yüksek:       ████████████████████████ 2.100.000 TL
```

### 2. Lokasyon Haritası

```
Antalya - Muratpaşa
├─ 3+1 Daireler: 45 ilan, Ort: 1.650.000 TL
├─ 4+1 Daireler: 32 ilan, Ort: 2.100.000 TL
└─ Villa: 12 ilan, Ort: 4.500.000 TL
```

### 3. Fiyat Trend Grafiği

```
Fiyat (TL)
2.000.000 │                    ●
          │              ●
1.500.000 │    ●
          │
          └─────────────────────────
           20 Kas  25 Kas  27 Kas
```

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
7. market_listings Tablosu
```

### Veri Yapısı

**market_listings Tablosu:**
- `source`: Hangi siteden geldiği (sahibinden, hepsiemlak, emlakjet)
- `external_id`: O sitedeki ilan ID'si
- `price`: Güncel fiyat
- `price_history`: Fiyat değişim geçmişi (JSON array)
- `snapshot_data`: Ham veri (tüm detaylar, JSON)
- `status`: Aktif mi? (1: Yayında, 0: Kalktı)
- `last_seen_at`: En son ne zaman kontrol edildi

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

## 📊 İSTATİSTİKLER VE RAPORLAR

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







