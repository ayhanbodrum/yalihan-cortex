# 🧠 Yalıhan Cortex - System Architecture Definition v2.1

**Tarih:** 2025-11-30  
**Versiyon:** 2.1.0  
**Durum:** ✅ Production Ready  
**Context7 Uyumluluk:** %100

---

## 📋 SİSTEM TANIMI

**Yalıhan Emlak OS**, Laravel 10 üzerinde çalışan, Context7 standartlarına uyumlu, **Olay Güdümlü (Event-Driven)** ve **AI destekli** bir emlak yönetim platformudur.

**Temel Prensip:** "Manuel Veri Girişi" devri bitti, **"AI Destekli Operasyon"** devri başladı.

---

## 🎯 1. SİNİR SİSTEMİ VE İZLEME (AI Command Center)

### Dashboard

**URL:** `/admin/ai/dashboard`  
**Controller:** `App\Http\Controllers\AI\AdvancedAIController`  
**Route Name:** `admin.ai.dashboard`

### Bileşenler

#### Health Check

**Teknoloji:** HTTP Ping (2 saniye timeout)

**Kontrol Edilen Servisler:**

1. **Cortex Brain (Laravel)**
    - Durum: Her zaman Online
    - URL: `config('app.url')`
    - Response Time: N/A

2. **LLM Engine (Ollama)**
    - Endpoint: `GET /api/tags`
    - URL: `env('OLLAMA_URL', 'http://ollama:11434')`
    - Durum: Online/Offline
    - Response Time: Milisaniye cinsinden

3. **Knowledge Base (AnythingLLM)**
    - Endpoint: `GET /api/system/health`
    - URL: `env('ANYTHINGLLM_URL', 'http://localhost:3001')`
    - Durum: Online/Offline/Not Configured
    - Response Time: Milisaniye cinsinden

**Görsel Gösterim:**

- Yeşil pulse noktası: Online
- Kırmızı pulse noktası: Offline
- Sarı pulse noktası: Not Configured

#### Opportunity Stream

**Kaynak:** `ai_logs` tablosu

**Filtreleme:**

- `request_type` LIKE '%SmartPropertyMatcherAI%'
- `created_at` >= Son 24 saat
- `status` = 'success'
- Skor >= 80

**Gösterim:**

- Timeline formatında
- Skor 90+ olanlar "⚠️ ACİL" badge'i ile işaretlenir
- Her satırda:
    - İlan/Talep başlığı
    - Skor değeri
    - Zaman (diffForHumans)
    - "Detay Gör" butonu
    - "Danışmana Ata" butonu

**Örnek:**

```
⏰ 10 dk önce: Ahmet Yılmaz için 'Deniz Manzaralı Villa' bulundu. (Skor: 92) - ⚠️ ACİL
```

#### Analytics

**Metrikler:**

1. **İmar Analizi**
    - `request_type` LIKE '%imar%' OR '%analyze-construction%'
    - Bugünkü başarılı istek sayısı

2. **İlan Açıklaması**
    - `request_type` LIKE '%description%' OR '%aciklama%'
    - Bugünkü başarılı istek sayısı

3. **Fiyat Hesaplama**
    - `request_type` LIKE '%price%' OR '%fiyat%' OR '%pricing%'
    - Bugünkü başarılı istek sayısı

4. **Token Kullanımı**
    - `tokens_used` SUM
    - Format: "X.XXM" (Milyon)

5. **Başarı Oranı**
    - `success_count / total_requests * 100`
    - Yüzde formatında

---

## 🏗️ 2. ARSA MODÜLÜ: MÜHENDİS ZEKASI (RAG)

### Teknoloji

**RAG (Retrieval-Augmented Generation)**

- **Vector DB:** AnythingLLM
- **LLM:** Ollama (Local) veya OpenAI (via AnythingLLM)

### Servis

**Dosya:** `app/Services/CortexKnowledgeService.php`

**Namespace:** `App\Services`

**Metod:** `queryConstructionRights(array $data): array`

### İşlev

**Input:**

```php
[
    'ilce' => 'Bodrum',
    'mahalle' => 'Yalıkavak',
    'ada_no' => '123',
    'parsel_no' => '456',
    'alan_m2' => 1500.50,
]
```

**System Prompt:**

```
Sen Yalıhan Emlak'ın Kıdemli Şehir Plancısısın. Verilen lokasyon ve parsel bilgilerini, veritabanındaki 'İmar Plan Notları' dokümanlarıyla karşılaştır. Bu arsa için KAKS (Emsal), TAKS, Gabari (Yükseklik) ve Çekme Mesafelerini tespit et. Toplam inşaat alanını hesapla. Kaynak dokümanı belirt.
```

**Output:**

