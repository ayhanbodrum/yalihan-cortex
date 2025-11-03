# 🛠️ Scripts Klasörü - Kullanım Rehberi

**Context7 Standardı:** C7-SCRIPTS-GUIDE-2025-10-10  
**Toplam Script:** 39 adet  
**Son Güncelleme:** 10 Ekim 2025

---

## 📂 Script Kategorileri

### 🔥 **1. ANA KULLANILMASI GEREKEN SCRIPTS (Top 5)**

| Script                         | Açıklama                                     | Kullanım                               | Sıklık                 |
| ------------------------------ | -------------------------------------------- | -------------------------------------- | ---------------------- |
| **`usta-test.mjs`**            | 🎯 USTA: Test + Screenshot + Tasarım Analizi | `node scripts/usta-test.mjs`           | 🔥 Design sprint       |
| **`admin-kapsamli-test.mjs`**  | Admin paneli kapsamlı otomatik test sistemi  | `node scripts/admin-kapsamli-test.mjs` | 🔥 Her deploy öncesi   |
| **`gelismis-hata-duzelt.php`** | Otomatik hata düzeltici (akıllı)             | `php scripts/gelismis-hata-duzelt.php` | 🔥 Test sonrası        |
| **`context7-docs-sync.php`**   | Dokümantasyon otomatik senkronizasyonu       | `php scripts/context7-docs-sync.php`   | 🔥 Otomatik (git hook) |
| **`context7-check.sh`**        | Kapsamlı Context7 uyumluluk kontrolü         | `bash scripts/context7-check.sh`       | 🔥 Her commit öncesi   |

---

## 🎯 2. CONTEXT7 UYUMLULUK SCRIPTS

### **2.1 Otomatik Kontrol**

| Script                           | Açıklama                     | Komut                                         |
| -------------------------------- | ---------------------------- | --------------------------------------------- |
| `context7-check.sh`              | Ana Context7 kontrol sistemi | `bash scripts/context7-check.sh`              |
| `context7-prevent-violations.sh` | Kural ihlali önleme          | `bash scripts/context7-prevent-violations.sh` |
| `context7-control.sh`            | Context7 kontrol merkezi     | `bash scripts/context7-control.sh check`      |

**Kullanım Örnekleri:**

```bash
# Temel kontrol
bash scripts/context7-check.sh

# Performans kontrolü
bash scripts/context7-check.sh --performance

# Otomatik düzeltme
bash scripts/context7-check.sh --auto-fix

# Route çakışması kontrolü
bash scripts/context7-check.sh --route-check

# Database field kontrolü
bash scripts/context7-check.sh --database-field-check
```

### **2.2 Otomatik Düzeltme**

| Script                            | Ne Düzeltir                               | Komut                                         |
| --------------------------------- | ----------------------------------------- | --------------------------------------------- |
| `context7-auto-fix.sh`            | Yasak alan adlarını otomatik düzelt       | `bash scripts/context7-auto-fix.sh`           |
| `context7-forbidden-auto-fix.php` | Kod seviyesinde yasak pattern'leri düzelt | `php scripts/context7-forbidden-auto-fix.php` |
| `auto-context7-remediate.sh`      | Kapsamlı Context7 onarımı                 | `bash scripts/auto-context7-remediate.sh`     |

### **2.3 Analiz & Raporlama**

| Script                             | Ne Analiz Eder                            | Çıktı            |
| ---------------------------------- | ----------------------------------------- | ---------------- |
| `context7-forbidden-scan.php`      | Yasak pattern'leri tarar                  | Terminal çıktısı |
| `context7-domain-analyzer.php`     | Kategori/özellik ilişkilerini analiz eder | Rapor            |
| `context7-blade-analyzer.php`      | Blade dosyalarını analiz eder             | Rapor            |
| `context7-controller-analyzer.php` | Controller'ları analiz eder               | Rapor            |
| `context7-model-analyzer.php`      | Model'leri analiz eder                    | Rapor            |
| `context7-seed-analyzer.php`       | Seeder'ları analiz eder                   | Rapor            |
| `context7-tech-analyzer.php`       | Teknoloji stack'i analiz eder             | Rapor            |

