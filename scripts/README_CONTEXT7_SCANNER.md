# Context7 Compliance Scanner - Kullanım Kılavuzu

**Tarih:** 2025-11-10  
**Durum:** ✅ HAZIR VE ÇALIŞIYOR

---

## 📋 OLUŞTURULAN SCRİPTLER

### 1. `scripts/context7-full-scan.sh` ⭐ ÖNERİLEN
- **macOS uyumlu** bash script
- Tüm Context7 kurallarını kontrol eder
- Markdown/JSON rapor desteği
- Renkli terminal çıktısı

**Kullanım:**
```bash
# Temel tarama
./scripts/context7-full-scan.sh

# Rapor ile
./scripts/context7-full-scan.sh --report

# JSON rapor
./scripts/context7-full-scan.sh --json
```

### 2. `scripts/context7-compliance-scanner.php`
- PHP script (daha gelişmiş)
- Detaylı analiz
- JSON/Markdown rapor desteği

**Kullanım:**
```bash
php scripts/context7-compliance-scanner.php --report
```

### 3. `scripts/context7-compliance-scanner.sh`
- Basit bash script (eski versiyon)

---

## 🔍 KONTROL EDİLEN KURALLAR

### 1. Database Fields (CRITICAL)
- ❌ `order` → ✅ `display_order`
- ❌ `durum` → ✅ `status`
- ❌ `aktif` → ✅ `status`
- ❌ `is_active` → ✅ `status`
- ❌ `enabled` → ✅ `status` (status field olarak)
- ❌ `sehir` → ✅ `il`
- ❌ `musteri` → ✅ `kisi`

### 2. CSS Classes (CRITICAL)
- ❌ `neo-*` → ✅ Tailwind CSS
- ❌ `btn-*`, `card-*`, `form-control` → ✅ Tailwind CSS

### 3. JavaScript (CRITICAL)
- ❌ jQuery → ✅ Vanilla JS
- ❌ `subtleVibrantToast` → ✅ `window.toast`

### 4. Layouts (CRITICAL)
- ❌ `layouts.app` → ✅ `admin.layouts.neo`

### 5. Routes (CRITICAL)
- ❌ `route('crm.*')` → ✅ `route('admin.*')`

### 6. Migrations (CRITICAL)
- ❌ `$table->integer('order')` → ✅ `$table->integer('display_order')`

---

## 📊 ÖRNEK ÇIKTI

```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔍 Context7 Compliance Scanner
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

📋 Database Fields: order → display_order
❌ CRITICAL: app/Models/CategoryField.php:20
   Pattern: 'order'
   → order → display_order kullanılmalı

📋 Routes: crm.* → admin.*
❌ CRITICAL: app/Modules/Crm/Controllers/KisiController.php:114
   Pattern: route('crm.
   → crm.* routes yasak - admin.* kullanılmalı

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
📊 TARAMA ÖZETİ
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

Toplam İhlal: 30
  ❌ Critical: 30
  ⚠️  High: 0
  ℹ️  Medium: 0
  ℹ️  Low: 0

✅ Rapor oluşturuldu: .context7/compliance-report-20251111-133113.md
⚠️  30 ihlal bulundu.
```

---

## 🎯 KULLANIM SENARYOLARI

### Günlük Kontrol
```bash
# Sabah kontrolü
./scripts/context7-full-scan.sh --report .context7/daily-check-$(date +%Y%m%d).md
```

### Pre-commit Hook
```bash
# .git/hooks/pre-commit içine ekle
if ! ./scripts/context7-full-scan.sh; then
    echo "❌ Context7 compliance check failed"
    exit 1
fi
```

### CI/CD Pipeline
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

### Otomatik Raporlama
```bash
# Haftalık rapor
./scripts/context7-full-scan.sh --report .context7/weekly-report-$(date +%Y%m%d).md
```

---

## 🔧 GELİŞTİRME

### Yeni Kural Ekleme

**Bash Script'e Ekle:**
```bash
# Yeni kontrol bölümü ekle
echo -e "${BLUE}📋 Yeni Kural${NC}"
while IFS= read -r line; do
    file=$(echo "$line" | cut -d: -f1)
    line_num=$(echo "$line" | cut -d: -f2)
    content=$(echo "$line" | cut -d: -f3- | sed 's/^[[:space:]]*//')
    
    # Yorum satırı kontrolü
    if [[ "$content" =~ ^(//|\*|#) ]]; then
        continue
    fi
    
    # Exclude kontrolü
    if [[ "$content" =~ exclude_pattern ]]; then
        continue
    fi
    
    add_violation "critical" "$file" "$line_num" "pattern" "mesaj"
done < <(grep -rnE "pattern" --include="*.php" app/ 2>/dev/null | head -20 || true)
echo ""
```

**PHP Script'e Ekle:**
```php
'new_rule' => [
    'pattern' => '/pattern/',
    'replacement' => 'replacement',
    'severity' => 'critical',
    'message' => 'Mesaj',
    'exclude' => ['exclude_pattern'],
],
```

---

## 📈 İSTATİSTİKLER

**Son Tarama Sonuçları (2025-11-10):**
- Toplam İhlal: 30
- Critical: 30
- High: 0
- Medium: 0
- Low: 0

**Tespit Edilen İhlal Kategorileri:**
1. Database Fields: `order`, `durum` kullanımları
2. Routes: `crm.*` route kullanımları
3. CSS Classes: `neo-*` kullanımları (muhtemelen)

---

## 🚀 HIZLI BAŞLANGIÇ

```bash
# 1. Script'i çalıştırılabilir yap
chmod +x scripts/context7-full-scan.sh

# 2. Temel tarama
./scripts/context7-full-scan.sh

# 3. Rapor ile tarama
./scripts/context7-full-scan.sh --report

# 4. Sonuçları kontrol et
cat .context7/compliance-report-*.md
```

---

## 📚 REFERANSLAR

- `.context7/authority.json` - Tüm Context7 kuralları
- `.context7/PERMANENT_STANDARDS.md` - Kalıcı standartlar
- `.context7/ORDER_DISPLAY_ORDER_STANDARD.md` - Order standardı
- `yalihan-bekci/knowledge/` - Öğrenilmiş pattern'ler

---

## ⚠️ NOTLAR

1. **Yorum Satırları:** Script yorum satırlarını otomatik olarak atlar
2. **False Positives:** Bazı durumlarda false positive'ler olabilir, manuel kontrol önerilir
3. **Performans:** Büyük projelerde tarama biraz zaman alabilir
4. **macOS Uyumluluk:** Script macOS'ta test edilmiştir

---

**Son Güncelleme:** 2025-11-10  
**Durum:** ✅ HAZIR VE ÇALIŞIYOR

