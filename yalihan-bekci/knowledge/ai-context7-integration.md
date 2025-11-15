# 🤖 AI Sistem - Context7 Entegrasyon Rehberi

**Version:** 3.4.0  
**Date:** 2025-10-12  
**Status:** ✅ Production Ready  
**Compliance:** Context7 %100

---

## 🎯 AI SİSTEM ÖZETİ

### **5 AI Provider**

1. ✅ **AnythingLLM** (Local AI Server)
2. ✅ **OpenAI GPT** (Cloud API)
3. ✅ **Google Gemini** (Cloud API - Vision/OCR)
4. ✅ **Anthropic Claude** (Cloud API - Code Review)
5. ✅ **Ollama** (Local AI - Turkish Support) ⭐ Recommended

---

## 📍 AI Ayarları Sayfası

**URL:** `/admin/ai-settings`  
**Route:** `admin.ai-settings.index`

### **Özellikler**

- ✅ 5 AI Provider konfigürasyonu
- ✅ Test butonu her provider'da
- ✅ Gerçek zamanlı durum göstergesi (Yeşil ✅ / Kırmızı ❌)
- ✅ Otomatik loglama (`storage/logs/ai_connections.log`)
- ✅ Toast bildirimleri
- ✅ 30 saniye otomatik yenileme

---

## ✅ CONTEXT7 UYUMLULUK KURALLARI

### **1. Field Naming (Alan İsimlendirme)**

**✅ DOĞRU:**

```php
status       // ✅ (NOT durum, is_active, aktif)
active       // ✅ (Boolean için)
enabled      // ✅ (Feature toggle için)
il_id        // ✅ (NOT sehir_id, city_id, region_id)
ilce_id      // ✅ (NOT district_id)
mahalle_id   // ✅ (NOT neighborhood_id)
para_birimi  // ✅ (NOT currency)
ai_*         // ✅ (Tüm AI config key'leri)
```

**❌ YASAK:**

```php
durum         // ❌ → status kullan
is_active     // ❌ → status kullan
aktif         // ❌ → active kullan
sehir         // ❌ → il kullan
sehir_id      // ❌ → il_id kullan
currency      // ❌ → para_birimi kullan
```

### **2. API Response Format**

**✅ Standart Format:**

```json
{
    "success": true,
    "data": {},
    "metadata": {
        "model": "gemma2:2b",
        "response_time": 2150,
        "confidence_score": 0.91
    },
    "context7_compliant": true
}
```

**❌ Hata Format:**

```json
{
    "success": false,
    "error": "Hata mesajı",
    "details": {},
    "context7_compliant": true
}
```

### **3. Model İlişkileri**

**✅ DOĞRU:**

```php
// İlan ilişkileri
$ilan->il         // ✅ İl ilişkisi
$ilan->ilce       // ✅ İlçe ilişkisi
$ilan->mahalle    // ✅ Mahalle ilişkisi

// Kategori ilişkileri
$ilan->anaKategori    // ✅ Ana kategori
$ilan->altKategori    // ✅ Alt kategori
$ilan->yayinTipi      // ✅ Yayın tipi
```

**❌ YASAK:**

```php
$ilan->sehir      // ❌ → il kullan
$ilan->bolge      // ❌ Kaldırıldı
$ilan->region     // ❌ Kaldırıldı
```

---

## 🔧 AI API ENDPOINT'LERİ

### **1. AI Settings Management**

```
GET  /admin/ai-settings               → Ana sayfa
PUT  /admin/ai-settings               → Ayarları güncelle
POST /admin/ai-settings/test-provider → Provider test et
GET  /admin/ai-settings/provider-status → Durum bilgisi
```

### **2. Content Generation**

```
POST /stable-create/ai-suggest
Parameters:
  - action: title|description|location|price|all
  - kategori, lokasyon, fiyat, ai_tone
```

### **3. Category API**

```
GET /api/categories/sub/{id}              → Alt kategoriler
GET /api/categories/publication-types/{id} → Yayın tipleri
```

### **4. Location API**

```
GET /api/location/districts/{il_id}       → İlçeler
GET /api/location/neighborhoods/{ilce_id} → Mahalleler
```

---

## 📊 DATABASE SCHEMA

### **Settings Table (AI Config)**

```sql
key: 'ai_anythingllm_url'        → value: 'http://localhost:3001'
key: 'ai_anythingllm_api_key'    → value: 'xxx'
key: 'ai_openai_api_key'         → value: 'sk-xxx'
key: 'ai_gemini_api_key'         → value: 'AIzxxx'
key: 'ai_claude_api_key'         → value: 'sk-ant-xxx'
key: 'ai_ollama_url'             → value: 'http://51.75.64.121:11434'
key: 'ai_ollama_model'           → value: 'gemma2:2b'
```

