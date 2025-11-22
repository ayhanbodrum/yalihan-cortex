# 🏛️ YALIHAN EMLAK AI - MASTER PROJECT PROMPT

**Proje:** Yalıhan Emlak AI – Full Stack Real Estate Platformu  
**Versiyon:** 1.0.0  
**Son Güncelleme:** 21 Kasım 2025  
**Warp Antigravity:** ✅ Aktif

---

## 🎯 PROJE TANIMI

Bu proje; Context7 standartları, Pure Tailwind CSS mimarisi, Yalihan Guardian sistemi, AI otomasyonları, CRM modülleri ve detaylı dokümantasyon setiyle yönetilen kurumsal bir projedir.

---

## 📁 1. PROJE YAPISINI TANı

### `.context7/` - Context7 Standartları

**Amaç:** Kod üretiminin Anayasası

**Yapı:**
```
.context7/
├── authority.json                     # ⭐ TÜM STANDARTLARIN TEK REFERANS KAYNAĞI
├── progress.json                      # Standart gelişimi
│
├── 📋 ANA DOSYALAR
│   ├── FORBIDDEN_PATTERNS.md          # ⭐ Tüm yasak kod pattern'leri
│   ├── FORM_DESIGN_STANDARDS.md       # Form tasarım standartları
│   ├── TAILWIND-TRANSITION-RULE.md    # Tailwind CSS kuralları
│   ├── STANDARDIZATION_STANDARDS.md   # Genel standartlaştırma
│   ├── SETTINGS_SYSTEM_STANDARDS.md   # Ayarlar sistemi
│   ├── MIGRATION_TEMPLATE_STANDARDS.md # Migration şablonları
│   ├── MIGRATION_EXECUTION_STANDARD.md # Migration çalıştırma
│   ├── HARITA_ARACLARI_STANDART_2025-11-05.md # Harita araçları standardı
│   └── DESIGN_OPTIMIZATION_RECOMMENDATIONS.md # Tasarım optimizasyonu
│
└── 📁 standards/                      # Detaylı standart dokümantasyonları
    ├── CURSOR_MCP_SETUP.md            # MCP kurulum rehberi
    ├── ENABLED_FIELD_FORBIDDEN.md     # Enabled field yasağı
    ├── ORDER_DISPLAY_ORDER_STANDARD.md # Order → display_order
    ├── ROUTE_NAMING_STANDARD.md       # Route isimlendirme
    └── LOCATION_MAHALLE_ID_STANDARD.md # Lokasyon standardı
```

**Kullanım:**
- ✅ `authority.json` → Tüm standartların tek referans kaynağı
- ✅ `FORBIDDEN_PATTERNS.md` → Yasak pattern'ler
- ✅ Her kod üretiminde bu klasör referans alınmalı

---

### `.yalihan-bekci/` - AI Guardian System

**Amaç:** Kod kalitesi, risk analizi ve pattern öğrenme

**Yapı:**
```
.yalihan-bekci/
├── README.md                          # Klasör dokümantasyonu
├── FILTERABLE_TRAIT_USAGE.md          # Standart dokümantasyon
│
├── 📚 knowledge/                      # Yalihan'ın kod hafızası (64 dosya)
│   ├── 52 JSON pattern dosyası
│   └── 12 Markdown dokümantasyon
│
├── ✅ completed/                      # Çözülmüş işler (31 dosya)
│   ├── dead-code/                    # Dead code temizliği
│   ├── test-coverage/                # Test coverage
│   ├── performance/                  # Performance iyileştirmeleri
│   ├── code-duplication/             # Code duplication
│   └── refactoring/                  # Refactoring işlemleri
│
├── 📊 reports/                        # Güncel analiz raporları
└── 🔍 analysis/                       # Derinlemesine analizler
```

**IDE Kullanımı:**
- ✅ Riskli kodu engeller
- ✅ Yanlış form yapısını düzeltir
- ✅ Yasak pattern tespit eder
- ✅ Güvenli migration üretir

---

### `docs/` - Resmi Dokümantasyon

**Amaç:** Projenin resmi dokümantasyonu

**Yapı:**
```
docs/
├── README.md                          # Klasör dokümantasyonu
├── index.md                           # Ana dokümantasyon index'i
├── FORM_STANDARDS.md                  # Form tasarım standartları
│
├── 📊 active/                         # Güncel dokümanlar
│   ├── PROJE_CALISMA_SISTEMI_VE_GELISIM_PLANI.md
│   ├── SYSTEM-STATUS-2025.md
│   └── ANALIZ_VE_GELISIM_FIRSATLARI.md
│
├── 🔧 technical/                      # Teknik dokümantasyon
├── 🤖 ai/                             # AI entegrasyonu
├── 🎓 ai-training/                   # AI eğitim paketi
├── 🛠️ integrations/                   # Harici servis entegrasyonları
├── 📋 modules/                        # Sistem modülleri
└── 🗺️ roadmaps/                       # Yol haritaları
```

