# 📊 Analytics Sistemi - Dökümantasyon

**Tarih:** 25 Kasım 2025  
**Versiyon:** 1.0.0  
**Context7 Standardı:** C7-ANALYTICS-2025-11-25

---

## 📋 İÇİNDEKİLER

1. [Genel Bakış](#genel-bakış)
2. [AI Karar Tutarsızlığı Analizi](#ai-karar-tutarsızlığı-analizi)
3. [Komisyon Eksikliği Risk Analizi](#komisyon-eksikliği-risk-analizi)
4. [CSS İhlalleri Taraması](#css-ihlalleri-taraması)
5. [API Endpoints](#api-endpoints)
6. [Kullanım Örnekleri](#kullanım-örnekleri)

---

## 🎯 GENEL BAKIŞ

Analytics Sistemi, proje içindeki kritik analizleri otomatikleştirir:

- ✅ **AI Karar Tutarsızlığı**: Farklı danışmanların aynı AI önerisine verdiği geri bildirimlerin tutarlılığını analiz eder
- ✅ **Komisyon Risk Analizi**: Çift danışman durumunda komisyon kaybı riskini tespit eder
- ✅ **CSS İhlalleri**: Bootstrap ve Neo Design sınıflarını tespit eder

---

## 🤖 AI KARAR TUTARSIZLIĞI ANALİZİ

### Amaç

Aynı AI önerisine (`request_data`) farklı danışmanların verdiği geri bildirimlerin (`user_rating`) tutarlılığını kontrol eder.

### Nasıl Çalışır?

1. `AiLog` tablosunda aynı `request_data`'ya sahip kayıtları gruplar
2. Her grup için ortalama `user_rating` hesaplar
3. Ortalamadan 2+ puan sapma gösteren kayıtları anomali olarak işaretler
4. Şiddet seviyesine göre sıralar (low, medium, high, critical)

### Kullanım

```php
use App\Services\Analytics\AIDecisionInconsistencyAnalyzer;

$analyzer = new AIDecisionInconsistencyAnalyzer();
$result = $analyzer->analyze(minRecords: 5, threshold: 2.0);
```

### Sonuç Formatı

```json
{
  "success": true,
  "total_groups_analyzed": 15,
  "inconsistent_groups": 3,
  "inconsistencies": [
    {
      "request_data_hash": "abc123...",
      "request_data": {...},
      "total_records": 8,
      "average_rating": 4.2,
      "rating_distribution": {"4": 5, "1": 1, "5": 2},
      "anomalies": [
        {
          "log_id": 123,
          "user_id": 5,
          "user_name": "Ahmet Yılmaz",
          "rating": 1,
          "average_rating": 4.2,
          "deviation": 3.2,
          "feedback_type": "negative",
          "feedback_reason": "AI önerisi yanlış",
          "created_at": "2025-11-25 10:30:00"
        }
      ],
      "severity": "high"
    }
  ],
  "summary": {
    "total_inconsistent_groups": 3,
    "total_anomalies": 5,
    "affected_users": 3,
    "severity_distribution": {
      "critical": 0,
      "high": 1,
      "medium": 2,
      "low": 0
    },
    "recommendations": [
      "Yüksek seviyede tutarsızlıklar var. AI kullanım rehberi gözden geçirilmeli."
    ]
  }
}
```

### Ne Zaman Kullanılır?

- Danışman eğitimi planlarken
- AI önerilerinin kalitesini değerlendirirken
- Tutarsız geri bildirimleri tespit ederken

---

## 💰 KOMİSYON EKSİKLİĞİ RİSK ANALİZİ

### Amaç

Çift danışman durumunda (satıcı + alıcı) komisyon kaybı riskini tespit eder.

### Nasıl Çalışır?

1. Geçen yıl tamamlanmış satışları (`status = 'tamamlandi'`) analiz eder
2. **Gerçek Risk Analizi:**
    - İlan danışmanı ile satış danışmanı farklı olan satışları tespit eder
    - Müşteri danışmanı ile satış danışmanı farklı olan satışları tespit eder
3. **Simülasyon (Opsiyonel):**
    - Satışların %30'unda (varsayılan) farklı bir alıcı danışmanı olduğunu varsayar
    - Bu durumda komisyon kaybı riskini hesaplar
4. Çift danışman durumunda komisyonun nasıl bölüşüleceğini simüle eder
5. Potansiyel kayıp tutarını hesaplar

### Kullanım

```php
use App\Services\Analytics\CommissionRiskAnalyzer;

$analyzer = new CommissionRiskAnalyzer();

// Sadece gerçek risk analizi (simülasyon olmadan)
$result = $analyzer->analyze(year: 2024, simulationPercentage: 0.30, useSimulation: false);

// Gerçek + Simülasyon (varsayılan)
$result = $analyzer->analyze(year: 2024, simulationPercentage: 0.30, useSimulation: true);

// Farklı simülasyon yüzdesi ile
$result = $analyzer->analyze(year: 2024, simulationPercentage: 0.50, useSimulation: true);
```

### Sonuç Formatı

```json
{
    "success": true,
    "year": 2024,
    "total_completed_sales": 150,
    "real_risk_sales_count": 10,
    "simulated_risk_sales_count": 45,
    "total_risk_sales_count": 55,
    "total_risk_amount": 275000.0,
    "real_risk_amount": 50000.0,
    "simulated_risk_amount": 225000.0,
    "average_risk_per_sale": 5000.0,
    "simulation": {
        "enabled": true,
        "percentage": 0.3,
        "simulated_count": 45,
        "simulated_amount": 225000.0
    },
    "risk_sales": [
        {
            "has_risk": true,
            "risk_amount": 5000.0,
            "risk_reason": "İlan danışmanı ile satış danışmanı farklı, ancak çift danışman komisyonu hesaplanmamış",
            "sale_id": 123,
            "sale_date": "2024-11-15",
            "sale_price": 1000000.0,
            "currency": "TRY",
            "current_commission": 30000.0,
            "current_commission_rate": 3.0,
            "current_danisman_id": 5,
            "current_danisman_name": "Ahmet Yılmaz",
            "ilan_id": 456,
            "ilan_danisman_id": 7,
            "ilan_danisman_name": "Mehmet Demir",
            "simulation": {
                "satici_danisman_id": 5,
                "satici_danisman_name": "Ahmet Yılmaz",
                "satici_commission_rate": 1.8,
                "satici_commission_amount": 18000.0,
                "alici_danisman_id": 7,
                "alici_danisman_name": "Mehmet Demir",
                "alici_commission_rate": 1.2,
                "alici_commission_amount": 12000.0,
                "total_dual_commission": 30000.0,
                "current_single_commission": 30000.0,
                "potential_loss": 12000.0,
                "split_ratio": "60-40"
            },
            "is_simulated": true,
            "simulated_buyer_danisman_id": 7,
            "simulated_buyer_danisman_name": "Mehmet Demir"
        }
    ],
    "summary": {
        "total_risk_sales": 55,
        "real_risk_sales": 10,
        "simulated_risk_sales": 45,
        "total_risk_amount": 275000.0,
        "real_risk_amount": 50000.0,
        "simulated_risk_amount": 225000.0,
        "average_risk_per_sale": 5000.0,
        "risk_by_reason": {
            "İlan danışmanı ile satış danışmanı farklı...": {
                "count": 10,
                "total_amount": 50000.0,
                "simulated_count": 0,
                "simulated_amount": 0
            },
            "Simülasyon: Satışların %30'unda farklı alıcı danışmanı olması gerektiği varsayıldı": {
                "count": 45,
                "total_amount": 225000.0,
                "simulated_count": 45,
                "simulated_amount": 225000.0
            }
        },
        "affected_danismans_count": 20,
        "recommendations": [
            "10 satışta gerçek komisyon eksikliği tespit edildi.",
            "45 satışta simüle edilmiş komisyon eksikliği tespit edildi (satışların %30'unda farklı alıcı danışmanı olduğu varsayıldı).",
            "Simüle edilmiş risk tutarı: 225,000.00 TL.",
            "Toplam risk tutarı çok yüksek (275,000.00 TL). Acil çift danışman komisyon sistemi kurulmalı.",
            "Satış kayıtlarına `satici_danisman_id` ve `alici_danisman_id` alanları eklenmeli.",
            "Gelecekteki satışlarda alıcı danışmanı bilgisi mutlaka kaydedilmeli."
        ]
    }
}
```

### Ne Zaman Kullanılır?

- Yıllık komisyon raporu hazırlarken
- Çift danışman komisyon sistemi kurmadan önce
- Potansiyel kayıpları tespit ederken

---

## 🎨 CSS İHLALLERİ TARAMASI

### Amaç

Proje içinde kullanımdan kaldırılmış Bootstrap ve Neo Design sınıflarını tespit eder.

### Nasıl Çalışır?

1. `resources/views/` ve `resources/js/` klasörlerini recursive tarar
2. Yasaklı pattern'leri arar:
    - `neo-*` (Neo Design System)
    - `btn-*` (Bootstrap buton)
    - `form-control` (Bootstrap form)
    - `card-*` (Bootstrap card)
3. Her dosyadaki ihlal sayısını hesaplar
4. Minimum ihlal sayısına göre filtreler

### Kullanım

```php
use App\Services\Analytics\CSSViolationScanner;

$scanner = new CSSViolationScanner();
$result = $scanner->scan(minViolations: 3);
```

### Sonuç Formatı

```json
{
    "success": true,
    "total_files_scanned": 450,
    "files_with_violations": 12,
    "total_violations": 45,
    "violations": [
        {
            "file": "/path/to/file.blade.php",
            "relative_path": "resources/views/admin/ilanlar/index.blade.php",
            "violations": [
                {
                    "pattern": "neo-",
                    "description": "Neo Design System sınıfları",
                    "match": "neo-btn",
                    "line_number": 45
                },
                {
                    "pattern": "btn-",
                    "description": "Bootstrap buton sınıfları",
                    "match": "btn-primary",
                    "line_number": 67
                }
            ],
            "violation_count": 5,
            "violation_types": {
                "neo-": 3,
                "btn-": 2
            }
        }
    ],
    "summary": {
        "total_files_with_violations": 12,
        "pattern_distribution": {
            "neo-": 20,
            "btn-": 15,
            "form-control": 8,
            "card-": 2
        },
        "file_type_distribution": {
            "blade.php": 8,
            "js": 3,
            "php": 1
        },
        "recommendations": [
            "12 dosyada CSS ihlali tespit edildi.",
            "20 Neo Design sınıfı kullanımı tespit edildi. Tailwind CSS'e geçiş yapılmalı.",
            "İhlal içeren dosyalar arşive taşınmadan önce temizlenmeli."
        ]
    }
}
```

### Ne Zaman Kullanılır?

- Proje temizliği yaparken
- Arşive taşımadan önce
- Yeni kod yazarken standart kontrolü için

---

## 🔌 API ENDPOINTS

### AI Karar Tutarsızlığı Analizi

```http
POST /api/analytics/ai-decision-inconsistency
Authorization: Bearer {token}
Content-Type: application/json

{
  "min_records": 5,
  "threshold": 2.0
}
```

### Belirli Request Data Analizi

```http
POST /api/analytics/ai-decision-by-request-data
Authorization: Bearer {token}
Content-Type: application/json

{
  "request_data_hash": "abc123...",
  "threshold": 2.0
}
```

### Komisyon Risk Analizi

```http
POST /api/analytics/commission-risk
Authorization: Bearer {token}
Content-Type: application/json

{
  "year": 2024,
  "simulation_percentage": 0.30,
  "use_simulation": true
}
```

**Parametreler:**

- `year` (opsiyonel): Analiz edilecek yıl (varsayılan: geçen yıl)
- `simulation_percentage` (opsiyonel): Simülasyon yüzdesi 0-1 arası (varsayılan: 0.30 = %30)
- `use_simulation` (opsiyonel): Simülasyon kullanılsın mı? (varsayılan: true)

### CSS İhlalleri Taraması

```http
POST /api/analytics/css-violations
Authorization: Bearer {token}
Content-Type: application/json

{
  "min_violations": 3
}
```

---

## 💻 KULLANIM ÖRNEKLERİ

### Örnek 1: AI Karar Tutarsızlığı Analizi (cURL)

```bash
curl -X POST https://api.example.com/api/analytics/ai-decision-inconsistency \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "min_records": 5,
    "threshold": 2.0
  }'
```

### Örnek 2: Komisyon Risk Analizi (PHP)

```php
use App\Services\Analytics\CommissionRiskAnalyzer;

$analyzer = new CommissionRiskAnalyzer();

// Gerçek + Simülasyon analizi
$result = $analyzer->analyze(year: 2024, simulationPercentage: 0.30, useSimulation: true);

if ($result['success']) {
    echo "Toplam Risk Tutarı: " . number_format($result['total_risk_amount'], 2) . " TL\n";
    echo "Gerçek Risk Tutarı: " . number_format($result['real_risk_amount'], 2) . " TL\n";
    echo "Simüle Edilmiş Risk Tutarı: " . number_format($result['simulated_risk_amount'], 2) . " TL\n";
    echo "Gerçek Riskli Satış Sayısı: " . $result['real_risk_sales_count'] . "\n";
    echo "Simüle Edilmiş Riskli Satış Sayısı: " . $result['simulated_risk_sales_count'] . "\n";
    echo "Toplam Riskli Satış Sayısı: " . $result['total_risk_sales_count'] . "\n";

    // Simülasyon bilgisi
    if ($result['simulation']['enabled']) {
        echo "Simülasyon: " . ($result['simulation']['percentage'] * 100) . "%\n";
        echo "Simüle Edilmiş Satış: " . $result['simulation']['simulated_count'] . "\n";
    }

    // Riskli satışları listele
    foreach ($result['risk_sales'] as $riskSale) {
        $type = ($riskSale['is_simulated'] ?? false) ? '[SIMÜLASYON]' : '[GERÇEK]';
        echo "{$type} Satış ID: {$riskSale['sale_id']}, Risk: {$riskSale['risk_amount']} TL\n";

        if (isset($riskSale['simulated_buyer_danisman_name'])) {
            echo "  → Simüle Edilmiş Alıcı Danışmanı: {$riskSale['simulated_buyer_danisman_name']}\n";
        }
    }
}
```

### Örnek 3: CSS İhlalleri Taraması (Artisan Command)

```php
// app/Console/Commands/ScanCSSViolations.php
use App\Services\Analytics\CSSViolationScanner;

public function handle()
{
    $scanner = new CSSViolationScanner();
    $result = $scanner->scan(minViolations: 3);

    $this->info("Toplam dosya: {$result['total_files_scanned']}");
    $this->info("İhlal içeren dosya: {$result['files_with_violations']}");
    $this->info("Toplam ihlal: {$result['total_violations']}");

    foreach ($result['violations'] as $violation) {
        $this->warn("{$violation['relative_path']}: {$violation['violation_count']} ihlal");
    }
}
```

---

## 📊 RAPORLAMA

Tüm analizler otomatik olarak `LogService::action()` ile loglanır:

- **AI Karar Tutarsızlığı**: `ai_decision_inconsistency_analysis`
- **Komisyon Risk**: `commission_risk_analysis`
- **CSS İhlalleri**: `css_violation_scan`

---

## ✅ CONTEXT7 UYUMLULUK

- ✅ `ResponseService` kullanımı
- ✅ `LogService::action()` ile loglama
- ✅ Standardize edilmiş hata mesajları
- ✅ Validation error handling
- ✅ Exception handling

---

## 🔒 GÜVENLİK

Tüm endpoint'ler `auth:sanctum` middleware'i ile korunur.

---

## 📝 CHANGELOG

### v1.0.0 (2025-11-25)

- ✅ AI Karar Tutarsızlığı Analizi
- ✅ Komisyon Eksikliği Risk Analizi
- ✅ CSS İhlalleri Taraması
- ✅ API endpoints
- ✅ Logging entegrasyonu

---

**Son Güncelleme:** 25 Kasım 2025  
**Yazar:** Yalıhan AI Development Team  
**Lisans:** Proprietary
