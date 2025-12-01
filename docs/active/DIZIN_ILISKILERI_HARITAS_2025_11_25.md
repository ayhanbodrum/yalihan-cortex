# Dizin İlişkileri Haritası (2025-11-25)

## 📊 Sistem Mimarisi Özeti

| Katman         | Dosya Sayısı | Amaç                                 | Konsolide Dosya                      |
| -------------- | ------------ | ------------------------------------ | ------------------------------------ |
| AI/ML          | 10           | Sağlayıcılar, modeller, kullanım     | AI_KONSOLIDE_2025_11_25.md           |
| API/Routes     | 15+          | REST/GraphQL endpoint'ler            | TECHNICAL_KONSOLIDE_2025_11_25.md    |
| Context7       | 8            | Compliance, kurallar, doğrulama      | CONTEXT7_KONSOLIDE_2025_11_25.md     |
| Özellikler     | 6            | Harita, emlak türü, yazlık           | FEATURES_KONSOLIDE_2025_11_25.md     |
| Entegrasyonlar | 10+          | N8N, Maps, TCMB, TKGM, MCP           | INTEGRATIONS_KONSOLIDE_2025_11_25.md |
| İlan Sistemi   | 8            | İlan yönetimi, gösterim, işleme      | ILAN_SISTEMI_KONSOLIDE_2025_11_25.md |
| Modüller       | 9            | Admin, Auth, Emlak, Talep vb.        | MODULES_KONSOLIDE_2025_11_25.md      |
| Kurallar       | 7            | Adlandırma, pattern'ler, standartlar | RULES_KONSOLIDE_2025_11_25.md        |
| Teknik         | 27           | Mimari, script'ler, optimizasyon     | TECHNICAL_KONSOLIDE_2025_11_25.md    |
| Kullanım       | 12           | Örnekler, API test, workflow         | USAGE_KONSOLIDE_2025_11_25.md        |
| Roadmap        | 5            | Planlama, sonraki adımlar            | ROADMAP_KONSOLIDE_2025_11_25.md      |

**Toplam: 4,901 satır, 10 konsolide dosya**

---

## 🔗 Dizin Haritası (docs/ İçindeki Yapı)

### 📁 active/ (23 dosya - KONSOLIDE MERKEZ)

