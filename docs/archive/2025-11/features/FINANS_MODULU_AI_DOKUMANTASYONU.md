# 🤖 Finans Modülü - AI Destekli Dökümantasyon

**Tarih:** 25 Kasım 2025  
**Versiyon:** 1.0.0  
**Context7 Standardı:** C7-FINANS-AI-2025-11-25

---

## 📋 İÇİNDEKİLER

1. [Genel Bakış](#genel-bakış)
2. [Mimari Yapı](#mimari-yapı)
3. [AI Özellikleri](#ai-özellikleri)
4. [API Endpoints](#api-endpoints)
5. [Kullanım Örnekleri](#kullanım-örnekleri)
6. [Context7 Uyumluluk](#context7-uyumluluk)
7. [Geliştirme Rehberi](#geliştirme-rehberi)

---

## 🎯 GENEL BAKIŞ

Finans Modülü, yapay zeka destekli finansal işlem yönetimi ve komisyon hesaplama sistemi sunar. AI entegrasyonu ile:

- ✅ Otomatik finansal analiz
- ✅ Gelir/gider tahminleri
- ✅ Risk analizi
- ✅ Komisyon optimizasyonu
- ✅ Fatura önerileri
- ✅ Özet raporlar

### Özellikler

- **Finansal İşlemler (FinansalIslem)**: Komisyon, ödeme, masraf, gelir, gider yönetimi
- **Komisyonlar (Komisyon)**: Satış, kiralama, danışmanlık komisyonları
- **AI Analiz**: Finansal trend analizi ve öneriler
- **AI Tahmin**: Gelecek dönem gelir/gider tahminleri
- **AI Optimizasyon**: Komisyon oranı optimizasyonu

---

## 🏗️ MİMARİ YAPI

### Modül Yapısı

```
app/Modules/Finans/
├── Controllers/
│   ├── FinansalIslemController.php  # CRUD + AI endpoints
│   └── KomisyonController.php        # CRUD + AI endpoints
├── Models/
│   ├── FinansalIslem.php            # Finansal işlem modeli
│   └── Komisyon.php                  # Komisyon modeli
├── Services/
│   ├── FinansService.php             # AI destekli finans servisi
│   └── KomisyonService.php           # AI destekli komisyon servisi
├── routes/
│   ├── api.php                       # API routes
│   └── web.php                       # Web routes
└── FinansServiceProvider.php         # Service provider
```

### Veritabanı Yapısı

#### `finansal_islemler` Tablosu

| Alan | Tip | Açıklama |
|------|-----|----------|
| `id` | bigint | Primary key |
| `ilan_id` | bigint | İlan ID (nullable) |
| `kisi_id` | bigint | Kişi ID (Context7: musteri_id → kisi_id) |
| `gorev_id` | bigint | Görev ID (nullable) |
| `islem_tipi` | string | komisyon, odeme, masraf, gelir, gider |
| `miktar` | decimal | İşlem tutarı |
| `para_birimi` | string | Para birimi (TRY, USD, EUR) |
| `aciklama` | text | İşlem açıklaması |
| `tarih` | date | İşlem tarihi |
| `status` | string | bekliyor, onaylandi, reddedildi, tamamlandi |
| `onaylayan_id` | bigint | Onaylayan kullanıcı ID |
| `onay_tarihi` | datetime | Onay tarihi |
| `referans_no` | string | Referans numarası |
| `fatura_no` | string | Fatura numarası |
| `notlar` | text | Ek notlar |

#### `komisyonlar` Tablosu

| Alan | Tip | Açıklama |
|------|-----|----------|
| `id` | bigint | Primary key |
| `ilan_id` | bigint | İlan ID |
| `kisi_id` | bigint | Kişi ID (Context7: musteri_id → kisi_id) |
| `danisman_id` | bigint | Danışman ID |
| `komisyon_tipi` | string | satis, kiralama, danismanlik |
| `komisyon_orani` | decimal | Komisyon oranı (%) |
| `komisyon_tutari` | decimal | Komisyon tutarı |
| `para_birimi` | string | Para birimi |
| `ilan_fiyati` | decimal | İlan fiyatı |
| `hesaplama_tarihi` | date | Hesaplama tarihi |
| `odeme_tarihi` | date | Ödeme tarihi (nullable) |
| `status` | string | hesaplandi, onaylandi, odendi |
| `notlar` | text | Ek notlar |

---

## 🤖 AI ÖZELLİKLERİ

### 1. Finansal Analiz (FinansService)

**Metod:** `analyzeFinancials(array $data, array $context)`

Finansal verileri analiz eder, trendleri tespit eder ve öneriler sunar.

**Özellikler:**
- Gelir/gider trend analizi
- Anomali tespiti
- Risk faktörü belirleme
- Öneriler (kısa/uzun vadeli)

**Örnek Kullanım:**

```php
use App\Modules\Finans\Services\FinansService;
use App\Services\AIService;

$aiService = new AIService();
$finansService = new FinansService($aiService);

$data = [
    ['tarih' => '2025-11-01', 'islem_tipi' => 'gelir', 'miktar' => 50000],
    ['tarih' => '2025-11-15', 'islem_tipi' => 'gider', 'miktar' => 20000],
];

$result = $finansService->analyzeFinancials($data, [
    'kisi_id' => 1,
    'ilan_id' => 5,
]);

// Sonuç:
// - success: true
// - analysis: AI analiz sonuçları
// - insights: Trend, anomali, fırsatlar
// - recommendations: Öneriler
// - risk_level: low/medium/high
```

### 2. Finansal Tahmin (FinansService)

**Metod:** `predictFinancials(?int $kisiId, ?int $ilanId, string $period)`

Geçmiş verilere dayanarak gelecek dönem tahminleri yapar.

**Parametreler:**
- `kisiId`: Kişi ID (opsiyonel)
- `ilanId`: İlan ID (opsiyonel)
- `period`: Dönem (`month`, `quarter`, `year`)

**Örnek Kullanım:**

```php
$result = $finansService->predictFinancials(
    kisiId: 1,
    ilanId: 5,
    period: 'month'
);

// Sonuç:
// - success: true
// - prediction: {expected_income, expected_expense, net_projection}
// - confidence: 0.0-1.0 (güven seviyesi)
// - historical_trend: increasing/decreasing/stable
```

### 3. Fatura Önerisi (FinansService)

**Metod:** `suggestInvoice(FinansalIslem $islem)`

AI ile otomatik fatura önerileri oluşturur.

**Özellikler:**
- Fatura numarası formatı
- Açıklama önerisi
- Vade tarihi önerisi
- Ödeme yöntemi önerisi

**Örnek Kullanım:**

```php
$islem = FinansalIslem::find(1);
$result = $finansService->suggestInvoice($islem);

// Sonuç:
// - success: true
// - suggestions: {
//     fatura_no_format: "KOM-20251125-000001",
//     aciklama: "Komisyon - Ahmet Yılmaz - Denize Sıfır Villa",
//     vade_tarihi: "2025-12-25",
//     odeme_yontemi: "havale"
//   }
```

### 4. Risk Analizi (FinansService)

**Metod:** `analyzeRisk(?int $kisiId, ?int $ilanId)`

Finansal risk faktörlerini analiz eder.

**Özellikler:**
- Risk seviyesi (low/medium/high)
- Risk faktörleri
- Öneriler

**Örnek Kullanım:**

```php
$result = $finansService->analyzeRisk(
    kisiId: 1,
    ilanId: 5
);

// Sonuç:
// - success: true
// - risk_level: "medium"
// - risk_factors: ["Yüksek bekleyen tutar", "Düşük memnuniyet skoru"]
// - recommendations: {immediate: [...], long_term: [...]}
```

### 5. Komisyon Oranı Önerisi (KomisyonService)

**Metod:** `suggestOptimalRate(int $ilanId, string $komisyonTipi, float $ilanFiyati)`

Piyasa verilerine göre optimal komisyon oranı önerir.

**Özellikler:**
- Piyasa karşılaştırması
- Optimal oran önerisi
- Gerekçelendirme

**Örnek Kullanım:**

```php
use App\Modules\Finans\Services\KomisyonService;

$komisyonService = new KomisyonService($aiService);

$result = $komisyonService->suggestOptimalRate(
    ilanId: 5,
    komisyonTipi: 'satis',
    ilanFiyati: 1000000
);

// Sonuç:
// - success: true
// - suggested_rate: 3.2
// - suggested_amount: 32000
// - reasoning: "Piyasa standartlarına göre..."
// - market_comparison: {default_rate, market_min, market_max, market_avg}
```

### 6. Komisyon Optimizasyonu (KomisyonService)

**Metod:** `optimizeCommission(Komisyon $komisyon)`

Mevcut komisyonu optimize eder.

**Özellikler:**
- Mevcut vs optimize edilmiş karşılaştırma
- İyileştirme yüzdesi
- Öneriler

**Örnek Kullanım:**

```php
$komisyon = Komisyon::find(1);
$result = $komisyonService->optimizeCommission($komisyon);

// Sonuç:
// - success: true
// - current: {rate: 3.0, amount: 30000}
// - optimized: {rate: 3.2, amount: 32000}
// - improvement: {
//     rate_change: 0.2,
//     amount_change: 2000,
//     percentage: 6.67
//   }
```

### 7. Komisyon Analizi (KomisyonService)

**Metod:** `analyzeCommissions(?int $danismanId, ?string $komisyonTipi)`

Komisyon trendlerini analiz eder.

**Özellikler:**
- İstatistikler
- Trend analizi
- Öneriler

**Örnek Kullanım:**

```php
$result = $komisyonService->analyzeCommissions(
    danismanId: 1,
    komisyonTipi: 'satis'
);

// Sonuç:
// - success: true
// - statistics: {
//     total_commissions: 25,
//     total_amount: 750000,
//     average_rate: 3.1,
//     average_amount: 30000
//   }
// - insights: [...]
// - recommendations: [...]
// - trends: {trend: "increasing", recent_avg: 32000, older_avg: 28000}
```

---

## 🔌 API ENDPOINTS

### Finansal İşlemler API

#### CRUD Endpoints

```
GET    /api/finans/islemler              # Liste
GET    /api/finans/islemler/{id}         # Detay
POST   /api/finans/islemler              # Oluştur
PUT    /api/finans/islemler/{id}         # Güncelle
DELETE /api/finans/islemler/{id}         # Sil
```

#### Status Management

```
POST   /api/finans/islemler/{id}/approve    # Onayla
POST   /api/finans/islemler/{id}/reject     # Reddet
POST   /api/finans/islemler/{id}/complete   # Tamamla
```

#### 🤖 AI Endpoints

```
POST   /api/finans/islemler/ai/analyze      # Finansal analiz
POST   /api/finans/islemler/ai/predict      # Finansal tahmin
GET    /api/finans/islemler/{id}/ai/invoice # Fatura önerisi
POST   /api/finans/islemler/ai/risk         # Risk analizi
POST   /api/finans/islemler/ai/summary      # Özet rapor
```

### Komisyonlar API

#### CRUD Endpoints

```
GET    /api/finans/komisyonlar              # Liste
GET    /api/finans/komisyonlar/{id}         # Detay
POST   /api/finans/komisyonlar              # Oluştur
PUT    /api/finans/komisyonlar/{id}         # Güncelle
DELETE /api/finans/komisyonlar/{id}         # Sil
```

#### Status Management

```
POST   /api/finans/komisyonlar/{id}/approve     # Onayla
POST   /api/finans/komisyonlar/{id}/pay         # Öde
POST   /api/finans/komisyonlar/{id}/recalculate # Yeniden hesapla
```

#### 🤖 AI Endpoints

```
POST   /api/finans/komisyonlar/ai/suggest-rate  # Optimal oran önerisi
POST   /api/finans/komisyonlar/{id}/ai/optimize  # Optimizasyon
POST   /api/finans/komisyonlar/ai/analyze       # Komisyon analizi
```

---

## 💻 KULLANIM ÖRNEKLERİ

### Örnek 1: Finansal Analiz (API)

```bash
curl -X POST https://api.example.com/api/finans/islemler/ai/analyze \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "kisi_id": 1,
    "ilan_id": 5,
    "start_date": "2025-11-01",
    "end_date": "2025-11-30"
  }'
```

**Yanıt:**

```json
{
  "success": true,
  "message": "AI finansal analiz tamamlandı",
  "data": {
    "success": true,
    "analysis": {...},
    "insights": {
      "trend": "increasing",
      "anomalies": [],
      "opportunities": ["Gelir artışı tespit edildi"]
    },
    "recommendations": {
      "immediate": ["Bekleyen işlemleri kontrol edin"],
      "long_term": ["Düzenli analiz yapın"]
    },
    "risk_level": "low"
  }
}
```

### Örnek 2: Komisyon Oranı Önerisi (API)

```bash
curl -X POST https://api.example.com/api/finans/komisyonlar/ai/suggest-rate \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "ilan_id": 5,
    "komisyon_tipi": "satis",
    "ilan_fiyati": 1000000
  }'
```

**Yanıt:**

```json
{
  "success": true,
  "message": "AI komisyon oranı önerisi tamamlandı",
  "data": {
    "success": true,
    "suggested_rate": 3.2,
    "suggested_amount": 32000,
    "reasoning": "Piyasa standartlarına göre satış komisyonu için %3.2 optimal orandır.",
    "market_comparison": {
      "default_rate": 3.0,
      "market_min": 2.5,
      "market_max": 4.0,
      "market_avg": 3.1
    }
  }
}
```

### Örnek 3: Otomatik Komisyon Hesaplama (Service)

```php
use App\Modules\Finans\Services\KomisyonService;
use App\Services\AIService;

$aiService = new AIService();
$komisyonService = new KomisyonService($aiService);

// AI ile otomatik hesaplama
$komisyon = $komisyonService->calculateCommission(
    ilanId: 5,
    kisiId: 1,
    danismanId: 2,
    komisyonTipi: 'satis'
);

// Komisyon otomatik olarak oluşturuldu:
// - komisyon_orani: AI önerisi (örn: 3.2)
// - komisyon_tutari: 32000
// - status: hesaplandi
```

### Örnek 4: Finansal Tahmin (Service)

```php
use App\Modules\Finans\Services\FinansService;

$finansService = new FinansService($aiService);

// Gelecek ay için tahmin
$result = $finansService->predictFinancials(
    kisiId: 1,
    ilanId: null,
    period: 'month'
);

if ($result['success']) {
    $prediction = $result['prediction'];
    echo "Beklenen Gelir: {$prediction['expected_income']} TL\n";
    echo "Beklenen Gider: {$prediction['expected_expense']} TL\n";
    echo "Net Projeksiyon: {$prediction['net_projection']} TL\n";
    echo "Güven Seviyesi: " . ($result['confidence'] * 100) . "%\n";
}
```

---

## ✅ CONTEXT7 UYUMLULUK

### Veritabanı Alanları

- ✅ `musteri_id` → `kisi_id` (Context7 standardı)
- ✅ `status` kullanımı (enabled/aktif değil)
- ✅ `para_birimi` kullanımı (currency değil)

### Model İlişkileri

```php
// FinansalIslem Model
public function kisi()
{
    return $this->belongsTo(Kisi::class, 'kisi_id');
}

// Komisyon Model
public function kisi()
{
    return $this->belongsTo(Kisi::class, 'kisi_id');
}
```

### API Standartları

- ✅ `ResponseService` kullanımı
- ✅ `LogService::action()` ile loglama
- ✅ Standardize edilmiş hata mesajları
- ✅ Validation error handling

### Service Standartları

- ✅ `AIService` entegrasyonu
- ✅ Exception handling
- ✅ Fallback mekanizmaları
- ✅ Cache kullanımı (gerekli yerlerde)

---

## 🛠️ GELİŞTİRME REHBERİ

### Yeni AI Özelliği Ekleme

1. **Service'e metod ekle:**

```php
// app/Modules/Finans/Services/FinansService.php

public function newAIFeature(array $data): array
{
    try {
        $prompt = $this->buildFeaturePrompt($data);
        $aiResult = $this->aiService->analyze($data, ['type' => 'new_feature']);
        
        return [
            'success' => true,
            'result' => $aiResult,
            'metadata' => ['analyzed_at' => now()],
        ];
    } catch (\Exception $e) {
        LogService::error('Yeni özellik hatası', ['error' => $e->getMessage()], $e);
        return ['success' => false, 'error' => $e->getMessage()];
    }
}
```

2. **Controller'a endpoint ekle:**

```php
// app/Modules/Finans/Controllers/FinansalIslemController.php

public function aiNewFeature(Request $request)
{
    $validator = Validator::make($request->all(), [
        'data' => 'required|array',
    ]);

    if ($validator->fails()) {
        return ResponseService::validationError($validator->errors()->toArray());
    }

    try {
        $result = $this->finansService->newAIFeature($request->input('data'));
        return ResponseService::success($result, 'AI özellik tamamlandı');
    } catch (\Exception $e) {
        return ResponseService::serverError('AI özellik başarısız', $e);
    }
}
```

3. **Route ekle:**

```php
// app/Modules/Finans/routes/api.php

Route::post('/ai/new-feature', [FinansalIslemController::class, 'aiNewFeature'])
    ->name('ai.new-feature');
```

### Test Yazma

```php
// tests/Unit/Modules/Finans/Services/FinansServiceTest.php

use App\Modules\Finans\Services\FinansService;
use App\Services\AIService;

class FinansServiceTest extends TestCase
{
    public function test_analyze_financials()
    {
        $aiService = new AIService();
        $finansService = new FinansService($aiService);
        
        $data = [
            ['tarih' => '2025-11-01', 'islem_tipi' => 'gelir', 'miktar' => 50000],
        ];
        
        $result = $finansService->analyzeFinancials($data);
        
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('analysis', $result);
    }
}
```

---

## 📊 PERFORMANS VE OPTİMİZASYON

### Cache Stratejisi

- AI analiz sonuçları cache'lenir (1 saat)
- Piyasa verileri cache'lenir (6 saat)
- Trend analizleri cache'lenir (30 dakika)

### AI Provider Seçimi

AIService otomatik olarak aktif provider'ı kullanır. Provider değiştirmek için:

```php
$aiService->switchProvider('openai'); // veya 'google', 'claude', 'deepseek', 'ollama'
```

### Fallback Mekanizması

AI hatalarında fallback mekanizmaları devreye girer:

- **Analiz hatası**: Basit istatistiksel analiz
- **Tahmin hatası**: Trend bazlı basit tahmin
- **Öneri hatası**: Varsayılan değerler

---

## 🔒 GÜVENLİK

### Authentication

Tüm API endpoint'leri `auth:sanctum` middleware'i ile korunur.

### Authorization

- Kullanıcılar sadece kendi işlemlerini görebilir (gelecek güncelleme)
- Admin kullanıcılar tüm işlemleri görebilir

### Validation

Tüm giriş verileri Laravel Validator ile kontrol edilir.

---

## 📝 CHANGELOG

### v1.0.0 (2025-11-25)

- ✅ Finansal İşlemler CRUD
- ✅ Komisyonlar CRUD
- ✅ AI destekli finansal analiz
- ✅ AI destekli tahmin
- ✅ AI destekli risk analizi
- ✅ AI destekli komisyon optimizasyonu
- ✅ Context7 uyumluluğu (musteri → kisi)
- ✅ API endpoints
- ✅ Service layer
- ✅ Logging entegrasyonu

---

## 🤝 KATKIDA BULUNMA

1. Context7 standartlarına uyun
2. AI özellikleri için AIService kullanın
3. ResponseService ile standart yanıtlar döndürün
4. LogService ile loglama yapın
5. Test yazın

---

## 📚 İLGİLİ DÖKÜMANLAR

- [AI Kullanım Örnekleri](../ai/AI_KULLANIM_ORNEKLERI.md)
- [Context7 Standartları](../../.context7/authority.json)
- [API Standartları](../technical/API_STANDARDS.md)
- [Service Layer Pattern](../technical/SERVICE_LAYER_PATTERN.md)

---

**Son Güncelleme:** 25 Kasım 2025  
**Yazar:** Yalıhan AI Development Team  
**Lisans:** Proprietary