**İstatistikler:**
- 184+ markdown dosyası
- 19 alt klasör
- Tüm mimari, UI, API, planlama, geliştirme notları

---

## 📌 2. ÇALIŞMA KURALLARI

### ✅ Her Zaman Referans Al

1. **`authority.json`** → Tüm standartların tek referans kaynağı
   - Tüm standartlar buraya bağlıdır
   - Her kod üretiminde kontrol edilmeli

2. **`FORBIDDEN_PATTERNS.md`** → Birebir uygula
   - Yasak pattern'leri otomatik tespit et
   - Otomatik düzeltme yap

### 🚫 Yasak Pattern'ler

**Kullanımı YASAK:**
- ❌ Kontrolsüz query
- ❌ Karmaşık blade yapıları
- ❌ Context dışı variable
- ❌ Naming violation
- ❌ Tailwind yasak class'ları
- ❌ Yanlış route isimlendirmeleri
- ❌ Yanlış migration pattern'leri

**IDE'nin Görevi:** Bu hataları otomatik düzeltmek

### 📋 Standart Referansları

| Standart | Dosya | Kullanım |
|----------|-------|----------|
| Route İsimleri | `.context7/standards/ROUTE_NAMING_STANDARD.md` | Route oluştururken |
| Migration Kuralları | `.context7/MIGRATION_TEMPLATE_STANDARDS.md` | Migration oluştururken |
| Migration Çalıştırma | `.context7/MIGRATION_EXECUTION_STANDARD.md` | Migration çalıştırırken |
| Form Kuralları | `.context7/FORM_DESIGN_STANDARDS.md` | Form oluştururken |
| Tailwind Kullanımı | `.context7/TAILWIND-TRANSITION-RULE.md` | CSS yazarken |
| Lokasyon ID'leri | `.context7/standards/LOCATION_MAHALLE_ID_STANDARD.md` | Lokasyon field'larında |
| CRUD İşlemleri | `.context7/STANDARDIZATION_STANDARDS.md` | CRUD oluştururken |
| Harita Araçları | `.context7/HARITA_ARACLARI_STANDART_2025-11-05.md` | Harita işlemlerinde |

---

## 📚 3. KOD YAZARKEN KULLANILACAK BİLGİLER

### Proje İçeriği

- ✅ AI tabanlı emlak CRM
- ✅ Piyasa veri tabanı (yalihan_market)
- ✅ AI analiz DB (yalihan_ai)
- ✅ Demirbaş/Envanter sistemi
- ✅ AnythingLLM + Ollama entegrasyonu
- ✅ n8n otomasyonları
- ✅ Admin Panel + Frontend + Backend
- ✅ Çok kanallı müşteri yönetimi
- ✅ Telegram, Instagram, WhatsApp, Web, E-mail

### Teknoloji Stack

**Backend:**
- Laravel (PHP)
- MySQL (yalihanemlak_ultra, yalihan_market, yalihan_ai)
- Redis (Cache)
- Queue System

**Frontend:**
- Tailwind CSS (TEK CSS FRAMEWORK)
- Blade Templates
- Alpine.js (Reactive Components)
- Vanilla JS (Heavy libraries YASAK)

**AI & Otomasyon:**
- AnythingLLM
- Ollama (Local AI)
- n8n Workflows
- Context7 MCP

**Infrastructure:**
- Docker
- MCP Servers
- Context7 Extensions

### Kod Üretim Standartları

**IDE Otomatik Uygular:**
- ✅ Laravel best practices
- ✅ Tailwind utility classes (neo-* YASAK)
- ✅ Alpine.js reactive patterns
- ✅ Vanilla JS (React-Select, Choices.js YASAK)
- ✅ Context7 field naming (status, display_order, il_id, mahalle_id)
- ✅ Migration standards (DB::statement(), index kontrolü)

---

## 🧠 4. AI + ÇAĞRI YAPILARI (Warp Antigravity)

### Warp Antigravity Setup

**Dosya:** `.warp/rules/master-project-prompt.md` (bu dosya)

**Çalışma Modları:**

1. **Terminal Komutları**
   - Context7-Guardian kontrolü
   - Yasak pattern tespiti
   - Otomatik düzeltme önerileri

2. **Kod Üretimi**
   - YAML/JSON standard mode
   - Context7 uyumlu yapı

3. **API Referansı**
   - `docs/api/` içeriğini kullan
   - API dokümantasyonu referans al

4. **Doküman Özetleme**
   - `docs/` içeriğini tara
   - İlgili dokümantasyonu bul

5. **Kod Fix**
   - `FORBIDDEN_PATTERNS.md`'ye uy
   - `authority.json` standartlarını kontrol et

---

