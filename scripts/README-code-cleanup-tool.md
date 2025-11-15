# 🧹 Code Cleanup Tool - Kullanım Kılavuzu

## 📋 İçindekiler

- [Giriş](#giriş)
- [Kurulum](#kurulum)
- [Modlar](#modlar)
- [Kullanım Örnekleri](#kullanım-örnekleri)
- [Best Practices](#best-practices)

---

## 🎯 Giriş

**Code Cleanup Tool**, Yalıhan Emlak projesinde kod taraması, değiştirme ve temizlik işlemlerini otomatikleştiren güçlü bir bash script'idir.

### ✨ Özellikler:

- 🔍 **Pattern Arama**: Kod tabanında güçlü arama
- 🔄 **Toplu Değiştirme**: Güvenli find & replace
- 🗑️ **Otomatik Cleanup**: Kullanılmayan/boş dosyaları tespit
- 📊 **Kod Analizi**: Detaylı istatistikler
- 🛡️ **Güvenli Mod**: Dry-run ile önce test
- 💾 **Otomatik Backup**: Her değişiklikte yedek

---

## 📦 Kurulum

Script zaten executable, hemen kullanabilirsiniz:

```bash
cd /Users/macbookpro/Projects/yalihanemlakwarp
./scripts/code-cleanup-tool.sh help
```

---

## 🎮 Modlar

### 1️⃣ **search** - Pattern Arama

Kod tabanında pattern arar ve detaylı rapor sunar.

```bash
./scripts/code-cleanup-tool.sh search 'PATTERN' [TARGET_DIR]
```

**Örnekler:**

```bash
# Tüm projede arama
./scripts/code-cleanup-tool.sh search 'stable-create'

# Belirli klasörde arama
./scripts/code-cleanup-tool.sh search 'Alpine' resources/views/admin

# Belirli dosya tipinde arama (script içinde değiştirilebilir)
./scripts/code-cleanup-tool.sh search 'EmlakLoc'
```

**Çıktı:**

```
📊 Sonuç Özeti:
  Blade Files:      14 matches
  PHP Files:        5 matches
  JavaScript Files: 8 matches
  CSS Files:        0 matches
  TOPLAM:           27 matches

📋 Detaylı Sonuçlar:
  [dosya:satır] kod satırı...
```

---

### 2️⃣ **replace** - Pattern Değiştirme

Pattern'leri güvenli bir şekilde değiştirir.

```bash
./scripts/code-cleanup-tool.sh replace 'OLD' 'NEW' [--dry-run|--execute] [TARGET]
```

**⚠️ ÖNEMLİ:** Varsayılan mod `--dry-run`'dır (güvenlik için).

**Örnekler:**

```bash
# 1. ADIM: Dry-run ile test et (hiçbir şey değişmez)
./scripts/code-cleanup-tool.sh replace 'stable-create' 'ilan-create' --dry-run

# 2. ADIM: Gerçek değişikliği yap
./scripts/code-cleanup-tool.sh replace 'stable-create' 'ilan-create' --execute

# Belirli klasörde değiştir
./scripts/code-cleanup-tool.sh replace 'oldClass' 'newClass' --execute resources/views
```

**Güvenlik Özellikleri:**

- ✅ Dry-run varsayılan
- ✅ Onay sorar (`y/N`)
- ✅ Otomatik backup yapar (`storage/backups/`)
- ✅ Etkilenen dosya sayısını gösterir

**Backup Konumu:**

```
storage/backups/code-cleanup-YYYYMMDD_HHMMSS/
```

---

### 3️⃣ **find-unused** - Kullanılmayan Dosyaları Bul

Kullanılmayan view dosyalarını tespit eder.

```bash
./scripts/code-cleanup-tool.sh find-unused [TARGET_DIR]
```

**Örnekler:**

```bash
# İlan modülünde kullanılmayan dosyaları bul
./scripts/code-cleanup-tool.sh find-unused resources/views/admin/ilanlar

# Tüm admin view'lerde bul
./scripts/code-cleanup-tool.sh find-unused resources/views/admin
```

**Nasıl Çalışır:**

1. Tüm `.blade.php` dosyalarını tarar
2. CRUD dosyalarını atlar (`index`, `create`, `edit`, `show`)
3. Her dosya için projede kullanım arar
4. Hiç referans yoksa "kullanılmıyor" işaretler

**Çıktı:**

```
❌ Kullanılmıyor: resources/views/admin/ilanlar/valuation.blade.php
❌ Kullanılmıyor: resources/views/admin/ilanlar/aktarim.blade.php

📊 Toplam: 2 kullanılmayan dosya
```

---

### 4️⃣ **find-duplicates** - Duplicate Dosyaları Bul

Benzer isimli duplicate dosyaları tespit eder.

```bash
./scripts/code-cleanup-tool.sh find-duplicates
```

**Aranacak Pattern'ler:**

- `*-v2.js`
- `*-clean.js`
- `*-fixed.js`
- `*-final.js`
- `*-working.js`
- `*-simple.js`
- `*-old.blade.php`
- `*-backup.blade.php`

**Çıktı:**

```
Pattern: *-v2.js
  /path/to/live-search-v2.js

Pattern: *-fixed.js
  /path/to/ilan-form-alpine-fixed.js

📊 Toplam: 5 potansiyel duplicate
```

---

### 5️⃣ **cleanup-empty** - Boş Dosyaları Temizle

0 byte olan dosyaları bulur ve siler.

```bash
./scripts/code-cleanup-tool.sh cleanup-empty [--dry-run|--execute]
```

**Örnekler:**

```bash
# 1. Önce dry-run ile test et
./scripts/code-cleanup-tool.sh cleanup-empty --dry-run

# 2. Gerçekten sil
./scripts/code-cleanup-tool.sh cleanup-empty --execute
```

**Güvenlik:**

- ✅ Dry-run varsayılan
- ✅ Silmeden önce onay sorar
- ✅ Node_modules ve vendor dahil tarar (dikkatli kullanın!)

**Çıktı:**

```
📋 Boş Dosyalar: 28
  resources/views/frontend/danismanlar/index.blade.php
  resources/views/layouts/navigation.blade.php
  ...

⚠️  DRY-RUN MODE: Dosyalar silinmedi!
```

---

### 6️⃣ **analyze** - Kod Analizi

Proje hakkında detaylı istatistikler üretir.

```bash
./scripts/code-cleanup-tool.sh analyze
```

**Raporlar:**

- 📊 Dosya sayıları (Blade, PHP, JS, CSS)
- 📈 En büyük dosyalar (Top 10)
- 📉 Boş dosya tespiti
- 💾 Dosya boyutları

**Çıktı:**

```
GENEL İSTATİSTİKLER
  Blade Dosyaları:     366
  PHP Dosyaları:       605
  JavaScript (src):    54
  JavaScript (public): 63
  CSS Dosyaları:       10

DOSYA BOYU ANALİZİ
📈 En Büyük 10 Dosya:
  1.4M  /path/to/swagger-ui-bundle.js
  1.0M  /path/to/bundle.min.js
  ...

📉 Boş Dosyalar:
  ⚠️  27 boş dosya bulundu!
```

---

## 💡 Kullanım Örnekleri

### Senaryo 1: Legacy Kod Temizliği

**Problem:** `stable-create` referanslarını `ilan-create` olarak değiştirmek istiyoruz.

```bash
# 1. Mevcut durumu analiz et
./scripts/code-cleanup-tool.sh search 'stable-create'

# 2. Kaç dosya etkilenecek göster
./scripts/code-cleanup-tool.sh replace 'stable-create' 'ilan-create' --dry-run

# 3. Gerçek değişikliği yap
./scripts/code-cleanup-tool.sh replace 'stable-create' 'ilan-create' --execute

# 4. Kontrol et
./scripts/code-cleanup-tool.sh search 'stable-create'  # 0 sonuç bekleniyor
```

---

### Senaryo 2: Kullanılmayan Dosyaları Bul ve Temizle

**Problem:** `admin/ilanlar` altında kullanılmayan component'ler var.

```bash
# 1. Kullanılmayan dosyaları bul
./scripts/code-cleanup-tool.sh find-unused resources/views/admin/ilanlar/components

# 2. Manuel olarak kontrol et ve sil
# (Script sadece tespit eder, silmez - güvenlik için)

# 3. Tekrar kontrol
./scripts/code-cleanup-tool.sh find-unused resources/views/admin/ilanlar/components
```

---

### Senaryo 3: Duplicate Dosyaları Temizle

**Problem:** Çok fazla `-v2`, `-fixed`, `-final` dosyası var.

```bash
# 1. Duplicate'leri tespit et
./scripts/code-cleanup-tool.sh find-duplicates

# 2. Kullanımlarını kontrol et
./scripts/code-cleanup-tool.sh search 'ilan-form-alpine-fixed'
./scripts/code-cleanup-tool.sh search 'ilan-form-alpine-final'

# 3. Manuel olarak sil (kullanılmıyorsa)
rm public/js/admin/ilan-form-alpine-fixed.js
rm public/js/admin/ilan-form-alpine-final.js

# 4. Tekrar kontrol
./scripts/code-cleanup-tool.sh find-duplicates
```

---

### Senaryo 4: Boş Dosyaları Temizle

**Problem:** 0 byte boş dosyalar var.

```bash
# 1. Boş dosyaları bul
./scripts/code-cleanup-tool.sh cleanup-empty --dry-run

# 2. Listede node_modules/vendor olmayanları kontrol et
# (Sadece proje dosyalarını temizlemek istiyoruz)

# 3. Güvenli olanları sil (EXECUTE dikkatli kullanın!)
# Manuel silme önerilir:
rm resources/views/frontend/danismanlar/index.blade.php
rm resources/views/layouts/navigation.blade.php

# 4. Analiz ile kontrol
./scripts/code-cleanup-tool.sh analyze
```

---

### Senaryo 5: Haftalık Kod Analizi

**Rutin:** Her hafta kod tabanını analiz et.

```bash
# 1. Genel analiz
./scripts/code-cleanup-tool.sh analyze > weekly-report-$(date +%Y%m%d).txt

# 2. Duplicate kontrol
./scripts/code-cleanup-tool.sh find-duplicates

# 3. Boş dosya kontrol
./scripts/code-cleanup-tool.sh cleanup-empty --dry-run

# 4. Legacy pattern arama
./scripts/code-cleanup-tool.sh search 'deprecated'
./scripts/code-cleanup-tool.sh search 'TODO'
./scripts/code-cleanup-tool.sh search 'FIXME'
```

---

## 🛡️ Best Practices

### ✅ DO (Yapın)

1. **Her Zaman Önce Dry-Run:**

    ```bash
    # ✅ İyi
    ./scripts/code-cleanup-tool.sh replace 'old' 'new' --dry-run
    ./scripts/code-cleanup-tool.sh replace 'old' 'new' --execute

    # ❌ Kötü (direkt execute)
    ./scripts/code-cleanup-tool.sh replace 'old' 'new' --execute
    ```

2. **Git Commit Öncesi Test:**

    ```bash
    ./scripts/code-cleanup-tool.sh analyze
    ./scripts/code-cleanup-tool.sh find-duplicates
    git add .
    git commit -m "Cleanup: ..."
    ```

3. **Belirli Klasörlerde Çalış:**

    ```bash
    # ✅ İyi (hedefli)
    ./scripts/code-cleanup-tool.sh search 'pattern' resources/views/admin

    # ❌ Dikkatli (tüm proje, vendor dahil)
    ./scripts/code-cleanup-tool.sh search 'pattern'
    ```

4. **Backup'ları Kontrol Et:**
    ```bash
    ls -la storage/backups/
    ```

---

### ❌ DON'T (Yapmayın)

1. **Node_modules/Vendor'ı Değiştirmeyin:**

    ```bash
    # ❌ Tehlikeli
    ./scripts/code-cleanup-tool.sh replace 'something' 'new' --execute node_modules
    ```

2. **Dry-Run Olmadan Execute:**

    ```bash
    # ❌ Tehlikeli
    ./scripts/code-cleanup-tool.sh replace 'critical' 'new' --execute
    ```

3. **Toplu Cleanup (Vendor dahil):**

    ```bash
    # ❌ Tehlikeli
    ./scripts/code-cleanup-tool.sh cleanup-empty --execute

    # ✅ Güvenli (manuel silme)
    rm specific/project/file.blade.php
    ```

---

## 📊 Rapor Örnekleri

### Search Raporu

```
🔍 Arama Pattern: stable-create
📁 Hedef: /Users/macbookpro/Projects/yalihanemlakwarp

📊 Sonuç Özeti:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  Blade Files:      14 matches
  PHP Files:        5 matches
  JavaScript Files: 8 matches
  CSS Files:        0 matches
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  TOPLAM:           27 matches
```

### Replace Raporu (Dry-Run)

```
🔄 Değiştirme İşlemi
  Eski Pattern: stable-create
  Yeni Pattern: ilan-create
  Mod: --dry-run

📊 Etkilenen Dosyalar: 27

📋 Dosya Listesi:
resources/views/admin/ilanlar/create.blade.php
resources/js/admin/stable-create.js
...

⚠️  DRY-RUN MODE: Değişiklik yapılmadı!
   Gerçek değişiklik için: --execute flag'ini kullanın
```

### Analyze Raporu

```
📊 Kod Analizi

╔══════════════════════════════════════════════════════════╗
║  GENEL İSTATİSTİKLER                                     ║
╚══════════════════════════════════════════════════════════╝

  Blade Dosyaları:     366
  PHP Dosyaları:       605
  JavaScript (src):    54
  JavaScript (public): 36
  CSS Dosyaları:       10

╔══════════════════════════════════════════════════════════╗
║  DOSYA BOYU ANALİZİ                                      ║
╚══════════════════════════════════════════════════════════╝

📈 En Büyük 10 Dosya:
1.4M  vendor/swagger-api/swagger-ui/dist/swagger-ui-bundle.js
1.0M  node_modules/terser/dist/bundle.min.js
...

📉 Boş Dosyalar:
  ✅ Boş dosya yok!
```

---

## 🚀 Gelişmiş Kullanım

### Custom Pattern Search

Script'i düzenleyerek custom pattern'ler ekleyebilirsiniz:

```bash
# scripts/code-cleanup-tool.sh içinde

# Find duplicate patterns bölümünde:
patterns=(
    "*-v2.js"
    "*-clean.js"
    # Ekle:
    "*-legacy.js"
    "*-deprecated.blade.php"
)
```

### Otomatik Raporlama (Cron)

```bash
# Her hafta pazartesi 09:00'da analiz
0 9 * * 1 /path/to/yalihanemlakwarp/scripts/code-cleanup-tool.sh analyze > /path/to/reports/weekly-$(date +\%Y\%m\%d).txt
```

---

## 📝 Notlar

- **Backup Konumu:** `storage/backups/code-cleanup-TIMESTAMP/`
- **Log Yok:** Script log oluşturmaz, sadece terminal output
- **Context7 Uyumlu:** Context7 compliance kontrolleri yok (manuel kontrol gerekli)
- **Güvenli:** Dry-run varsayılan, her execute'da onay sorar

---

## 🆘 Sorun Giderme

### "Permission Denied" Hatası

```bash
chmod +x scripts/code-cleanup-tool.sh
```

### Script Bulunamıyor

```bash
# Tam path kullan
/Users/macbookpro/Projects/yalihanemlakwarp/scripts/code-cleanup-tool.sh help
```

### Backup Restore

```bash
# Backup'tan geri yükle
cp -r storage/backups/code-cleanup-20251024_090000/* ./
```

---

## 📚 İlgili Dokümanlar

- Context7 Compliance: `docs/context7/`
- Yalıhan Bekçi: `YALIHAN_BEKCI_KULLANIM_KILAVUZU.md`
- Cleanup Reports: `DUPLICATE_CLEANUP_COMPLETED_REPORT.md`

---

## ✅ Özet

**Code Cleanup Tool** ile:

- 🔍 Hızlı kod araması
- 🔄 Güvenli toplu değiştirme
- 🗑️ Otomatik cleanup
- 📊 Detaylı analiz
- 💾 Otomatik backup

**Temel Komutlar:**

```bash
./scripts/code-cleanup-tool.sh search 'pattern'
./scripts/code-cleanup-tool.sh replace 'old' 'new' --dry-run
./scripts/code-cleanup-tool.sh analyze
./scripts/code-cleanup-tool.sh find-duplicates
./scripts/code-cleanup-tool.sh cleanup-empty --dry-run
```

**Güvenlik:** Her zaman önce `--dry-run`, sonra `--execute`! 🛡️

---

**Version:** 1.0.0  
**Last Updated:** 2025-10-24  
**Author:** Yalıhan Emlak Dev Team