### **AI Chat Logs**

```sql
ai_chat_logs
├── id
├── user_id
├── prompt (user question)
├── response (AI answer)
├── provider (ollama, openai, gemini, claude)
├── model (gemma2:2b, gpt-4, etc.)
├── tokens_used
├── response_time (ms)
└── created_at
```

---

## 🎨 AI FEATURES

### **1. Title Generation (Başlık Üretimi)**

- **Variants:** 3
- **Length:** 60-80 characters
- **Tones:** seo, kurumsal, hizli_satis, luks
- **Response Time:** <2s
- **Cache:** 1 hour TTL

### **2. Description Generation (Açıklama Üretimi)**

- **Word Count:** 200-250
- **Paragraphs:** 3
- **Tones:** seo, kurumsal, hizli_satis, luks
- **Response Time:** <3s
- **Cache:** 1 hour TTL

### **3. Location Analysis (Lokasyon Analizi)**

- **Score:** 0-100
- **Grade:** A, B, C, D
- **Potential:** Yüksek, Orta, Düşük
- **Response Time:** <2s
- **Cache:** 24 hours TTL

### **4. Price Suggestion (Fiyat Önerisi)**

- **Levels:** 3 (Pazarlık, Piyasa, Premium)
- **Calculation:** Base price ± percentage
- **Response Time:** <1s
- **Cache:** None (real-time)

---

## 🔒 SECURITY & COMPLIANCE

### **Context7 Güvenlik Kuralları**

```yaml
CSRF Protection: ✅ Required
Rate Limiting: ✅ 10 requests/minute/user
Input Validation: ✅ All inputs sanitized
PII Masking: ✅ Phone/email masked
Auto-Save: ❌ Human approval required
API Keys: ✅ Stored in database (encrypted)
```

### **Error Handling**

```php
try {
    // AI provider call
} catch (\Exception $e) {
    Log::warning('AI Provider failed', ['error' => $e->getMessage()]);
    return $this->fallbackResponse();
}
```

---

## 📝 LOGGING SYSTEM

### **AI Connection Log**

**File:** `storage/logs/ai_connections.log`

**Format:**

```
[2025-10-12 18:54:32] GEMINI - SUCCESS ✅ | Response: 456ms | Details: {"status":200,"models":15}
[2025-10-12 18:54:35] OPENAI - FAILED ❌ | Response: 5234ms | Details: {"error":"Connection timeout"}
```

### **Laravel Log**

**Channel:** `ai`  
**Level:** `info`  
**File:** `storage/logs/laravel.log`

---

## 🎯 YALIHAN BEKÇİ PATTERN'LERİ

### **Successful Pattern #1: Provider Testing**

```
Backend Test Method → AJAX Call → Visual Feedback → Auto Logging
✅ Implemented: 2025-10-12
✅ Files: AISettingsController.php, ai-settings-test.js
```

### **Successful Pattern #2: Real-time Status**

```
Cache Provider Status → Auto Refresh (30s) → Update Badges → Toast Notification
✅ Implemented: 2025-10-12
✅ Colors: Green (active), Red (failed), Blue (testing), Gray (not tested)
```

### **Successful Pattern #3: Dual Logging**

```
Laravel Log (structured) + Dedicated Log (ai_connections.log)
✅ Format: [timestamp] PROVIDER - STATUS | Response: Xms | Details: {...}
```

---

## 🚀 PRODUCTION CHECKLIST

### **Backend**

- ✅ AISettingsController implemented
- ✅ Test methods for all 5 providers
- ✅ Logging system active
- ✅ Cache integration
- ✅ Error handling
- ✅ Fallback mechanism

### **Frontend**

- ✅ AI Settings page (/admin/ai-settings)
- ✅ Test buttons (5 providers)
- ✅ Status badges (color-coded)
- ✅ Toast notifications
- ✅ Auto-refresh (30s)
- ✅ Dark mode support

### **Context7**

- ✅ Field names: English only
- ✅ API format: Standard
- ✅ No forbidden patterns
- ✅ Compliance: 100%

---

## 📖 DOCUMENTATION REFERENCES

### **Main Docs**

```
docs/ai-training/          → AnythingLLM training package
docs/context7/             → Context7 compliance reports
docs/context7/reports/ai-sistem-gelisme-2025-10-12.md → Latest AI development
```

### **Config Files**

```
config/ai.php              → AI system configuration
yalihan-bekci/knowledge/   → Bekçi knowledge base
```

---

**🎓 Bu doküman Yalıhan Bekçi'nin AI sistemini %100 anlaması için hazırlanmıştır.**  
**Context7 Compliant:** ✅ 100%  
**Last Updated:** 2025-10-12
