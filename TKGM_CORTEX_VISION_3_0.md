# 🧠 YALIHAN CORTEX + TKGM VISION 3.0: AKILLI ARSA ZEKASheyword SİSTEMİ

**Tarih:** 4 Aralık 2025  
**Durum:** 🚀 Strategic Roadmap  
**Hedef:** TKGM'i Pasif Veri Kaynağı → **Öğrenen Akıllı Arsa Analiz Merkezi**

---

## 🎯 VİZYON ÖZETİ

**TKGM Cortex**, sadece parsel bilgisi getirmekle kalmayıp, her sorguda öğrenen, analiz eden ve stratejik kararlar veren bir **Akıllı Arsa Asistanı** olacak.

### Şu Anki Durum (v1.0)
```
Kullanıcı Ada/Parsel girer
    ↓
TKGM API'den veri çeker
    ↓
Form doldurulur
    ✅ BITTI
```

### Hedef Durum (v3.0)
```
Kullanıcı Ada/Parsel girer
    ↓
TKGM API + Öğrenme Motoru
    ↓
AI Analiz + Tarihsel Karşılaştırma
    ↓
Stratejik Öneriler + Fiyat Tahmini
    ↓
Otomatik Güncelleme + Sürekli Öğrenme
    🚀 DEVAM EDER
```

---

## 📊 7 STRATEJİK MODÜL

### MODÜL 1: 🗺️ TKGM ENTEGRASYON MERKEZI (Mevcut)

**Durum:** ✅ Aktif  
**Dosya:** `app/Services/Integrations/TKGMService.php`

#### Mevcut Özellikler:
- ✅ Gerçek TKGM API entegrasyonu
- ✅ Koordinat bazlı sorgulama
- ✅ 7 gün cache + 30 gün stale fallback
- ✅ Yatırım analizi (basit)

#### Kullanım İstatistikleri:
```
Aktif Kullanım Noktaları: 6 dosya
├─ YalihanCortex.php (AI analiz)
├─ IlanAIController.php (API endpoint)
├─ AIOrchestrator.php (orkestrasyon)
├─ TKGMController.php (TKGM API)
├─ ArsaCalculationController.php (hesaplama)
└─ TKGMParselController.php (admin panel)

API Endpoint'ler: 18 route
Günlük Ortalama Çağrı: ~50-100 sorgu (tahmini)
Cache Hit Rate: %75+ (7 günlük cache)
```

---

### MODÜL 2: 🧠 TKGM ÖĞRENMe MOTORu (YENİ!)

**Durum:** 🔴 Planlanıyor  
**Hedef:** Her TKGM sorgusundan öğren, pattern'leri tespit et

#### Öğrenme Stratejisi:

```php
// app/Services/Intelligence/TKGMLearningEngine.php

class TKGMLearningEngine
{
    /**
     * Her TKGM sorgusunu kaydet ve öğren
     */
    public function learn(array $tkgmData, array $context): void
    {
        // 1. TKGM verisini kaydet
        $this->storeTKGMSnapshot($tkgmData);
        
        // 2. Pattern tespit et
        $patterns = $this->detectPatterns($tkgmData, $context);
        
        // 3. İstatistik güncelle
        $this->updateStatistics($tkgmData);
        
        // 4. Fiyat-KAKS korelasyonu öğren
        $this->learnPriceKAKSCorrelation($tkgmData, $context);
    }
    
    /**
     * Öğrenilen pattern'lere göre öneri üret
     */
    public function suggest(array $newTKGMData): array
    {
        return [
            'fiyat_tahmini' => $this->predictPrice($newTKGMData),
            'kiyaslama' => $this->compareWithSimilar($newTKGMData),
            'risk_analizi' => $this->analyzeRisk($newTKGMData),
            'yatirim_potansiyeli' => $this->evaluateInvestment($newTKGMData),
        ];
    }
}
```

#### Ne Öğrenecek?

1. **Fiyat-KAKS İlişkisi**
   ```
   Bodrum Yalıkavak, KAKS 0.50 → Ortalama ₺15.000/m²
   Bodrum Gümbet, KAKS 0.60 → Ortalama ₺12.000/m²
   ```

2. **İmar Durumu Etkisi**
   ```
   İmarlı Arsa → %30 daha pahalı
   Plan içi → %15 daha pahalı
   İmar dışı → %40 daha ucuz
   ```

3. **Lokasyon Premium'u**
   ```
   Denize 500m → +%25 fiyat
   Denize 1km → +%10 fiyat
   Denize 3km+ → Referans fiyat
   ```

---

### MODÜL 3: 📈 AKILLI FİYAT TAHMİN MOTORu