```
✅ AI_KONSOLIDE_2025_11_25.md (500 satır)
   ├─ Kaynak: ai-training/*, ai/*, prompts/*
   ├─ İçerik: OpenAI, DeepSeek, Gemini, Claude, Ollama entegrasyonları
   ├─ İlişkiler: TECHNICAL (API), INTEGRATIONS (MCP), USAGE (örnekler)
   └─ Tarih: 25 Kasım 2025

✅ CONTEXT7_KONSOLIDE_2025_11_25.md (388 satır)
   ├─ Kaynak: context7-rules.md, context7-master.md, .warp/rules/
   ├─ İçerik: Dual system (Upstash + Yalıhan Bekçi), compliance
   ├─ İlişkiler: RULES, TECHNICAL, MODULES
   └─ Tarih: 25 Kasım 2025

✅ FEATURES_KONSOLIDE_2025_11_25.md (423 satır)
   ├─ Kaynak: features/harita.md, property-types.md, yazlık.md
   ├─ İçerik: Harita sistemi, emlak kategorileri, yazlık kiraları
   ├─ İlişkiler: TECHNICAL (Tailwind), INTEGRATIONS (Maps API)
   └─ Tarih: 25 Kasım 2025

✅ ILAN_SISTEMI_KONSOLIDE_2025_11_25.md (238 satır)
   ├─ Kaynak: features/ilan-*.md
   ├─ İçerik: İlan oluşturma, düzenleme, gösterim, taslak
   ├─ İlişkiler: MODULES (Emlak), TECHNICAL (database), INTEGRATIONS (N8N)
   └─ Tarih: 25 Kasım 2025

✅ INTEGRATIONS_KONSOLIDE_2025_11_25.md (993 satır - EN KAPSAMLI)
   ├─ Kaynak: integrations/*, submaps/*, tkgm/*, mcp-servers/
   ├─ İçerik:
   │   ├─ N8N workflows
   │   ├─ Maps API (Google, OpenStreetMap)
   │   ├─ TCMB döviz kuru
   │   ├─ TKGM kadastro
   │   ├─ Context7 library docs
   │   ├─ MCP servers (Yalıhan Bekçi, Upstash)
   │   └─ AI provider entegrasyonları
   ├─ İlişkiler: TECHNICAL (API), AI, USAGE (örnekler)
   └─ Tarih: 25 Kasım 2025

✅ MODULES_KONSOLIDE_2025_11_25.md (365 satır)
   ├─ Kaynak: app/Modules/* yapısı dokumentasyonu
   ├─ İçerik:
   │   ├─ Admin module
   │   ├─ Auth/Yetkilendirme
   │   ├─ Emlak operations
   │   ├─ Talep/Teklif sistemi
   │   ├─ Arsa modülü
   │   ├─ Analytics/Raporlama
   │   ├─ CRM Satış
   │   ├─ Finans
   │   └─ Takım yönetimi
   ├─ İlişkiler: TECHNICAL, RULES, CONTEXT7
   └─ Tarih: 25 Kasım 2025

✅ ROADMAP_KONSOLIDE_2025_11_25.md (391 satır)
   ├─ Kaynak: roadmap/*, plaanlama dosyaları
   ├─ İçerik: Kısa/orta/uzun vadeli planlar, milestone'lar
   ├─ İlişkiler: MODULES, INTEGRATIONS, AI
   └─ Tarih: 25 Kasım 2025

✅ RULES_KONSOLIDE_2025_11_25.md (544 satır)
   ├─ Kaynak: docs/rules/, adlandırma standartları
   ├─ İçerik:
   │   ├─ Adlandırma kuralları (il_id, status, oncelik)
   │   ├─ Eloquent pattern'leri
   │   ├─ UI/Blade kuralları
   │   ├─ Güvenlik standartları
   │   └─ Performance best practices
   ├─ İlişkiler: CONTEXT7, TECHNICAL, MODULES
   └─ Tarih: 25 Kasım 2025

✅ TECHNICAL_KONSOLIDE_2025_11_25.md (581 satır - EN DETAYLI TEKNİK)
   ├─ Kaynak: technical/*, api/*, database/*, performance/*, system/
   ├─ İçerik:
   │   ├─ Proje mimarisi (Laravel 10 + modüler)
   │   ├─ Context7 dual system
   │   ├─ Script'ler (MCP servers, backup, migrate)
   │   ├─ API mimarisi (Sanctum, routes gruplaması)
   │   ├─ Database schema + indexing
   │   ├─ Tailwind CSS migration
   │   ├─ React Select integration
   │   └─ Performance optimization
   ├─ İlişkiler: MODULES, RULES, INTEGRATIONS, AI
   └─ Tarih: 25 Kasım 2025

✅ USAGE_KONSOLIDE_2025_11_25.md (478 satır)
   ├─ Kaynak: usage examples, API test scripts, workflow documentation
   ├─ İçerik:
   │   ├─ AI usage örnekleri (property description, lead scoring)
   │   ├─ API endpoint kullanımı
   │   ├─ Workflow trigger'ları
   │   ├─ Context7 commands
   │   └─ CLI utility'ler
   ├─ İlişkiler: AI, INTEGRATIONS, TECHNICAL, MODULES
   └─ Tarih: 25 Kasım 2025
```

---

### 📁 archive/ (37 dosya - TARİHSEL)

```
📦 Orijinal kaynaklar (tüm tarihler 2025-11-25 olarak standartlaştırıldı)
├─ Context7 kuralları
├─ İntegrasyon talimatları
├─ API dokümantasyonu
├─ Modül rehberleri
└─ Teknik referanslar
```

### 📁 ai-training/ (24 dosya - ÖĞRETİM MODÜLLERİ)

```
🤖 AI sistemi eğitimi
├─ ChatGPT kullanım rehberi
├─ Proje mimarisi kılavuzu
├─ Prompt library'si
└─ Integrasyon örnekleri
```

### 📁 technical/ (15 dosya - TEKNİK REFERANS)

```
⚙️ Sistem teknik detayları
├─ API mimarisi
├─ Database şemaları
├─ Performance best practices
├─ Sistem bileşenleri
└─ Optimization stratejileri
```

