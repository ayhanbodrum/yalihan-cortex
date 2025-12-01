# 🧠 Yalihan Cortex - Çalışma Mantığı ve Sistem Mimarisi

**Tarih:** 2025-11-27  
**Versiyon:** 1.0  
**Durum:** ✅ Production'da Aktif

---

## 📋 İçindekiler

1. [Genel Bakış](#genel-bakış)
2. [Sistem Mimarisi](#sistem-mimarisi)
3. [Ana Bileşenler](#ana-bileşenler)
4. [Çalışma Akışı](#çalışma-akışı)
5. [Algoritma Detayları](#algoritma-detayları)
6. [Kullanım Senaryoları](#kullanım-senaryoları)
7. [Performans İzleme](#performans-izleme)
8. [Hata Yönetimi](#hata-yönetimi)

---

## 🎯 Genel Bakış

**YalihanCortex**, tüm AI servislerini yöneten merkezi bir "beyin" sistemidir. Sistem, emlak talepleri için akıllı eşleştirme, müşteri churn risk analizi, fiyat değerleme ve AI destekli öneriler sunar.

### Temel Özellikler

- ✅ **Merkezi Yönetim:** Tüm AI işlemleri tek bir noktadan yönetilir
- ✅ **Kâr Odaklı Zekâ:** Action Score algoritması ile en kârlı eşleşmeleri önceliklendirir
- ✅ **Churn Risk Analizi:** Müşteri kaybı riskini önceden tespit eder
- ✅ **Performans İzleme:** Tüm işlemler timer ile ölçülür ve AiLog'a kaydedilir
- ✅ **Fallback Sistemi:** AI provider hatalarında otomatik yedek provider'a geçer
- ✅ **Context7 Uyumlu:** Tüm işlemler MCP standartlarına uygun

---

## 🏗️ Sistem Mimarisi

```
┌─────────────────────────────────────────────────────────┐
│                    AIController                         │
│  (API Endpoint: /api/admin/ai/find-matches)            │
└────────────────────┬──────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│              YalihanCortex (Merkezi Beyin)              │
│  ├─ matchForSale()     → Talep eşleştirme              │
│  ├─ priceValuation()   → Fiyat değerleme              │
│  └─ handleFallback()   → Hata yönetimi                 │
└─────┬──────────┬──────────┬──────────┬───────────────┘
      │          │          │          │
      ▼          ▼          ▼          ▼
┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│SmartProp │ │KisiChurn │ │ Finans   │ │  TKGM    │
│MatcherAI │ │ Service  │ │ Service  │ │ Service  │
└──────────┘ └──────────┘ └──────────┘ └──────────┘
      │          │          │          │
      └──────────┴──────────┴──────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│              LogService (Timer & Logging)               │
│  ├─ startTimer()  → İşlem başlangıç zamanı             │
│  ├─ stopTimer()   → İşlem süresi (milisaniye)         │
│  └─ ai()          → AI işlem logları                   │
└────────────────────┬──────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│                  AiLog (Veritabanı)                    │
│  ├─ provider: "YalihanCortex"                          │
│  ├─ request_type: "cortex_decision"                     │
│  ├─ response_time: 245.67 (ms)                          │
│  └─ status: "success" / "failed"                       │
└─────────────────────────────────────────────────────────┘
```

---

## 🔧 Ana Bileşenler

### 1. YalihanCortex Servisi

**Dosya:** `app/Services/AI/YalihanCortex.php`

**Dependency Injection ile Enjekte Edilen Servisler:**

```php
- SmartPropertyMatcherAI  → Emlak eşleştirme algoritması
- KisiChurnService        → Müşteri churn risk analizi
- FinansService           → Finansal değerleme
- TKGMService             → Tapu ve Kadastro verileri
- AIService               → Genel AI işlemleri (GPT, Gemini, vb.)
```

### 2. LogService (Timer Sistemi)

**Dosya:** `app/Services/Logging/LogService.php`

**Timer Metodları:**

```php
// Timer başlat
$startTime = LogService::startTimer('operation_name');

// İşlemler...

// Timer durdur ve milisaniye al
$durationMs = LogService::stopTimer($startTime);
```

### 3. AiLog Modeli

**Veritabanı Tablosu:** `ai_logs`

**Kaydedilen Veriler:**
- `provider`: "YalihanCortex"
- `request_type`: "cortex_decision"
- `content_type`: "match_for_sale" veya "price_valuation"
- `content_id`: Talep ID veya İlan ID
- `status`: "success" veya "failed"
- `response_time`: Milisaniye (integer)
- `request_data`: Context verileri (JSON)
- `response_data`: Decision bilgileri (JSON)

---

## 🔄 Çalışma Akışı

### Senaryo 1: Talep Eşleştirme (`matchForSale`)

#### Adım 1: İstek Alınır

```php
// AIController'dan gelen istek
$cortexResult = $this->cortex->matchForSale($talep);
```

#### Adım 2: Timer Başlatılır

```php
$startTime = LogService::startTimer('yalihan_cortex_match_for_sale');
```

#### Adım 3: Churn Risk Analizi

**Eğer talep bir kişiye bağlıysa:**

```php
$churnRisk = $this->churnService->calculateChurnRisk($talep->kisi);

// Churn Risk Hesaplama Mantığı:
// 1. Son Etkileşim Analizi
//    - 60+ gün geçtiyse: +40 puan
//    - 30-60 gün: +20 puan
// 2. Talep Yaşı Analizi
//    - 90+ günlük talep: +30 puan
// 3. Pipeline Durumu
//    - Soğuk segment veya geride pipeline: +20 puan
// 
// Toplam: 0-100 arası churn skoru
```

**Churn Risk Seviyeleri:**
- **0-39:** Düşük Risk (Normal takip)
- **40-69:** Orta Risk (Dikkatli takip)
- **70-100:** Yüksek Risk (Acil müdahale)

#### Adım 4: Property Matching

```php
$matches = $this->propertyMatcher->match($talep);

// SmartPropertyMatcherAI İşlem Adımları:
// 1. Hard Filtering (Zorunlu Filtreler)
//    - İl, İlçe, Mahalle eşleşmesi
//    - Fiyat aralığı
//    - Emlak tipi (Daire, Villa, Arsa, vb.)
//    - Oda sayısı
//    - Metrekare aralığı
//
// 2. Soft Scoring (Esnek Puanlama)
//    - Konum benzerliği: 0-30 puan
//    - Fiyat uyumu: 0-25 puan
//    - Özellik eşleşmesi: 0-20 puan
//    - Metrekare uyumu: 0-15 puan
//    - Oda sayısı uyumu: 0-10 puan
//
// 3. Sıralama ve Limit
//    - En yüksek skorlu ilanlar
//    - Maksimum 20 sonuç
```

#### Adım 5: Eşleşmeleri Zenginleştirme (Action Score)

**KÂR ODAKLI ZEKÂ ALGORİTMASI:**

```php
// Her eşleşme için Action Score hesaplanır
$actionScore = $matchScore + ($churnScore * 0.5);

// Örnek:
// Match Score: 85
// Churn Score: 60
// Action Score: 85 + (60 * 0.5) = 85 + 30 = 115

// Filtreleme:
// - Sadece action_score > 85 olan eşleşmeler döndürülür
// - En yüksek action_score'a göre sıralanır
// - İlk 5 eşleşme seçilir
```

**Action Score Mantığı:**
- **Yüksek Match + Yüksek Churn** → Çok yüksek Action Score (Acil satış fırsatı)
- **Yüksek Match + Düşük Churn** → İyi Action Score (Normal öncelik)
- **Düşük Match + Yüksek Churn** → Orta Action Score (Dikkatli değerlendirme)

#### Adım 6: Akıllı Öneriler

```php
$recommendations = $this->generateRecommendations($talep, $result);

// Öneri Türleri:
// 1. Churn Risk Önerileri
//    - "Acil müdahale gerekli. Müşteri ile hemen iletişime geçin."
//    - "Dikkatli takip edilmeli. Proaktif iletişim önerilir."
//
// 2. Eşleşme Önerileri
//    - "5 adet yüksek uyumlu ilan bulundu."
//    - "Fiyat aralığını genişletirseniz daha fazla seçenek bulabilirsiniz."
//
// 3. Aksiyon Önerileri
//    - "En yüksek action score'a sahip ilanı önceliklendirin."
//    - "Churn riski yüksek müşteri için hızlı aksiyon alın."
```

#### Adım 7: Timer Durdurulur ve Log Kaydedilir

```php
$durationMs = LogService::stopTimer($startTime);

// AiLog'a kayıt
$this->logCortexDecision('match_for_sale', [
    'talep_id' => $talep->id,
    'matches_count' => count($result['matches']),
    'churn_score' => $churnScore,
], $durationMs, true);
```

#### Adım 8: Sonuç Döndürülür

```json
{
  "talep_id": 123,
  "kisi_id": 456,
  "churn_analysis": {
    "risk_score": 60,
    "risk_level": "medium",
    "breakdown": {
      "baz_puan": 20,
      "talep_yasi": 30,
      "pipeline_durumu": 10
    },
    "recommendation": "Dikkatli takip edilmeli. Proaktif iletişim önerilir."
  },
  "matches": [
    {
      "ilan_id": 789,
      "baslik": "3+1 Daire, Bodrum",
      "fiyat": 1500000,
      "para_birimi": "TRY",
      "match_score": 85.5,
      "churn_score": 60.0,
      "action_score": 115.5,
      "match_level": "excellent",
      "priority": "high",
      "reasons": ["Konum uyumu", "Fiyat uyumu"],
      "breakdown": {
        "konum": 28,
        "fiyat": 22,
        "ozellikler": 18
      }
    }
  ],
  "recommendations": [
    "5 adet yüksek uyumlu ilan bulundu.",
    "Churn riski orta seviyede. Proaktif iletişim önerilir."
  ],
  "metadata": {
    "processed_at": "2025-11-27T14:30:00.000Z",
    "algorithm": "YalihanCortex v1.0",
    "duration_ms": 245.67,
    "matches_count": 5,
    "success": true
  }
}
```

---

### Senaryo 2: Fiyat Değerleme (`priceValuation`)

#### Adım 1: İstek Alınır

```php
$cortexResult = $this->cortex->priceValuation($ilan);
```

#### Adım 2: Timer Başlatılır

```php
$startTime = LogService::startTimer('yalihan_cortex_price_valuation');
```

#### Adım 3: TKGM Verileri Çekilir

```php
$tkgmData = $this->tkgmService->getPropertyData($ilan);

// TKGM Servisi:
// - Tapu bilgileri
// - Parsel bilgileri
// - İmar durumu
// - Bölge değerleme verileri
```

#### Adım 4: Finansal Analiz

```php
$financialAnalysis = $this->finansService->analyze($ilan);

// Finans Servisi:
// - Piyasa fiyat analizi
// - Bölge ortalamaları
// - Metrekare başına fiyat
// - Yatırım potansiyeli
```

#### Adım 5: Değerleme Hesaplama

```php
// TKGM ve Finans verileri birleştirilir
$valuation = [
    'market_value' => $calculatedValue,
    'tkgm_data' => $tkgmData,
    'financial_analysis' => $financialAnalysis,
    'confidence_score' => $confidence, // 0-100
];
```

#### Adım 6: Timer Durdurulur ve Log Kaydedilir

```php
$durationMs = LogService::stopTimer($startTime);

$this->logCortexDecision('price_valuation', [
    'ilan_id' => $ilan->id,
    'confidence_score' => $confidence,
], $durationMs, true);
```

---

## 🧮 Algoritma Detayları

### 1. Action Score Hesaplama

**Formül:**
```
Action Score = Match Score + (Churn Score × 0.5)
```

**Örnek Senaryolar:**

| Match Score | Churn Score | Action Score | Açıklama |
|-------------|-------------|--------------|----------|
| 90 | 80 | 130 | **Çok Yüksek Öncelik** - Mükemmel eşleşme + Yüksek churn riski |
| 85 | 60 | 115 | **Yüksek Öncelik** - İyi eşleşme + Orta churn riski |
| 80 | 40 | 100 | **Orta Öncelik** - İyi eşleşme + Düşük churn riski |
| 70 | 20 | 80 | **Düşük Öncelik** - Orta eşleşme + Çok düşük churn riski |

**Filtreleme Kuralı:**
- Sadece `action_score > 85` olan eşleşmeler döndürülür
- En yüksek action_score'a göre sıralanır
- İlk 5 eşleşme seçilir

### 2. Churn Risk Hesaplama

**Puanlama Sistemi:**

```php
// 1. Son Etkileşim Analizi (0-40 puan)
if ($gunFarki >= 60) {
    $bazPuan = 40;  // 60+ gün geçti
} elseif ($gunFarki >= 30) {
    $bazPuan = 20;  // 30-60 gün
} else {
    $bazPuan = 0;   // 30 günden az
}

// 2. Talep Yaşı Analizi (0-30 puan)
if ($talepGunFarki >= 90) {
    $talepYasiPuan = 30;  // 90+ günlük talep
} else {
    $talepYasiPuan = 0;
}

// 3. Pipeline Durumu (0-20 puan)
if ($isSoguk || $geride) {
    $pipelinePuan = 20;  // Soğuk segment veya geride
} else {
    $pipelinePuan = 0;
}

// Toplam: min(100, $bazPuan + $talepYasiPuan + $pipelinePuan)
```

**Risk Seviyeleri:**
- **0-39:** 🟢 Düşük Risk
- **40-69:** 🟡 Orta Risk
- **70-100:** 🔴 Yüksek Risk

### 3. Match Score Hesaplama

**Puanlama Kriterleri:**

```php
// 1. Konum Benzerliği (0-30 puan)
- Aynı mahalle: 30 puan
- Aynı ilçe: 20 puan
- Aynı il: 10 puan
- Farklı il: 0 puan

// 2. Fiyat Uyumu (0-25 puan)
- %5 içinde: 25 puan
- %10 içinde: 20 puan
- %20 içinde: 15 puan
- %30 içinde: 10 puan
- %30+ fark: 0 puan

// 3. Özellik Eşleşmesi (0-20 puan)
- Her eşleşen özellik: +5 puan
- Maksimum: 20 puan

// 4. Metrekare Uyumu (0-15 puan)
- %10 içinde: 15 puan
- %20 içinde: 10 puan
- %30 içinde: 5 puan
- %30+ fark: 0 puan

// 5. Oda Sayısı Uyumu (0-10 puan)
- Tam eşleşme: 10 puan
- 1 oda fark: 5 puan
- 2+ oda fark: 0 puan

// Toplam: 0-100 puan
```

---

## 📊 Kullanım Senaryoları

### Senaryo 1: Yeni Talep Eşleştirme

**Durum:** Müşteri yeni bir talep oluşturdu.

**İşlem Akışı:**
1. `AIController@findMatches` çağrılır
2. `YalihanCortex@matchForSale` çalıştırılır
3. Churn risk analizi yapılır (eğer kişi varsa)
4. Property matching yapılır
5. Action Score hesaplanır
6. En yüksek action_score'a sahip 5 ilan döndürülür

**Beklenen Sonuç:**
- 5 adet yüksek uyumlu ilan
- Churn risk analizi (eğer kişi varsa)
- Akıllı öneriler

### Senaryo 2: Churn Risk Yüksek Müşteri

**Durum:** Müşterinin churn riski yüksek (70+).

**İşlem Akışı:**
1. Churn risk analizi: 75 puan (Yüksek Risk)
2. Property matching: 80 match score
3. Action Score: 80 + (75 × 0.5) = 117.5
4. **Öncelik:** Çok Yüksek
5. **Öneri:** "Acil müdahale gerekli. Müşteri ile hemen iletişime geçin."

**Beklenen Sonuç:**
- Yüksek action_score'a sahip eşleşmeler
- Acil aksiyon önerileri
- Churn risk uyarıları

### Senaryo 3: Fiyat Değerleme

**Durum:** İlan için fiyat değerlemesi isteniyor.

**İşlem Akışı:**
1. `YalihanCortex@priceValuation` çağrılır
2. TKGM verileri çekilir
3. Finansal analiz yapılır
4. Değerleme hesaplanır
5. Confidence score belirlenir

**Beklenen Sonuç:**
- Piyasa değeri
- TKGM verileri
- Finansal analiz
- Confidence score (0-100)

---

## 📈 Performans İzleme

### Timer Sistemi

**Her Cortex kararı için:**
- Timer başlatılır (`LogService::startTimer`)
- İşlem yapılır
- Timer durdurulur (`LogService::stopTimer`)
- Süre milisaniye olarak kaydedilir

**Örnek Log:**
```json
{
  "provider": "YalihanCortex",
  "request_type": "cortex_decision",
  "content_type": "match_for_sale",
  "content_id": 123,
  "status": "success",
  "response_time": 245.67,
  "request_data": {
    "talep_id": 123,
    "matches_count": 5,
    "churn_score": 60
  }
}
```

### Performans Metrikleri

**Sorgulama Örnekleri:**

```php
// Ortalama response time
$avgResponseTime = AiLog::where('provider', 'YalihanCortex')
    ->where('status', 'success')
    ->avg('response_time');

// Başarı oranı
$successRate = AiLog::where('provider', 'YalihanCortex')
    ->where('status', 'success')
    ->count() / AiLog::where('provider', 'YalihanCortex')->count() * 100;

// En yavaş işlemler
$slowest = AiLog::where('provider', 'YalihanCortex')
    ->orderBy('response_time', 'desc')
    ->take(10)
    ->get();
```

---

## 🛡️ Hata Yönetimi

### Fallback Sistemi

**ProviderException Yakalama:**

```php
try {
    // AI provider işlemi
    $result = $this->aiService->generate($prompt);
} catch (\Exception $e) {
    if ($e instanceof ProviderException) {
        // Fallback provider'a geç
        $result = $this->handleFallback($provider, $data);
    }
}
```

**Fallback Provider Sırası:**

```php
$fallbackProviders = [
    'openai' => ['deepseek', 'gemini', 'ollama'],
    'gemini' => ['openai', 'deepseek', 'ollama'],
    'deepseek' => ['openai', 'gemini', 'ollama'],
    'ollama' => ['openai', 'deepseek', 'gemini'],
];
```

### Hata Loglama

**Hata durumunda:**
- Timer durdurulur
- Hata mesajı AiLog'a kaydedilir
- `status: "failed"` olarak işaretlenir
- Hata detayları `error_message` alanına yazılır

**Örnek Hata Log:**
```json
{
  "provider": "YalihanCortex",
  "request_type": "cortex_decision",
  "content_type": "match_for_sale",
  "status": "failed",
  "response_time": 120.45,
  "error_message": "PropertyMatcherAI::match() failed: No matching properties found",
  "request_data": {
    "talep_id": 123,
    "error": "No matching properties found"
  }
}
```

---

## 🔍 @CortexDecision Etiketi

**Tüm ana Cortex metodları `@CortexDecision` etiketi ile işaretlenir:**

```php
/**
 * Talep için zenginleştirilmiş eşleştirme
 *
 * @CortexDecision
 * Churn skoru + Match skoru ile kapsamlı analiz yapar
 */
public function matchForSale(Talep $talep, array $options = []): array
{
    // ...
}
```

**Bu etiket:**
- MCP uyumluluğu için zorunludur
- Cortex kararlarını tanımlar
- Timer ve AiLog kayıtlarını tetikler

---

## 📚 İlgili Dokümantasyon

- **Mimari Dokümantasyon:** `yalihan-bekci/knowledge/YALIHAN_CORTEX_ARCHITECTURE_2025-11-27.md`
- **Vision Dokümantasyon:** `docs/ai/YALIHAN_CORTEX_VISION_2.0.md`
- **API Dokümantasyonu:** `docs/api/context7-api-documentation.md`

---

## ✅ Context7 Uyumluluk

### Database Fields
- ✅ `status` (TinyInteger/Boolean)
- ✅ `response_time` (Integer - milisaniye)
- ✅ `request_data`, `response_data` (JSON)

### Logging Standards
- ✅ `LogService::ai()` kullanımı
- ✅ Timer sistemi (milisaniye bazında)
- ✅ AiLog kayıtları

### Response Standards
- ✅ `ResponseService::success()` kullanımı
- ✅ Metadata yapısı standart

---

## 🚀 Sonraki Adımlar

1. **ProviderException Sınıfı:** AI provider hataları için özel exception
2. **Dashboard Geliştirme:** Cortex kararlarını görselleştir
3. **Test Coverage:** Unit ve integration testler
4. **Performance Optimization:** Yavaş işlemlerin optimizasyonu

---

**Son Güncelleme:** 2025-11-27  
**Durum:** Production'a hazır ✅  
**Versiyon:** 1.0