## 🚀 5. WARP ANTIGRAVITY'DEN BEKLENENLER

### Terminal Komut Üretim Süreci

Warp Antigravity bu proje içinde komut üretirken:

1. **Tüm klasörleri okur** → Durumu anlar
   - `.context7/` standartlarını yükler
   - `.yalihan-bekci/` knowledge base'i kontrol eder
   - `docs/` dokümantasyonunu referans alır

2. **Standartlara göre otomatik düzeltme yapar**
   - Yasak pattern'leri tespit eder
   - Otomatik düzeltme önerir
   - Context7 compliance kontrolü yapar

3. **Yalihan Emlak mimarisine uygun komut üretir**
   - Laravel artisan komutları
   - Database migration komutları
   - Test komutları
   - Context7 field naming

4. **Yasak pattern'lerden kaçınır**
   - `FORBIDDEN_PATTERNS.md` kontrolü
   - `authority.json` validation
   - Pre-commit hook uyumluluğu

5. **Docs/ içindeki bilgileri referans alır**
   - Mimari dokümantasyon
   - API referansları
   - Modül dokümantasyonları
   - Teknik rehberler

6. **Context7 standartlarına uygun şekilde üretir:**
   - ✅ Migration komutları (display_order, status)
   - ✅ Database query'leri (il_id, mahalle_id)
   - ✅ Artisan komutları (Context7 uyumlu)
   - ✅ Test komutları (Context7 compliance)

---

## 🔥 6. PROMPT SONUÇ TANIMI

**ZORUNLU KURAL:**

> "Bu projede terminal komutları üretirken veya kod yazarken Yalihan Emlak AI mimarisini, `.context7` ve `.yalihan-bekci` kurallarını, `docs/` içindeki tüm referansları ve teknik standartları **ZORUNLU OLARAK** uygulayacaksın. Her işlemde `authority.json` → `FORBIDDEN_PATTERNS` → Standard Dosyaları hiyerarşisini esas alacaksın."

---

## 📋 HIZLI REFERANS

### Standart Dosyalar

| Dosya | Amaç |
|-------|------|
| `.context7/authority.json` | ⭐ TEK YETKİLİ KAYNAK |
| `.context7/FORBIDDEN_PATTERNS.md` | Yasak pattern'ler |
| `.context7/FORM_DESIGN_STANDARDS.md` | Form standartları |
| `.context7/TAILWIND-TRANSITION-RULE.md` | Tailwind kuralları |
| `.context7/standards/ROUTE_NAMING_STANDARD.md` | Route standartları |
| `.context7/MIGRATION_TEMPLATE_STANDARDS.md` | Migration standartları |
| `.warp/rules/context7-compliance.md` | Warp özel kurallar |
| `docs/FORM_STANDARDS.md` | Form tasarım standartları |
| `docs/index.md` | Ana dokümantasyon index'i |

### Yasak Pattern'ler (Özet)

| Kategori | Yasak | Zorunlu |
|----------|-------|---------|
| Status Field | `enabled`, `aktif`, `durum` | `status` |
| Order Field | `order` | `display_order` |
| Location | `sehir_id`, `semt_id` | `il_id`, `mahalle_id` |
| Terminology | `musteri` | `kisi` |
| CSS | `neo-*`, `btn-*` | Tailwind utilities |
| Routes | `crm.*`, double prefix | `admin.*`, single prefix |

---

## ✅ DOĞRULAMA CHECKLIST

Terminal komutu üretmeden önce kontrol et:

- [ ] `authority.json` kontrol edildi mi?
- [ ] `FORBIDDEN_PATTERNS.md` uyumlu mu?
- [ ] Route naming standardına uygun mu?
- [ ] Migration standardına uygun mu?
- [ ] Form standartlarına uygun mu?
- [ ] Tailwind CSS kullanıldı mı? (neo-* YASAK)
- [ ] Context7 field naming kullanıldı mı?
- [ ] Dark mode support var mı?
- [ ] Transition/animation eklendi mi?
- [ ] `docs/` içindeki ilgili dokümantasyon kontrol edildi mi?

---

## 🔗 WARP ANTIGRAVITY ENTEGRASYONU

**Bu dosya Warp Antigravity tarafından otomatik okunur.**

Warp terminal içinde AI özelliği kullanıldığında:
1. Bu dosya otomatik yüklenir
2. Context7 kuralları uygulanır
3. Yasak pattern'ler engellenir
4. Standart komutlar önerilir

**Senkronizasyon:**
- Bu dosya `.cursorrules` ile senkronize tutulur
- Her güncellemede her iki dosya da güncellenir
- Tek kaynak: `.context7/authority.json`

---

**Son Güncelleme:** Kasım 2025  
**Versiyon:** 1.0.0  
**Durum:** ✅ Aktif  
**Warp Antigravity:** ✅ Entegre