### **2.4 Tasarım & UI**

| Script                           | Ne Kontrol Eder                   | Komut                                         |
| -------------------------------- | --------------------------------- | --------------------------------------------- |
| `context7-design-consistency.sh` | Neo Design System uyumluluğu      | `bash scripts/context7-design-consistency.sh` |
| `css-conflict-fixer.php`         | Tailwind CSS çakışmalarını düzelt | `php scripts/css-conflict-fixer.php`          |

---

## 🗄️ 3. DATABASE & MIGRATION SCRIPTS

### **3.1 Migration Otomatik Düzeltme (16 Script!)**

**⚠️ Not:** Bu scriptler geçmiş migration hatalarını düzeltmek için oluşturulmuş. Artık ihtiyaç yok ama referans amaçlı saklanıyor.

| Script                                | Ne Yapar                              | Kullanım Durumu |
| ------------------------------------- | ------------------------------------- | --------------- |
| `advanced-pattern-fixer.php`          | Gelişmiş syntax pattern'lerini düzelt | ✅ Arşiv        |
| `advanced-syntax-learner.php`         | AI-learning ile syntax düzelt         | ✅ Arşiv        |
| `automated-learning-resolver.php`     | Otomatik öğrenen düzeltici            | ✅ Arşiv        |
| `bulk-migration-fixer.php`            | Toplu migration düzeltme              | ✅ Arşiv        |
| `context7-migration-analyzer.php`     | Migration analizi                     | ✅ Arşiv        |
| `context7-migration-auto-cleaner.php` | Otomatik temizlik                     | ✅ Arşiv        |
| `context7-migration-bulk-fixer.php`   | Toplu düzeltme                        | ✅ Arşiv        |
| `context7-migration-syntax-fixer.php` | Syntax düzeltme                       | ✅ Arşiv        |
| `emergency-function-closer.php`       | Function kapanışlarını düzelt         | ✅ Arşiv        |
| `final-push-fixer.php`                | Son düzeltmeler                       | ✅ Arşiv        |
| `final-structure-fixer.php`           | Yapı düzeltme                         | ✅ Arşiv        |
| `fix-duplicate-down-functions.php`    | Duplicate function düzelt             | ✅ Arşiv        |
| `fix-migrations.sh`                   | Migration wrapper                     | ✅ Arşiv        |
| `iterative-learning-fixer.php`        | Iteratif öğrenen düzeltici            | ✅ Arşiv        |
| `migration-syntax-auto-fixer.php`     | Otomatik syntax fixer                 | ✅ Arşiv        |
| `schema-callback-fixer.php`           | Schema callback düzelt                | ✅ Arşiv        |
| `targeted-syntax-fixer.php`           | Hedefli düzeltme                      | ✅ Arşiv        |
| `ultimate-migration-fixer.php`        | Ultimate düzeltici                    | ✅ Arşiv        |
| `ultimate-reconstructor.php`          | Yeniden yapılandırıcı                 | ✅ Arşiv        |
| `ultra-migration-fixer.php`           | Ultra düzeltici                       | ✅ Arşiv        |

**Özet:** Migration hatalarınız düzeltildiği için bunlar arşiv amaçlı. Artık kullanmanıza gerek yok.

### **3.2 Database Schema Export**

| Script                          | Ne Yapar                                       | Kullanım                                    |
| ------------------------------- | ---------------------------------------------- | ------------------------------------------- |
| `export-table-schema.php`       | Tek tablo şemasını markdown olarak export eder | `php scripts/export-table-schema.php`       |
| `export-multi-table-schema.php` | Çoklu tablo şeması export                      | `php scripts/export-multi-table-schema.php` |

**Kullanım:**

```bash
# ilanlar tablosu şemasını export et
php scripts/export-table-schema.php

# Çıktı: docs/technical/database/schemas/ilanlar-schema.md
```

---

