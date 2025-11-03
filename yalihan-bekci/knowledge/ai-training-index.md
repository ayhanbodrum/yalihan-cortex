# 🎓 AI Training Documentation Index - Yalıhan Bekçi

**Version:** 3.4.0  
**Last Updated:** 2025-10-12  
**Context7 Compliant:** ✅ 100%

---

## 📚 AI Training Dokümanları

### **Core Training (AnythingLLM'e Embed Edilecek)**

1. **00-ANYTHINGLLM-MASTER-TRAINING.md**
   - Sistem kimliği ve genel bakış
   - AI provider detayları (Ollama, OpenAI, Gemini, Claude)
   - Mimari yapı
   - Güvenlik kuralları
   - Location: `docs/ai-training/`

2. **01-AI-FEATURES-GUIDE.md**
   - Başlık/açıklama/lokasyon/fiyat özellikleri
   - 4 ton profili (SEO, Kurumsal, Hızlı Satış, Lüks)
   - Cache stratejileri
   - RAG (Retrieval-Augmented Generation)

3. **02-CONTEXT7-RULES-SIMPLIFIED.md** ⭐ **KRİTİK**
   - Zorunlu alan adları
   - Yasaklar listesi
   - Neo Design System kuralları
   - Lokasyon hiyerarşisi

4. **03-DATABASE-SCHEMA-FOR-AI.md**
   - 6 ana tablo yapısı
   - İlişkiler ve foreign key'ler
   - Örnek SQL sorguları
   - Field açıklamaları

5. **04-PROMPT-TEMPLATES.md**
   - 15+ prompt şablonu
   - Kategori özel prompt'lar
   - Ton bazlı varyantlar
   - System prompt

6. **05-USE-CASES-AND-SCENARIOS.md**
   - 8 gerçek kullanım senaryosu
   - Dialog örnekleri
   - Edge case çözümleri
   - Performans örnekleri

7. **06-API-REFERENCE.md**
   - AI endpoint'leri
   - Request/Response formatları
   - Error handling
   - Authentication

8. **07-EMBEDDING-GUIDE.md**
   - AnythingLLM kurulum adımları
   - System prompt (kopyala-yapıştır)
   - Test senaryoları
   - Başarı kontrol listesi

9. **08-TRAINING-CHECKLIST.md**
   - Kurulum öncesi gereksinimler
   - Upload adımları
   - Test senaryoları
   - Final checklist

10. **09-OLLAMA-INTEGRATION.md**
    - Ollama server detayları
    - gemma2:2b model özellikleri
    - Optimal parametreler
    - Performance optimization

11. **10-REAL-WORLD-EXAMPLES.md**
    - Gerçek ilan örnekleri
    - A/B test sonuçları
    - Case studies
    - Edge case çözümleri

---

## 🎯 Context7 AI Standartları

### **Field Naming Rules**
```json
{
  "correct": ["status", "active", "enabled", "il_id", "ai_*"],
  "forbidden": ["durum", "is_active", "aktif", "sehir", "sehir_id"]
}
```

### **API Response Format**
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

### **AI Provider Config Keys**
```
ai_anythingllm_url
ai_anythingllm_api_key
ai_openai_api_key
ai_gemini_api_key
ai_claude_api_key
ai_ollama_url
ai_ollama_model
```

---

## 🤖 AI Provider Details

### **AnythingLLM (Local AI Server)**
- **Endpoint:** http://localhost:3001
- **Features:** Embedding, Chat, Document Processing
- **Type:** Local
- **Status:** ✅ Active

### **OpenAI GPT (Cloud API)**
- **Endpoint:** https://api.openai.com/v1
- **Models:** gpt-4, gpt-4-turbo, gpt-3.5-turbo
- **Features:** Chat, Completion, Embedding
- **Use Cases:** Complex content, multilingual