**Durum:** 🟡 Kısmen Var (PropertyValuationService'te)  
**Hedef:** TKGM verisi + Öğrenme = Doğru Fiyat Tahmini

#### Algoritma:

```
FİYAT TAHMİNİ = 
    (Alan × Birim Fiyat) × 
    (İmar Çarpanı) × 
    (Lokasyon Çarpanı) × 
    (KAKS Bonusu) × 
    (Pazar Trendi)

Örnek:
──────
Ada: 807, Parsel: 9
Alan: 1.751 m²
KAKS: 0.50
İmar: İmarlı
Lokasyon: Yalıkavak (Denize 1.2km)

Hesaplama:
Base: 1.751 × ₺12.000 = ₺21.012.000
İmar Çarpanı: × 1.30 (İmarlı) = ₺27.315.600
Lokasyon: × 1.15 (Yalıkavak) = ₺31.413.000
KAKS Bonusu: × 1.10 (0.50 KAKS) = ₺34.554.300

TAHMİNİ FİYAT: ₺34.5M - ₺38M
GÜVENİLİRLİK: %85 (50 benzer parsel analizi)
```

#### Öğrenme Döngüsü:

```
1. TKGM Verisi + Satış Fiyatı Kaydedilir
    ↓
2. Pattern Analizi (ML Model)
    ↓
3. Fiyat Tahmin Modeli Güncellenir
    ↓
4. Yeni Tahminler Daha Doğru Olur
```

---

### MODÜL 4: 🗺️ PARSEL HARİTA İSTİHBARAT

**Durum:** 🔴 Planlanıyor  
**Hedef:** TKGM GeoJSON + OSM + Uydu Görüntüleri = Tam İstihbarat

#### Entegrasyonlar:

1. **TKGM GeoJSON** (Mevcut)
   - Parsel sınırları
   - Alan bilgisi
   - Koordinatlar

2. **OpenStreetMap Overpass API** (Mevcut)
   - Çevredeki POI'ler
   - Yollar, altyapı
   - Sosyal tesisler

3. **Yeni: Uydu Görüntü Analizi** (YEK-PLUS?)
   ```
   - Arazi eğimi
   - Manzara skoru
   - Yeşil alan oranı
   - Gölgelenme analizi
   ```

4. **Yeni: İmar Planı Overlay**
   ```
   - İmar planı katmanı
   - Riskli alan kontrolü
   - Sit alanı tespiti
   ```

#### Görsel Çıktı:

```
┌─────────────────────────────────────┐
│  PARSEL 807/9 - YALIKANVAK          │
├─────────────────────────────────────┤
│  🗺️ Harita:                          │
│  ┌───────────────────────────┐      │
│  │  [Parsel Sınırları]       │      │
│  │  📍 POI: Market (200m)    │      │
│  │  🏖️ Deniz (1.2km)         │      │
│  │  🏥 Hastane (3.5km)       │      │
│  └───────────────────────────┘      │
│                                     │
│  📊 Analiz:                          │
│  • Manzara Skoru: 8.5/10           │
│  • Altyapı: %90 tamamlanmış        │
│  • Ulaşım: Asfalt yol var          │
│  • Risk: Yok                        │
└─────────────────────────────────────┘
```

---

### MODÜL 5: 🤖 TKGM + CORTEX ENTEGRASYONu

**Durum:** 🟢 Kısmen Var  
**Hedef:** Cortex'in tüm modüllerine TKGM zekası ekle

#### Entegrasyon Noktaları:

**1. Fırsat Sentezi (Opportunity Synthesis)**
```php
// Cortex'teki Action Score'a TKGM ekle
$actionScore = [
    'match_score' => 90,        // SmartPropertyMatcherAI
    'churn_risk' => 45,         // KisiChurnService
    'tkgm_quality' => 85,       // 🆕 TKGM Kalite Skoru
    'investment_score' => 92,   // 🆕 TKGM Yatırım Skoru
];

FINAL_SCORE = (match × 0.4) + (churn × 0.3) + 
              (tkgm_quality × 0.15) + (investment × 0.15)
```

**2. Pazar Hakimiyeti (Competitor Mapping)**
```php
// Rakip analizi + TKGM karşılaştırması
"Rakip 1: KAKS 0.40, ₺10M"
"Rakip 2: KAKS 0.50, ₺12M"
"BİZİM: KAKS 0.50, ₺12.5M → %4 PAHALI ama aynı KAKS"
"ÖNERİ: ₺500k indirimle satılabilir"
```

**3. Akıllı Bütçe (Budget Correction)**
```php
// TKGM verisi ile bütçe düzeltme
"Müşteri bütçesi: ₺5M"
"Bu KAKS'ta gerçekçi: ₺350m² (KAKS 0.50 bölgede)"
"ÖNERİ: 350-400m² arsa arayalım"
```

---

### MODÜL 6: 📚 TKGM VERİ DEPOSU (Knowledge Base)

**Durum:** 🔴 Planlanıyor  
**Hedef:** Tüm TKGM sorgularını kaydet, analiz et, öğren

#### Database Schema:

```sql
-- TKGM Sorgu Geçmişi
CREATE TABLE tkgm_queries (
    id BIGINT PRIMARY KEY,
    ada VARCHAR(20),
    parsel VARCHAR(20),
    il_id INT,
    ilce_id INT,
    mahalle_id INT,
    
    -- TKGM Verileri
    alan_m2 DECIMAL(10,2),
    kaks DECIMAL(4,2),
    taks INT,
    imar_statusu VARCHAR(100),
    nitelik VARCHAR(50),
    
    -- Konum
    enlem DECIMAL(10,8),
    boylam DECIMAL(11,8),
    
    -- İlan İlişkisi (opsiyonel)
    ilan_id BIGINT NULL,
    satis_fiyati DECIMAL(15,2) NULL,
    
    -- Meta
    query_source VARCHAR(50), -- 'wizard', 'calculator', 'api'
    user_id BIGINT,
    queried_at TIMESTAMP,
    
    INDEX idx_location (il_id, ilce_id),
    INDEX idx_ada_parsel (ada, parsel),
    INDEX idx_kaks (kaks),
    INDEX idx_alan (alan_m2)
);

-- TKGM Öğrenme Pattern'leri
CREATE TABLE tkgm_learning_patterns (
    id BIGINT PRIMARY KEY,
    pattern_type VARCHAR(50), -- 'price_kaks', 'location_premium', etc.
    il_id INT,
    ilce_id INT,
    
    -- Pattern Verileri (JSON)
    pattern_data JSON,
    
    -- İstatistikler
    sample_count INT,
    confidence_level DECIMAL(5,2),
    last_updated TIMESTAMP,
    
    INDEX idx_pattern_type (pattern_type),
    INDEX idx_location (il_id, ilce_id)
);
```

#### Öğrenme Sorguları:

```sql
-- 1. Ortalama KAKS-Fiyat İlişkisi (Bölge bazlı)
SELECT 
    il.adi AS il,
    ilce.adi AS ilce,
    AVG(tq.kaks) AS ort_kaks,
    AVG(tq.satis_fiyati / tq.alan_m2) AS ort_birim_fiyat,
    COUNT(*) AS sayi
FROM tkgm_queries tq
JOIN iller il ON tq.il_id = il.id
JOIN ilceler ilce ON tq.ilce_id = ilce.id
WHERE tq.satis_fiyati IS NOT NULL
GROUP BY tq.il_id, tq.ilce_id
HAVING COUNT(*) >= 5;

-- 2. İmar Durumu Fiyat Etkisi
SELECT 
    imar_statusu,
    AVG(satis_fiyati / alan_m2) AS ort_birim_fiyat,
    COUNT(*) AS sayi
FROM tkgm_queries
WHERE satis_fiyati IS NOT NULL
GROUP BY imar_statusu;

-- 3. En Çok Sorgulanan Bölgeler (Talep Analizi)
SELECT 
    il.adi,
    ilce.adi,
    COUNT(*) AS sorgu_sayisi,
    COUNT(DISTINCT user_id) AS benzersiz_kullanici
FROM tkgm_queries tq
JOIN iller il ON tq.il_id = il.id
JOIN ilceler ilce ON tq.ilce_id = ilce.id
WHERE queried_at >= NOW() - INTERVAL 30 DAY
GROUP BY tq.il_id, tq.ilce_id
ORDER BY sorgu_sayisi DESC
LIMIT 10;
```

---

### MODÜL 7: 🎓 TKGM AUTOPİLOT (Tam Otomasyon)

**Durum:** 🔴 Vizyon (6-12 ay)  
**Hedef:** İlan oluşturma sürecini %80 otomatikleştir

#### Autopilot Akışı:

```
1. KULLANICI: Ada/Parsel + Fiyat girer
    ↓
2. TKGM CORTEX:
    ├─ Parsel bilgilerini çeker
    ├─ Öğrenilen pattern'lerle karşılaştırır
    ├─ AI ile açıklama yazar
    ├─ Fotoğraf önerileri getirir
    ├─ Fiyat uyarısı verir (pahalı/ucuz)
    ├─ Benzer ilanları gösterir
    └─ Yatırım analizi yapar
    ↓
3. KULLANICI: Sadece onaylar
    ↓
4. İLAN: Otomatik yayınlanır
```

#### Autopilot Özellikleri:

**A. Akıllı Form Doldurma**
```
Alan: 1.751 m² ✅ (TKGM'den)
Konum: Yalıkavak ✅ (TKGM'den)
Koordinatlar: 37.xx, 27.xx ✅ (TKGM'den)
KAKS: 0.50 ✅ (TKGM'den - öğrenilmiş)
Fiyat Uyarısı: "⚠️ Piyasaya göre %5 pahalı" ❗
```

**B. AI Açıklama + Fotoğraf**
```
Açıklama: 
"Yalıkavak Sülüklü mevkiinde, 1.751 m² imarlı arsa. 
KAKS 0.50 ile 875 m² inşaat imkanı. 
Denize 1.2 km mesafede, deniz manzaralı. 
Tüm altyapı mevcut."
✅ AI tarafından üretildi

Fotoğraf Önerileri:
📸 Drone görüntü (arazi + manzara)
📸 Giriş cephesi (yol görünümü)
📸 Panoramik manzara
```

**C. Stratejik Öneriler**
```
💡 "Benzer parseller ₺10.5M-₺12M aralığında satılıyor"
💡 "KAKS 0.50 bölgede talep yüksek (%85 satış oranı)"
💡 "İmarlı arsa - hızlı satış potansiyeli"
💡 "Önerilen fiyat: ₺11.5M (piyasa ortalaması)"
```

---

## 🚀 IMPLEMENTATION ROADMAP

### PHASE 1: Foundation (1-2 hafta)
- [x] TKGM Service temizliği ✅
- [ ] Database schema (tkgm_queries, tkgm_learning_patterns)
- [ ] TKGMLearningEngine servis oluştur
- [ ] İlk öğrenme sorguları

### PHASE 2: Learning Engine (2-3 hafta)
- [ ] Pattern detection algoritmaları
- [ ] Fiyat-KAKS korelasyon öğrenme
- [ ] İmar durumu etki analizi
- [ ] Lokasyon premium hesaplama

### PHASE 3: Smart Predictions (2-3 hafta)
- [ ] Akıllı fiyat tahmin motoru
- [ ] Benzer parsel karşılaştırma
- [ ] Risk analizi
- [ ] Yatırım potansiyeli skoru

### PHASE 4: Cortex Integration (1-2 hafta)
- [ ] Opportunity Synthesis + TKGM
- [ ] Competitor Mapping + TKGM
- [ ] Budget Correction + TKGM
- [ ] Action Score yeniden hesaplama

### PHASE 5: Map Intelligence (3-4 hafta)
- [ ] GeoJSON entegrasyonu
- [ ] OSM Overpass API entegrasyonu
- [ ] Uydu görüntü analizi (YEK-PLUS?)
- [ ] İmar planı overlay
- [ ] Görsel istihbarat dashboard

### PHASE 6: Autopilot (4-6 hafta)
- [ ] Akıllı form doldurma
- [ ] AI açıklama + fotoğraf önerileri
- [ ] Stratejik öneriler
- [ ] Otomatik ilan oluşturma
- [ ] Kullanıcı onay akışı

---

## 💰 EXPECTED BUSINESS IMPACT

| Modül | Kar Etkisi | Zaman Tasarrufu | Kullanıcı Memnuniyeti |
|-------|-----------|-----------------|---------------------|
| **Öğrenme Motoru** | +%12 | 30 dk/ilan | +%25 |
| **Fiyat Tahmin** | +%18 | 1 saat | +%35 |
| **Harita İstihbarat** | +%8 | 45 dk | +%20 |
| **Cortex Entegrasyon** | +%15 | - | +%30 |
| **Autopilot** | +%25 | 2 saat | +%50 |
| **TOPLAM** | **+%78** | **4+ saat** | **+%160** |

### ROI Hesaplama:

```
Baseline (Şu An):
├─ Ortalama ilan oluşturma süresi: 3 saat
├─ İlan başına gelir: ₺15.000
└─ Aylık ilan sayısı: 100 ilan

Vision 3.0 (Hedef):
├─ Ortalama süre: 1 saat (-66%)
├─ İlan başına gelir: ₺26.700 (+78%)
└─ Aylık ilan sayısı: 200 ilan (+100%)

Aylık Gelir Artışı:
Önce: 100 × ₺15.000 = ₺1.5M
Sonra: 200 × ₺26.700 = ₺5.34M
────────────────────────────
ARTIŞ: +₺3.84M/ay (+256%)
```

---

## 🎯 SUCCESS METRICS

### Teknik Metrikler:
- ✅ TKGM API başarı oranı: >%95
- ✅ Cache hit rate: >%80
- ✅ Ortalama yanıt süresi: <500ms
- 🎯 Fiyat tahmin doğruluğu: >%85
- 🎯 Pattern tespit başarısı: >%75
- 🎯 Autopilot onay oranı: >%70

### İş Metrikleri:
- 🎯 İlan oluşturma süresi: -66%
- 🎯 Fiyat doğruluğu: +%80
- 🎯 Satış hızı: +%45
- 🎯 Kullanıcı memnuniyeti: +%160
- 🎯 Aylık gelir: +%256

---

## 🔥 QUICK WINS (Hemen Yapılabilir)

### Week 1: Data Collection Start
```sql
-- tkgm_queries tablosunu oluştur
-- Her TKGM sorgusunu kaydetmeye başla
-- İlan-TKGM ilişkisini kur
```

### Week 2: Basic Learning
```php
// Basit pattern tespiti
$avgKAKSPrice = DB::table('tkgm_queries')
    ->where('il_id', $ilId)
    ->avg(DB::raw('satis_fiyati / alan_m2'));

// Fiyat uyarısı
if ($userPrice > $avgKAKSPrice * 1.1) {
    return "⚠️ Piyasaya göre %10+ pahalı";
}
```

### Week 3: Cortex Integration
```php
// YalihanCortex'e TKGM kalite skoru ekle
$cortex->addTKGMQualityScore($ilan);
```

---

## 📚 RESOURCES & DOCUMENTATION

### Dosyalar:
- `app/Services/Integrations/TKGMService.php` - Mevcut servis
- `app/Services/Integrations/TKGMAgent.php` - API agent
- `TKGM_CLEANUP_COMPLETED.md` - Temizlik raporu
- `VISION_2_0_STRATEGIC_INTELLIGENCE.md` - Cortex Vision 2.0

### API Endpoints:
```
POST /api/v1/tkgm/parsel-sorgu
POST /api/v1/tkgm/yatirim-analizi
GET  /api/v1/tkgm/health
POST /api/v1/properties/tkgm-lookup
```

### External APIs:
- TKGM MEGSIS API
- Nominatim (Geocoding)
- OpenStreetMap Overpass API

---

## 🎓 LEARNING & EVOLUTION

**TKGM Cortex** sadece bir API wrapper değil, **öğrenen bir sistem**:

1. **Her sorgu** → Veri deposuna kaydedilir
2. **Her satış** → Pattern öğrenme güncellenir
3. **Her hafta** → Model yeniden eğitilir
4. **Her ay** → Tahmin doğruluğu artar

**Sonuç:** 6 ay sonra TKGM Cortex, Türkiye'nin en doğru arsa değerleme sistemi olacak! 🚀

---

## 🤝 TEAM & TIMELINE

### Ekip İhtiyacı:
- **Backend Dev:** 1 kişi (Laravel + ML)
- **Data Scientist:** 1 kişi (Pattern analizi)
- **Frontend Dev:** 1 kişi (Dashboard + Harita)

### Zaman Çizelgesi:
- **Phase 1-2:** 4-5 hafta (Foundation + Learning)
- **Phase 3-4:** 4-5 hafta (Predictions + Integration)
- **Phase 5-6:** 7-10 hafta (Map + Autopilot)
- **TOPLAM:** **15-20 hafta** (3.5-5 ay)

---

## 🎯 FINAL VISION

**2025 Q2 Hedef:**

```
Kullanıcı: "Ada 807, Parsel 9, ₺12M'ye satmak istiyorum"

TKGM Cortex:
├─ Parsel analizi: ✅ 1.751 m², KAKS 0.50, İmarlı
├─ Piyasa analizi: ⚠️ Benzer parseller ₺10.5M-₺11.5M
├─ Fiyat önerisi: 💰 ₺11.2M (piyasa ortalaması)
├─ AI açıklama: 📝 Otomatik üretildi
├─ Fotoğraf: 📸 3 önerili çekim açısı
├─ Yatırım skoru: 🏆 85/100 (A+ rating)
└─ Satış tahmini: 📅 45-60 gün

Kullanıcı: "Tamam" (1 tık)
İlan: Yayında! 🚀
```

---

**Generated by:** Yalihan Technical Architect  
**Vision:** TKGM Cortex v3.0 - Learning Intelligence System  
**Target Date:** Q2 2025 (May-June)  
**Status:** 🚀 Ready to Start

**"Data + AI + Learning = Smart Real Estate"** 🏠🧠