### 📁 api/ (1 dosya)

```
🔌 REST API dökümentasyonu
└─ Endpoint referansları
```

### 📁 admin/ (3 dosya)

```
👨‍💼 Admin paneli dokümantasyonu
├─ İlan yönetim sayfası
├─ Show sayfası analizi
└─ Oluşturma sayfası planlama
```

### 📁 features/ (3 dosya)

```
✨ Özellik dokümantasyonu
├─ Harita sistemi
├─ Emlak türleri
└─ Yazlık kiraları
```

### 📁 integrations/ (3 dosya)

```
🔗 Harici sistem entegrasyonları
├─ N8N automation
├─ Maps provider'ları
└─ TCMB API
```

### 📁 analysis/ (1 dosya)

```
📊 Özellik analiz raporları
└─ Feature mapping
```

### 📁 deployment/ (1 dosya)

```
🚀 Deployment dokümantasyonu
└─ CI/CD konfigürasyonu
```

### 📁 development/ (6 dosya)

```
🔨 Geliştirme rehberleri
├─ Component library
├─ Development setup
├─ Example codes
└─ Utility referansları
```

### 📁 cleanup/ (2 dosya)

```
🧹 Temizlik ve bakım
├─ Cleanup raporu
└─ File gereksizlik analizi
```

### 📁 prompts/ (5 dosya)

```
💬 AI prompt'ları
├─ Master prompt
├─ Copilot talimatlari
├─ Prompt library
└─ Template'ler
```

### 📁 reports/ (1 dosya)

```
📈 Analitik raporları
└─ Sistem metriksleri
```

### 📁 yalihan-becii/ (4 dosya)

```
🔐 Context7 Yalıhan Bekçi
├─ Servis entegrasyonu
├─ Eğitim dokümantasyonu
└─ Rule engine
```

---

## 🔀 Sistem İlişkilerine göre İlişki Matrisi

### AI SİSTEMİ → Bağlantılar

```
AI_KONSOLIDE.md (500 satır)
  ├─ Kullanan: TECHNICAL (API endpoints), USAGE (örnekler)
  ├─ Kullanan: INTEGRATIONS (MCP servers, provider entegrasyonları)
  ├─ Kullanan: MODULES (Talep, CRM modülleri)
  └─ Kaynaklar: docs/ai/*, docs/ai-training/*, docs/prompts/*
```

### API/TEKNIK SİSTEMİ → Bağlantılar

```
TECHNICAL_KONSOLIDE.md (581 satır)
  ├─ Kullanan: AI (endpoint'ler), MODULES (route'lar)
  ├─ Kullanan: INTEGRATIONS (external API'ler)
  ├─ Kullanan: RULES (pattern standartları)
  ├─ Kullanan: CONTEXT7 (compliance)
  └─ Kaynaklar: docs/technical/*, docs/api/*, database migrations
```

### ENTEGRASYON SİSTEMİ → Bağlantılar

```
INTEGRATIONS_KONSOLIDE.md (993 satır - HUB)
  ├─ Kulanılan: AI (LLM provider'ları), TECHNICAL (API mimarisi)
  ├─ Kullanan: USAGE (workflow örnekleri), MODULES (N8N trigger'ları)
  ├─ Kullanan: FEATURES (Maps, vb)
  └─ Kaynaklar: docs/integrations/*, mcp-servers/*, docs/submaps/*
```

### MODÜL SİSTEMİ → Bağlantılar

```
MODULES_KONSOLIDE.md (365 satır)
  ├─ Kullanan: TECHNICAL (controller'lar, service'ler)
  ├─ Kullanan: RULES (naming conventions), CONTEXT7 (compliance)
  ├─ Kullanan: AI (async job'lar), INTEGRATIONS (harici çağrılar)
  └─ Kaynaklar: app/Modules/*, route dosyaları
```

---

## 📅 Tarih Standartlaştırması