### **Google Gemini (Cloud API)**
- **Endpoint:** https://generativelanguage.googleapis.com/v1beta
- **Models:** gemini-2.5-flash, gemini-pro
- **Features:** Vision, OCR, Image Analysis, Chat
- **Use Cases:** Image analysis, OCR, Visual QA

### **Anthropic Claude (Cloud API)**
- **Endpoint:** https://api.anthropic.com/v1
- **Models:** claude-3, claude-3-opus, claude-3-sonnet
- **Features:** Code Review, Quality Control, Long Context
- **Use Cases:** Code review, QA, Technical writing

### **Ollama (Local AI Server)**
- **Endpoint:** http://51.75.64.121:11434
- **Model:** gemma2:2b
- **Features:** Local Inference, Turkish Support, Free
- **Use Cases:** Title/description generation, price suggestions
- **Status:** ✅ Recommended

---

## 📍 AI Settings Page

**URL:** http://localhost:8000/admin/ai-settings  
**Route:** `admin.ai-settings.index`  
**Controller:** `App\Http\Controllers\Admin\AISettingsController`  
**View:** `resources/views/admin/ai-settings/index.blade.php`

### **Features**
- ✅ Provider configuration (5 providers)
- ✅ Test buttons for each provider
- ✅ Real-time status badges (Green ✅ / Red ❌)
- ✅ Automatic logging (ai_connections.log)
- ✅ Toast notifications
- ✅ Auto-refresh (30s interval)

---

## 📊 AI System Architecture

### **Backend Services**
```
app/Services/
├── OllamaService.php (Local AI - gemma2:2b)
├── OpenAIService.php (GPT integration)
├── GeminiService.php (Vision/OCR)
├── IlanGecmisAIService.php (History analysis)
├── TKGMService.php (Land registry integration)
└── KategoriOzellikService.php (Category features)
```

### **Database Tables**
```
ai_chat_logs → AI request/response logging
ai_knowledge_base → Learned knowledge storage
ai_embeddings → Vector embeddings for RAG
settings → AI provider config (key-value)
```

### **Frontend Assets**
```
public/js/ai-settings-test.js → Provider testing logic
resources/views/admin/ai-settings/index.blade.php → Settings UI
```

---

## 🔍 Yalıhan Bekçi Integration

### **Knowledge Files**
- ✅ `ai-system-master.json` → Main AI system info
- ✅ `ai-api-endpoints.json` → All AI endpoints
- ✅ `ai-training-index.md` → Training docs index
- ✅ `context7-llms-config.json` → Updated with AI providers

### **MCP Server Access**
```bash
# AI sistem bilgilerini sorgula
curl http://localhost:3100/ai-system-info

# AI endpoint'leri listele
curl http://localhost:3100/ai-endpoints

# Context7 AI kurallarını getir
curl http://localhost:3100/context7-ai-rules
```

---

## 🎯 AI Usage Patterns

### **Title Generation**
```javascript
POST /stable-create/ai-suggest
{
  "action": "title",
  "kategori": "Villa",
  "lokasyon": "Yalıkavak",
  "fiyat": 3500000,
  "ai_tone": "seo"
}
```

### **Provider Testing**
```javascript
POST /admin/ai-settings/test-provider
{
  "provider": "gemini"
}

Response:
{
  "success": true,
  "provider": "gemini",
  "message": "Gemini bağlantısı başarılı",
  "response_time": 456,
  "details": {"status": 200, "models": 15}
}
```

---

## ✅ Context7 Compliance Checklist

- ✅ All AI field names use English (ai_*, status, active)
- ✅ No Turkish field names (durum, aktif, sehir) ❌
- ✅ API responses follow standard format
- ✅ Logging includes timestamp, provider, status
- ✅ Error handling implemented
- ✅ Rate limiting applied (10 req/min)
- ✅ CSRF protection active
- ✅ PII masking enabled

---

**📚 Yalıhan Bekçi şimdi tüm AI sistem bilgilerini biliyor!**  
**Status:** ✅ Knowledge Base Updated  
**Date:** 2025-10-12  
**Context7:** 100% Compliant