## 🤖 4. YARDIMCI ARAÇLAR

| Script                 | Ne Yapar                           | Kullanım                           |
| ---------------------- | ---------------------------------- | ---------------------------------- |
| `cache-clear-tool.php` | Cache ve VS Code session temizliği | `php scripts/cache-clear-tool.php` |

---

## 📋 5. FAYDALı SCRIPTS KULLANIM ÖRNEKLERİ

### **🔥 Scenario 1: Deploy Öncesi Full Check**

```bash
# 1. Context7 kontrolü
bash scripts/context7-check.sh --performance --security --quality

# 2. Admin panel otomatik test
node scripts/admin-otomatik-test.mjs

# 3. API sağlık kontrolü
bash scripts/context7-link-health.sh

# 4. Rapor kontrol
cat admin-test-report.md

# Tümü başarılı ise:
# → Deploy edilebilir!
```

### **🎯 Scenario 2: Yeni Özellik Geliştirdikten Sonra**

```bash
# 1. Kod kalitesi kontrolü
bash scripts/context7-check.sh --quality --design-check --ui-check

# 2. Dokümantasyon senkronize et
php scripts/context7-docs-sync.php

# 3. Otomatik test
node scripts/admin-otomatik-test.mjs

# Hata varsa:
# - Ekran görüntülerini incele
# - Düzelt
# - Tekrar test et
```

### **🛠️ Scenario 3: Database Değişikliği Sonrası**

```bash
# 1. Schema export et
php scripts/export-table-schema.php

# 2. Context7 database field kontrolü
bash scripts/context7-check.sh --database-field-check

# 3. Migration kontrolü
php artisan migrate --pretend

# Sorun yoksa:
# → Migrate yapabilirsin
```

### **🧹 Scenario 4: Haftalık Bakım**

```bash
# Tüm kapsamlı kontroller (tek komut!)
php scripts/doktor.php full

# Çıktı:
# - Migration analizi
# - Context7 compliance
# - Yasak pattern taraması
# - Schema export
# - Kapsamlı rapor
```

---

## 🎯 EN ÖNEMLİ 3 KOMUT

### **1️⃣ Admin Panel Test (YENİ!)**

```bash
# Tek komut ile tüm admin sayfalarını test et
bash scripts/admin-otomatik-test.sh

# Ne yapar:
# ✅ Otomatik login
# ✅ Tüm linkleri keşfeder
# ✅ Her sayfayı test eder
# ✅ Hataları bulur
# ✅ Ekran görüntüsü alır
# ✅ Detaylı rapor oluşturur
```

### **2️⃣ Context7 Full Check**

```bash
# Kapsamlı Context7 kontrolü
bash scripts/context7-check.sh \
  --performance \
  --security \
  --quality \
  --database-field-check \
  --route-check

# Ne yapar:
# ✅ Yasak pattern taraması
# ✅ Performans kontrolü (N+1, cache)
# ✅ Güvenlik kontrolü (CSRF, XSS)
# ✅ Kod kalitesi (PSR-12)
# ✅ Database field uyumluluğu
# ✅ Route çakışması
```

### **3️⃣ Doktor (All-in-One)**

```bash
# Tüm işlemleri tek seferde yap
php scripts/doktor.php full

# Ne yapar:
# ✅ Migration analizi
# ✅ Context7 compliance
# ✅ Yasak pattern taraması
# ✅ Otomatik düzeltme
# ✅ Schema export
# ✅ Kapsamlı rapor
```

---

## 🚀 YENI EKLENENLERIN KULLANIMI

### **🎯 USTA - Ultra Smart Test & Auto-fix (YENİ! v1.0)**

```bash
# 1. USTA testi çalıştır (görsel mod)
node scripts/usta-test.mjs

# 2. Raporu incele
cat usta-test-raporu.md

# 3. Otomatik düzelt
php scripts/usta-duzelt.php

# 4. Tekrar test (before/after karşılaştırma)
node scripts/usta-test.mjs
```

**✨ USTA Ne Yapar:**

