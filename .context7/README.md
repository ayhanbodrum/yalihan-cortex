# 📚 Context7 Standartları ve Dokümantasyonu

**Son Güncelleme:** Kasım 2025  
**Versiyon:** 5.4.0  
**Durum:** ✅ Aktif

---

## 🎯 Amaç

Bu klasör, Yalıhan Emlak projesinin **Context7 standartlarını** ve **compliance kurallarını** içerir. Tüm IDE'ler, AI araçları ve otomatik script'ler bu klasördeki standartları referans almalıdır.

---

## 📂 Klasör Yapısı

```
.context7/
├── README.md                          # Bu dosya
├── authority.json                     # ⭐ Ana otorite dosyası (TEK KAYNAK)
├── progress.json                      # İlerleme takibi
│
├── 📋 STANDART DOSYALAR (Korunmalı)
│   ├── FORBIDDEN_PATTERNS.md          # ⭐ Tüm yasak pattern'ler (birleştirilmiş)
│   ├── FORM_DESIGN_STANDARDS.md       # Form tasarım standartları
│   ├── TAILWIND-TRANSITION-RULE.md    # Tailwind CSS kuralları
│   ├── STANDARDIZATION_STANDARDS.md   # Genel standartlaştırma kuralları
│   ├── SETTINGS_SYSTEM_STANDARDS.md   # Ayarlar sistemi standartları
│   ├── MIGRATION_TEMPLATE_STANDARDS.md # Migration şablon standartları
│   ├── MIGRATION_EXECUTION_STANDARD.md # Migration çalıştırma standardı
│   ├── HARITA_ARACLARI_STANDART_2025-11-05.md # Harita araçları standardı
│   └── DESIGN_OPTIMIZATION_RECOMMENDATIONS.md # Tasarım optimizasyon önerileri
│
├── 📁 standards/                       # Detaylı standart dokümantasyonları
│   ├── ENABLED_FIELD_FORBIDDEN.md     # Enabled field yasağı (detay)
│   ├── ORDER_DISPLAY_ORDER_STANDARD.md # Order → display_order (detay)
│   ├── ROUTE_NAMING_STANDARD.md       # Route isimlendirme (detay)
│   ├── LOCATION_MAHALLE_ID_STANDARD.md # Lokasyon standardı (detay)
│   └── CURSOR_MCP_SETUP.md            # MCP kurulum rehberi
│
├── 📊 GÜNCEL RAPORLAR
│   ├── daily-check-*.md               # Günlük compliance kontrol raporları
│   └── ci-report-*.json               # CI/CD compliance raporları
│
├── 📁 archive/                        # Arşivlenmiş eski raporlar
│   └── 2025-11/                       # Kasım 2025 arşivi
│
└── api.php                            # API route tanımları (referans)
```

---

## ⭐ Ana Dosyalar

### 1. `authority.json` - TEK YETKİLİ KAYNAK

**En önemli dosya!** Tüm Context7 standartları burada tanımlıdır.

- **Versiyon:** 5.4.0
- **Standart:** C7-PERMANENT-STANDARDS-2025-11-07
- **Kapsam:** Tüm IDE'ler, AI araçları, otomatik script'ler

**İçerik:**

- Forbidden patterns (yasak desenler)
- Required patterns (zorunlu desenler)
- Database field naming standards
- CSS framework standards (Tailwind CSS ONLY)
- Route naming standards
- Migration standards

### 2. Standart Dokümantasyon Dosyaları

Her standart için ayrı bir dokümantasyon dosyası:

| Dosya                             | Açıklama                                  | Öncelik    |
| --------------------------------- | ----------------------------------------- | ---------- |
| `FORBIDDEN_PATTERNS.md`           | ⭐ Tüm yasak pattern'ler (birleştirilmiş) | ⭐⭐⭐⭐⭐ |
| `FORM_DESIGN_STANDARDS.md`        | Form tasarım standartları                 | ⭐⭐⭐⭐⭐ |
| `TAILWIND-TRANSITION-RULE.md`     | Tailwind CSS + transition zorunluluğu     | ⭐⭐⭐⭐⭐ |
| `MIGRATION_TEMPLATE_STANDARDS.md` | Migration şablon standartları             | ⭐⭐⭐⭐   |
| `STANDARDIZATION_STANDARDS.md`    | Genel standartlaştırma kuralları          | ⭐⭐⭐⭐   |
| `standards/*.md`                  | Detaylı standart dokümantasyonları        | ⭐⭐⭐     |

---

## 🚫 Yasak Desenler (Forbidden Patterns)

**Tüm yasak pattern'ler:** `.context7/FORBIDDEN_PATTERNS.md` (birleştirilmiş referans)

### Hızlı Referans