```php
[
    'success' => true,
    'data' => [
        'kaks' => '0.50',
        'taks' => '0.30',
        'gabari' => '12.5m',
        'cekme_mesafeleri' => '5m ön, 3m yan',
        'toplam_insaat_alani' => '750 m²',
        'kaynak_dokuman' => 'Bodrum İmar Planı 2024',
        'raw_response' => '...',
    ],
    'source' => 'AnythingLLM - yalihan-hukuk',
]
```

### UI Entegrasyonu

**Dosya:** `resources/views/admin/ilanlar/components/field-dependencies-dynamic.blade.php`

**Koşul:** `x-if="formData.category_slug === 'arsa'"`

**Özellikler:**

- Sadece Arsa kategorisi seçildiğinde görünür
- "Cortex İmar & İnşaat Analizi" kartı
- "Analizi Başlat" butonu
- Loading state: "Plan notları okunuyor..."
- Sonuç gösterimi: KAKS, TAKS, Gabari, Çekme Mesafeleri, Toplam İnşaat Alanı

---

## 🏖️ 3. YAZLIK MODÜLÜ: MUHASEBECİ ZEKASI (Auto-Pricing)

### Teknoloji

**Config-Based Algorithmic Calculation**

### Yapılandırma

**Dosya:** `config/yali_options.php`

```php
'pricing_rules' => [
    'discounts' => [
        'weekly' => 0.05,  // %5 İndirim
        'monthly' => 0.15, // %15 İndirim
    ],
    'seasonal_multipliers' => [
        'yaz' => 1.00,      // %100 (Baz Fiyat)
        'ara_sezon' => 0.70, // %70
        'kis' => 0.50,      // %50
    ],
],
'sezon_tipleri' => [
    'yaz' => ['label' => 'Yaz Sezonu (Haziran-Ağustos)', 'color' => 'yellow', 'icon' => '☀️'],
    'ara_sezon' => ['label' => 'Ara Sezon (Eylül-Ekim / Nisan-Mayıs)', 'color' => 'orange', 'icon' => '🍂'],
    'kis' => ['label' => 'Kış Sezonu (Kasım-Mart)', 'color' => 'blue', 'icon' => '❄️'],
],
```

### İşlev

**API Endpoint:** `POST /api/ai/calculate-seasonal-price`

**Controller:** `App\Http\Controllers\Api\IlanAIController@calculateSeasonalPrice`

**Input:**

```json
{
    "gunluk_fiyat": 10000,
    "currency": "TRY"
}
```

**Hesaplama Formülleri:**

- **Haftalık:** `günlük × 7 × (1 - 0.05) = günlük × 6.65`
- **Aylık:** `günlük × 30 × (1 - 0.15) = günlük × 25.5`
- **Kış Günlük:** `günlük × 0.50`
- **Ara Sezon Günlük:** `günlük × 0.70`

**Output:**

```json
{
    "success": true,
    "data": {
        "gunluk_fiyat": 10000,
        "haftalik_fiyat": 66500,
        "aylik_fiyat": 255000,
        "seasonal_prices": {
            "yaz": {
                "daily_price": 10000,
                "weekly_price": 66500,
                "monthly_price": 255000
            },
            "ara_sezon": {
                "daily_price": 7000,
                "weekly_price": 46550,
                "monthly_price": 178500
            },
            "kis": {
                "daily_price": 5000,
                "weekly_price": 33250,
                "monthly_price": 127500
            }
        }
    }
}
```

### UI Entegrasyonu

**Dosya:** `resources/views/admin/ilanlar/components/category-fields/kiralik-fields.blade.php`

**Özellikler:**

- Sadece Yazlık kategorisi seçildiğinde görünür
- Günlük fiyat input'una "⚡ Otomatik Hesapla" butonu
- Loading state ve flash effect
- Otomatik form doldurma (haftalik_fiyat, aylik_fiyat)
- Sezonluk fiyat önerileri kartları

---

## 🏠 4. KONUT MODÜLÜ: DENETMEN ZEKASI (Smart Validation)

### Teknoloji

**Client-Side & Server-Side Validation Logic**

### Servis

**Dosya:** `app/Services/CategoryFieldValidator.php`

**Metodlar:**

- `getKonutRules()` - Validation kuralları
- `validateKonut()` - Custom validation

### İşlevler

#### Mantık Kontrolü

**Kural:** "Net m² > Brüt m²" fiziksel olarak imkansızdır.

**Validasyon:**

```php
'features.net_m2' => [
    'required',
    'numeric',
    'min:10',
    function ($attribute, $value, $fail) {
        $brutM2 = request('features.brut_m2');
        if ($brutM2 !== null && $value > $brutM2) {
            $fail('Net metrekare, Brüt metrekareden büyük olamaz!');
        }
    },
],
```