-   🔍 Özel sayfaları test eder (7 sayfa)
-   📸 Full page screenshot alır (before/after)
-   🐛 Teknik hataları tespit eder
-   🎨 Tasarım sorunlarını analiz eder (Neo Design System)
-   🔧 Otomatik düzeltme önerir ve uygular
-   ✅ Context7 compliance check
-   👁️ Görsel mod (headless=false) - Süreci izlersin!

**🎨 Tespit Edilen Tasarım Sorunları:**

-   Card yapısı eksik (Neo-card kullanılmamış)
-   Input styling eksik (neo-input yok)
-   Button styling eksik (neo-btn-primary yok)
-   Responsive design eksik (md:, lg: breakpoint yok)
-   Dark mode desteği yok (dark: class yok)
-   Tailwind kullanılmamış

**📸 Çıktılar:**

-   `usta-test-raporu.md` - Detaylı analiz + tasarım şablonları
-   `screenshots/usta-test/before/` - İlk durum
-   `screenshots/usta-test/after/` - Düzeltme sonrası

---

### **Admin Kapsamlı Test Sistemi (YENİ! v2.0)**

```bash
# 1. Kapsamlı otomatik test (tüm sayfalar)
node scripts/admin-kapsamli-test.mjs

# 2. Test sonucu raporunu görüntüle
cat admin-kapsamli-test-raporu.md

# 3. Otomatik hata düzeltici
php scripts/gelismis-hata-duzelt.php

# 4. Testi tekrar çalıştır
node scripts/admin-kapsamli-test.mjs
```

**✨ Ne Test Ediyor:**

-   ✅ Dashboard & Ana Sayfa
-   ✅ CRM (Kişiler, Danışmanlar, Talepler, Takım, Görevler)
-   ✅ İlan Yönetimi (İlanlar, Kategoriler, Özellikler)
-   ✅ Sistem (Kullanıcılar, Ayarlar, Raporlar)
-   ✅ Her sayfa için: Liste, Ekle, Düzenle
-   ✅ Toplam: 42+ sayfa otomatik test

**🎯 Tespit Edilen Hatalar:**

-   `Undefined variable` hataları
-   Eksik tablo migration'ları
-   404 Not Found sayfaları
-   500 Internal Server hatalar
-   Blade syntax hataları

**📋 Otomatik Düzeltilen Hatalar:**

-   ✅ `$taslak`, `$status`, `$danismanlar`, `$ustKategoriler` değişkenleri
-   ✅ `talepler` tablosu migration
-   ✅ Controller ve view uyumsuzlukları

**Çıktılar:**

-   `admin-kapsamli-test-raporu.md` - Detaylı rapor (kategori bazlı)
-   `screenshots/kapsamli-test/error-*.png` - Hatalı sayfa görselleri
-   `screenshots/kapsamli-test/success-*.png` - Başarılı sayfa görselleri

### **Context7 Dokümantasyon Senkronizasyonu**

```bash
# Manuel senkronizasyon
php scripts/context7-docs-sync.php

# Ne yapar:
# 1. Tüm MD dosyalarını tarar (38 dosya)
# 2. docs/README.md'yi günceller
# 3. İstatistikleri yeniler
# 4. .context7/authority.json'u senkronize eder
# 5. Context7 compliance kontrolü yapar
```

**Otomatik Çalışma:**

```bash
# Git commit öncesi otomatik çalışır
git commit -m "Yeni dokümantasyon"

# .githooks/context7-docs-sync-hook.sh otomatik tetiklenir
# Dokümantasyon otomatik senkronize edilir
```

---

## 📊 Script İstatistikleri

```
📂 Toplam Script: 39

🔥 Aktif Kullanım:        5 script (13%)
⚡ Gerektiğinde:         8 script (20%)
✅ Arşiv (Başarılı):     20 script (51%)
🧪 Analiz Araçları:      6 script (16%)
```

### **Kategori Dağılımı:**

```
Context7 Compliance:     13 script (33%)
Migration Fixers:        20 script (51%) - Arşiv
Database Tools:          2 script (5%)
Admin Test:              2 script (5%)  - YENİ!
Helper Tools:            2 script (6%)
```