| Kategori           | Eski Tarih   | YENİ Tarih       | Durum        |
| ------------------ | ------------ | ---------------- | ------------ |
| Konsolide Dosyalar | Değişken     | 25 Kasım 2025    | ✅ Standart  |
| Archive Dosyaları  | Değişken     | 25 Kasım 2025    | ✅ Standart  |
| AI Training        | Değişken     | 25 Kasım 2025    | ✅ Standart  |
| README.md          | 24 Ekim 2025 | ⚠️ GÜNCELLENMELİ | ⏳ Beklemede |
| Teknik Dosyalar    | Değişken     | 25 Kasım 2025    | ✅ Standart  |

---

## 📋 Konsolide Dosya İçerik Sayımı

```
AI_KONSOLIDE_2025_11_25.md               500 satır
CONTEXT7_KONSOLIDE_2025_11_25.md         388 satır
FEATURES_KONSOLIDE_2025_11_25.md         423 satır
ILAN_SISTEMI_KONSOLIDE_2025_11_25.md     238 satır
INTEGRATIONS_KONSOLIDE_2025_11_25.md     993 satır (%)
MODULES_KONSOLIDE_2025_11_25.md          365 satır
ROADMAP_KONSOLIDE_2025_11_25.md          391 satır
RULES_KONSOLIDE_2025_11_25.md            544 satır
TECHNICAL_KONSOLIDE_2025_11_25.md        581 satır
USAGE_KONSOLIDE_2025_11_25.md            478 satır
─────────────────────────────────────────────────
TOPLAM                                 4,901 satır
```

---

## 🎯 AI/API Entegrasyon Noktaları

### 1. AI Modeli Akışı

```
Prompt (docs/prompts/)
    ↓
AI Sağlayıcı (AI_KONSOLIDE: OpenAI/DeepSeek/Gemini/Claude)
    ↓
API Endpoint (TECHNICAL: /api/ai/*, /api/ai-review/*)
    ↓
Service Layer (MODULES: App\Modules\*\Services)
    ↓
Database (TECHNICAL: migration'lar, schema)
    ↓
N8N Workflow (INTEGRATIONS: trigger'lar)
```

### 2. Entegrasyon Sistemi Akışı

```
External Service (Maps, TCMB, TKGM)
    ↓
MCP Server (INTEGRATIONS: Yalıhan Bekçi, Upstash)
    ↓
Laravel Service (MODULES: *\Services)
    ↓
Web/API Endpoint (TECHNICAL: routes/*, controller'lar)
    ↓
Frontend (resources/views/, resources/js/)
```

### 3. Context7 Compliance Akışı

```
Source Code (app/*, routes/*)
    ↓
Context7 Validator (TECHNICAL: artisan context7:validate-migration)
    ↓
RULES Check (RULES: naming, pattern'ler)
    ↓
Yalıhan Bekçi (INTEGRATIONS: MCP server)
    ↓
Auto Fix (TECHNICAL: artisan context7:validate-migration --auto-fix)
```

---

## 📊 Dosya Sayımı Özeti

| Dizin        | Dosya   | Konsolide  | Satırlar    | Durum          |
| ------------ | ------- | ---------- | ----------- | -------------- |
| active/      | 23      | ✅ 10      | 4,901       | Tamamlandı     |
| archive/     | 37      | ✅ Arşiv   | 20,000+     | Tarihsel       |
| ai-training/ | 24      | ✅ AI      | 500+        | Eğitim         |
| technical/   | 15      | ✅ TECH    | 2,000+      | Referans       |
| Diğer        | 25      | ✅ Çeşitli | 1,500+      | Destekleyici   |
| **TOPLAM**   | **124** | **10**     | **~29,000** | **Tamamlandı** |

---

## ✅ Durum Özeti (25 Kasım 2025)

- ✅ 10 konsolide dosya oluşturuldu (4,901 satır)
- ✅ 124 orijinal dosya arşivlendi (20,000+ satır)
- ✅ Tüm tarihler standartlaştırıldı (2025-11-25)
- ✅ AI/API/Context7 ilişkileri haritalandı
- ⚠️ README.md güncellenmesi beklemede (24 Ekim → 25 Kasım)
- ✅ Context7 compliance tamamlandı (0 violation)

---

**Son Güncelleme:** 25 Kasım 2025  
**Sistem Durumu:** Üretim Hazır  
**Konsolide Dosya Toplam:** 4,901 satır  
**Orijinal Kaynak Toplam:** ~29,000 satır