**UI Feedback:**

- Input çerçevesi kırmızı olur
- Altında uyarı mesajı gösterilir
- Form kaydedilemez

#### Görsel Algı

**Config:** `config/yali_options.php`

```php
'oda_sayisi_options' => [
    ['value' => '1+0', 'label' => '1+0 (Stüdyo)', 'color' => 'text-blue-600 bg-blue-50', 'icon' => '🏠'],
    ['value' => '1+1', 'label' => '1+1', 'color' => 'text-blue-700 bg-blue-100', 'icon' => '👥'],
    ['value' => '2+1', 'label' => '2+1', 'color' => 'text-green-600 bg-green-50', 'icon' => '👨‍👩‍👧'],
    ['value' => '3+1', 'label' => '3+1', 'color' => 'text-orange-600 bg-orange-50', 'icon' => '👨‍👩‍👧‍👦'],
    ['value' => '4+1', 'label' => '4+1', 'color' => 'text-purple-600 bg-purple-50', 'icon' => '🏰'],
    // ...
],
```

**UI Entegrasyonu:**

- Select option'ları renklendirilir
- Seçildiğinde select input'u o renge bürünür
- Icon'lar gösterilir

#### Piyasa Analizi

**API Endpoint:** `POST /api/ai/calculate-konut-metrics`

**Controller:** `App\Http\Controllers\Api\IlanAIController@calculateKonutMetrics`

**Input:**

```json
{
    "satis_fiyati": 3500000,
    "brut_m2": 100
}
```

**Hesaplama:**

```php
$m2BirimFiyat = $satisFiyati / $brutM2; // 35000 TL/m²
```

**Output:**

```json
{
    "success": true,
    "data": {
        "m2_birim_fiyat": 35000,
        "formatted": "35.000 TL/m²",
        "piyasa_analizi": "Piyasa ortalamasına uygun."
    }
}
```

**UI Entegrasyonu:**

- Fiyat ve brüt m² girildiğinde otomatik hesaplama
- Input'un sağ altında badge: "Birim: 35.000 TL/m²"
- JS ile anlık hesaplama (backend sadece teyit eder)

---

## 🔧 5. TEKNİK ALTYAPI VE STANDARTLAR

### Backend

- **Framework:** Laravel 10
- **PHP:** 8.2+
- **Database:** MySQL
- **Strict Types:** `declare(strict_types=1);` zorunlu

### Frontend

- **Templating:** Blade Components
- **Reaktivite:** Alpine.js
- **Styling:** Tailwind CSS
- **Dark Mode:** Tüm elementlerde desteklenir

### AI Stack

- **Local LLM:** Ollama (http://ollama:11434)
- **Vector DB Manager:** AnythingLLM (http://localhost:3001)
- **RAG:** CortexKnowledgeService
- **Logging:** `ai_logs` tablosu

### Veri Yapısı

**ai_logs Tablosu:**

```sql
- id
- provider (ollama, openai, gemini, etc.)
- request_type (analyze-construction, calculate-seasonal-price, etc.)
- status (success, failed, error)
- tokens_used
- response_time
- request_data (JSON)
- response_data (JSON)
- created_at
- updated_at
```

### Context7 Kuralları

1. **Database Fields:**
    - `status` kullanımı (NOT `durum`, `aktif`, `is_active`)
    - İngilizce kolon isimleri zorunlu

2. **Code Standards:**
    - `declare(strict_types=1);` zorunlu
    - ResponseService kullanımı
    - Comprehensive error handling
    - Logging sistemi

3. **UI Standards:**
    - Tailwind CSS utility classes
    - Dark mode variants
    - Transitions ve animations
    - Accessibility (ARIA labels)

---

## 🚀 SONRAKİ HEDEF

**Telegram Bot Entegrasyonu**

Dashboard'daki "Acil Fırsatları" (Skor 90+) doğrudan yöneticinin cebine bildirim olarak göndermek.

**Plan:**

1. Telegram Bot API entegrasyonu
2. Webhook endpoint oluşturma
3. Opportunity Stream'den acil fırsatları filtreleme
4. Real-time bildirim gönderme

---

## 📚 İLGİLİ DOKÜMANTASYON

- **Yalıhan Bekçi Knowledge:** `.yalihan-bekci/knowledge/yazlik-konut-ai-automation-2025-11-30.md`
- **Gemini Architecture:** `docs/ai/GEMINI_NEW_ARCHITECTURE_V2.1.md`
- **Context7 Standards:** `.context7/authority.json`

---

**Son Güncelleme:** 2025-11-30  
**Versiyon:** 2.1.0  
**Durum:** ✅ Production Ready  
**Context7 Compliance:** %100