---

## 🎨 HIZLI KULLANIM KILAVUZU

### **Deploy Öncesi 3-Adım Kontrol:**

```bash
# 1. Kod kalitesi (30 saniye)
bash scripts/context7-check.sh --quality

# 2. Admin panel test (15 saniye)
bash scripts/admin-otomatik-test.sh

# 3. Rapor kontrol
cat admin-test-report.md

# ✅ Hepsi başarılıysa deploy et!
```

### **Yeni Özellik Sonrası Kontrol:**

```bash
# Tek komut, tüm kontroller
php scripts/doktor.php check
```

### **Dokümantasyon Güncelledikten Sonra:**

```bash
# Otomatik senkronizasyon
php scripts/context7-docs-sync.php

# Ya da git commit ile otomatik olur
git add docs/
git commit -m "Dokümantasyon güncellendi"
# Hook otomatik çalışır
```

### **Haftalık Bakım:**

```bash
# Kapsamlı analiz ve rapor
php scripts/doktor.php full

# Database schema güncellemesi
php scripts/export-table-schema.php
```

---

## 🗑️ TEMİZLİK ÖNERİSİ

**Arşivlenebilecek Scripts (20 adet):**

```bash
# Bu migration fixer'lar başarıyla görevi tamamladı
# Arşiv klasörüne taşınabilir:
mkdir -p scripts/archive/migration-fixers
mv scripts/*-migration-*.php scripts/archive/migration-fixers/
mv scripts/*-syntax-*.php scripts/archive/migration-fixers/
mv scripts/*-fixer.php scripts/archive/migration-fixers/
mv scripts/emergency-*.php scripts/archive/migration-fixers/
mv scripts/ultimate-*.php scripts/archive/migration-fixers/
```

**Sonuç:**

-   Aktif scripts: 19 (kullanışlı)
-   Arşiv: 20 (başarıyla tamamlanmış)

---

## 💡 PRATİK İPUÇLARI

### **1. Otomatik Test Pipeline:**

```bash
# .git/hooks/pre-commit
#!/bin/bash
bash scripts/context7-check.sh --database-field-check || exit 1
php scripts/context7-docs-sync.php
node scripts/admin-otomatik-test.mjs || exit 1
```

### **2. Cron Job (Günlük Test):**

```bash
# crontab -e
0 9 * * * cd /path/to/project && bash scripts/admin-otomatik-test.sh >> logs/admin-test.log 2>&1
```

### **3. CI/CD Integration:**

```yaml
# .github/workflows/test.yml
- name: Context7 Check
  run: bash scripts/context7-check.sh --all

- name: Admin Panel Test
  run: node scripts/admin-otomatik-test.mjs

- name: Upload Screenshots
  uses: actions/upload-artifact@v2
  with:
      name: test-screenshots
      path: screenshots/
```

---

## 📚 İlgili Dökümanlar

-   **Admin Test Rehberi:** `ADMIN_CRAWLER_KULLANIM.md`
-   **Context7 Sync Rehberi:** `CONTEXT7_AUTO_SYNC_GUIDE.md`
-   **Ana Dokümantasyon:** `docs/README.md`

---

## 🎯 ÖNERİLEN KULLANIM SIKLIĞI

| Script                    | Ne Zaman Çalıştır                      |
| ------------------------- | -------------------------------------- |
| `admin-otomatik-test.sh`  | Her deploy öncesi                      |
| `context7-check.sh`       | Her commit öncesi (otomatik hook)      |
| `context7-docs-sync.php`  | MD değişikliği sonrası (otomatik hook) |
| `doktor.php full`         | Haftalık bakım                         |
| `context7-link-health.sh` | Deploy öncesi                          |
| `export-table-schema.php` | Database değişikliği sonrası           |

---

**Context7 Uyumlu:** ✅  
**Otomatik Sistem:** ✅  
**Son Güncelleme:** 10 Ekim 2025