| Kategori     | Yasak                       | Zorunlu                  |
| ------------ | --------------------------- | ------------------------ |
| Status Field | `enabled`, `aktif`, `durum` | `status`                 |
| Order Field  | `order`                     | `display_order`          |
| Location     | `sehir_id`, `semt_id`       | `il_id`, `mahalle_id`    |
| Terminology  | `musteri`                   | `kisi`                   |
| CSS          | `neo-*`, `btn-*`            | Tailwind utilities       |
| Routes       | `crm.*`, double prefix      | `admin.*`, single prefix |

**Detaylı dokümantasyon:** `.context7/standards/` klasöründe

---

## ✅ Zorunlu Standartlar

### 1. Tailwind CSS - TEK CSS FRAMEWORK

**CRITICAL:** Tailwind CSS projedeki **TEK** CSS framework'tür.

- ✅ Tüm styling Tailwind utility classes ile yapılmalı
- ✅ Her interactive element'te transition/animation ZORUNLU
- ✅ Dark mode support ZORUNLU
- ❌ Neo Design System, Bootstrap, Foundation YASAK

**Örnek:**

```html
<!-- ✅ DOĞRU -->
<button
    class="px-4 py-2 bg-blue-600 text-white rounded-lg
               hover:bg-blue-700 hover:scale-105
               transition-all duration-200
               dark:bg-blue-500 dark:hover:bg-blue-600"
>
    Kaydet
</button>

<!-- ❌ YANLIŞ -->
<button class="neo-btn neo-btn-primary">Kaydet</button>
```

### 2. Database Field Naming

- ✅ Tüm field'lar İngilizce olmalı
- ✅ `status` (NOT `durum`, `aktif`, `enabled`)
- ✅ `display_order` (NOT `order`)
- ✅ `il_id` (NOT `sehir_id`)

### 3. Migration Standards

- ✅ `display_order` kolonu kullanılmalı
- ✅ `status` kolonu kullanılmalı
- ✅ Index'ler doğru tanımlanmalı
- ✅ DB::statement() ile index kontrolü yapılmalı

---

## 📊 Compliance Takibi

### Günlük Kontrol

```bash
# Günlük compliance kontrolü
php scripts/context7-compliance-scanner.php

# Sonuçlar: .context7/daily-check-*.md
```

### CI/CD Raporları

- **Dosya:** `.context7/ci-report-*.json`
- **Format:** JSON
- **İçerik:** Toplam ihlal sayısı, kritik ihlaller, yüksek öncelikli ihlaller

---

## 🔧 Kullanım

### Yeni Standart Ekleme

1. Standart dokümantasyonunu `.context7/` klasörüne ekle
2. `authority.json` dosyasını güncelle
3. Gerekirse pre-commit hook'ları güncelle
4. README.md'yi güncelle

### Standart Kontrolü

```bash
# Tam tarama
./scripts/context7-full-scan.sh

# Pre-commit kontrolü
./scripts/context7-pre-commit-check.sh
```

---

## 📁 Arşiv

Eski raporlar ve geçici analizler `.context7/archive/` klasöründe saklanır:

- **Eski compliance raporları** - Tarihsel kayıt
- **Geçici analiz raporları** - Referans
- **Eski log dosyaları** - Audit trail
- **Daily reports arşivi** - Snapshot'lar

**⚠️ ÖNEMLİ:**

- Arşivlenmiş dosyalar **referans amaçlıdır**, aktif kullanılmaz
- [outdated] / [duplicate_hint] flag'leri **o anki durum** içindi
- **"Yapılacak iş" değil, tarihsel kayıttır**
- Yalıhan Bekçi taraması bu klasörleri **dışarıda bırakmalı**

**Aktif standart kaynakları:**

- `.context7/authority.json`
- `.context7/PERMANENT_STANDARDS.md`
- `.context7/FORBIDDEN_PATTERNS.md`
- `docs/active/RULES_KONSOLIDE_2025_11_25.md`

---

## 🔗 İlgili Dokümantasyon

- **Ana Dokümantasyon:** `docs/index.md`
- **Form Standartları:** `docs/FORM_STANDARDS.md`
- **Context7 Memory:** `.context7/CONTEXT7_MEMORY_SYSTEM.md`
- **Yalıhan Bekçi:** `yalihan-bekci/` klasörü

---

## 📝 Notlar

- **authority.json** dosyası **TEK YETKİLİ KAYNAK** olarak kullanılmalıdır
- Tüm standartlar **permanent** olarak işaretlenmiştir (geri alınamaz)
- Yeni kod yazarken **mutlaka** bu standartlara uyulmalıdır
- Pre-commit hook'lar otomatik kontrol yapar

---

**Son Güncelleme:** Kasım 2025  
**Versiyon:** 5.4.0  
**Durum:** ✅ Aktif ve Güncel
