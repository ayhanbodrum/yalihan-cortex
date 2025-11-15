# Context7 Compliance Scanner - Kullanım Kılavuzu

**Tarih:** 2025-11-10  
**Durum:** ✅ HAZIR

---

## 📋 OLUŞTURULAN SCRİPTLER

### 1. `scripts/context7-compliance-scanner.sh` (Bash)
- Basit bash script
- Hızlı tarama
- Renkli çıktı

### 2. `scripts/context7-compliance-scanner.php` (PHP)
- Daha gelişmiş PHP script
- Detaylı analiz
- JSON/Markdown rapor desteği

### 3. `scripts/context7-full-scan.sh` (Bash - macOS Uyumlu)
- macOS uyumlu bash script
- Tüm Context7 kurallarını kontrol eder
- Markdown/JSON rapor desteği

---

## 🚀 KULLANIM

### Temel Tarama
```bash
# Bash script (hızlı)
./scripts/context7-full-scan.sh

# PHP script (detaylı)
php scripts/context7-compliance-scanner.php
```

### Rapor ile Tarama
```bash
# Markdown rapor
./scripts/context7-full-scan.sh --report

# Özel dosya adı
./scripts/context7-full-scan.sh --report .context7/my-report.md

# JSON rapor
./scripts/context7-full-scan.sh --json
```

---

## 🔍 KONTROL EDİLEN KURALLAR

### 1. Database Fields
- ❌ `order` → ✅ `display_order`
- ❌ `durum` → ✅ `status`
- ❌ `aktif` → ✅ `status`
- ❌ `is_active` → ✅ `status`
- ❌ `enabled` → ✅ `status` (status field olarak)
- ❌ `sehir` → ✅ `il`
- ❌ `musteri` → ✅ `kisi`

### 2. CSS Classes
- ❌ `neo-*` → ✅ Tailwind CSS
- ❌ `btn-*`, `card-*`, `form-control` → ✅ Tailwind CSS

### 3. JavaScript
- ❌ jQuery → ✅ Vanilla JS
- ❌ `subtleVibrantToast` → ✅ `window.toast`

### 4. Layouts
- ❌ `layouts.app` → ✅ `admin.layouts.neo`

### 5. Routes
- ❌ `route('crm.*')` → ✅ `route('admin.*')`

### 6. Migrations
- ❌ `$table->integer('order')` → ✅ `$table->integer('display_order')`

---

## 📊 ÇIKTI FORMATI

### Terminal Çıktısı
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔍 Context7 Compliance Scanner
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📋 Database Fields: order → display_order
❌ CRITICAL: app/Models/CategoryField.php:20
   Pattern: 'order'
   → order → display_order kullanılmalı

📊 TARAMA ÖZETİ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Toplam İhlal: 10
  ❌ Critical: 8
  ⚠️  High: 2
```

### Markdown Rapor
```markdown
# Context7 Compliance Report

**Tarih:** 2025-11-10 10:30:00
**Durum:** ⚠️ TARAMA TAMAMLANDI

## 📊 Özet
- **Toplam İhlal:** 10
- **Critical:** 8
- **High:** 2
```

### JSON Rapor
```json
{
  "date": "2025-11-10 10:30:00",
  "status": "completed",
  "summary": {
    "total": 10,
    "critical": 8,
    "high": 2
  },
  "violations": [...]
}
```

---

## 🎯 ÖNERİLER

### Günlük Kullanım
```bash
# Sabah kontrolü
./scripts/context7-full-scan.sh --report .context7/daily-check-$(date +%Y%m%d).md
```

### Pre-commit Hook Entegrasyonu
```bash
# .git/hooks/pre-commit içine ekle
./scripts/context7-full-scan.sh || exit 1
```

### CI/CD Entegrasyonu
```yaml
# .github/workflows/context7-check.yml
- name: Context7 Compliance Check
  run: |
    ./scripts/context7-full-scan.sh --json .context7/ci-report.json
    if [ $? -ne 0 ]; then
      echo "❌ Context7 compliance check failed"
      exit 1
    fi
```

---

## 🔧 GELİŞTİRME

### Yeni Kural Ekleme

1. **PHP Script'e Ekle:**
```php
'new_rule' => [
    'pattern' => '/pattern/',
    'replacement' => 'replacement',
    'severity' => 'critical',
    'message' => 'Mesaj',
    'exclude' => ['exclude_pattern'],
],
```

2. **Bash Script'e Ekle:**
```bash
# Yeni kontrol bölümü ekle
echo -e "${BLUE}📋 Yeni Kural${NC}"
while IFS=: read -r file line rest; do
    # Kontrol mantığı
    add_violation "critical" "$file" "$line" "pattern" "mesaj"
done < <(grep -rnE "pattern" --include="*.php" app/ 2>/dev/null || true)
```

---

## 📚 REFERANSLAR

- `.context7/authority.json` - Tüm Context7 kuralları
- `.context7/PERMANENT_STANDARDS.md` - Kalıcı standartlar
- `.context7/ORDER_DISPLAY_ORDER_STANDARD.md` - Order standardı
- `yalihan-bekci/knowledge/` - Öğrenilmiş pattern'ler

---

**Son Güncelleme:** 2025-11-10  
**Durum:** ✅ HAZIR