# Scripts Directory - Kullanım Kılavuzu

**Son Güncelleme:** 2025-11-11  
**Durum:** ✅ AKTİF

---

## 📋 İÇİNDEKİLER

1. [Context7 Script'leri](#context7-scripts)
2. [Helper Libraries](#helper-libraries)
3. [Test Script'leri](#test-scripts)
4. [Kullanım Örnekleri](#usage-examples)
5. [Best Practices](#best-practices)

---

## 🔍 CONTEXT7 SCRİPTLERİ

### Ana Scanner
- **`context7-full-scan.sh`** ⭐ - Tüm projeyi tarar, Context7 ihlallerini bulur
- **`context7-compliance-scanner.php`** - PHP tabanlı detaylı scanner

### Pre-commit Hook Script'leri
- **`check-order-column.sh`** - Migration/model'de `order` kontrolü
- **`check-secrets.sh`** - Gizli bilgi kontrolü
- **`check-sql-injection.sh`** - SQL injection riski kontrolü
- **`context7-pre-commit-check.sh`** - Kapsamlı Context7 kontrolü

### Scheduler Script'leri
- **`context7-daily-check.sh`** - Günlük otomatik tarama (09:00)

---

## 📚 HELPER LIBRARIES

### `lib/common.sh`
Ortak fonksiyonlar:
- `color_print()` - Renkli çıktı
- `print_success()` - Başarı mesajı
- `print_error()` - Hata mesajı
- `check_dependency()` - Bağımlılık kontrolü
- `validate_input()` - Girdi doğrulama

**Kullanım:**
```bash
source scripts/lib/common.sh

print_header "Script Başlığı"
check_dependencies "php" "bash"
print_success "İşlem tamamlandı"
```

### `lib/logger.sh`
Logging fonksiyonları:
- `log_info()` - Bilgi logu
- `log_error()` - Hata logu
- `log_warn()` - Uyarı
- `setup_logging_trap()` - Otomatik logging

**Kullanım:**
```bash
source scripts/lib/logger.sh

setup_logging_trap
log_info "Script başladı"
log_error "Hata oluştu"
```

---

## 🧪 TEST SCRİPTLERİ

### `test-all-scripts.sh`
Tüm script'leri test eder:
- Syntax kontrolü
- Executable kontrolü
- Help option kontrolü
- Dry-run kontrolü

**Kullanım:**
```bash
./scripts/test-all-scripts.sh
```

---

## 💡 KULLANIM ÖRNEKLERİ

### Yeni Script Oluşturma

```bash
#!/bin/bash
# Script Name: example-script.sh
# Description: Example script

set -euo pipefail

# Source libraries
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"
source "$SCRIPT_DIR/lib/logger.sh"

# Setup logging
setup_logging_trap

# Main
print_header "Example Script"

# Check dependencies
check_dependencies "php" "bash"

# Validate input
validate_input "$1" "Parametre gerekli"

# Your code here
print_success "İşlem tamamlandı"
```

---

## ✅ BEST PRACTICES

### 1. Hata Yönetimi
```bash
set -euo pipefail  # Strict mode
trap cleanup EXIT  # Cleanup on exit
```

### 2. Logging
```bash
source scripts/lib/logger.sh
setup_logging_trap
log_info "Important message"
```

### 3. Renkli Çıktı
```bash
source scripts/lib/common.sh
print_success "Success message"
print_error "Error message"
```

### 4. Bağımlılık Kontrolü
```bash
check_dependencies "php" "bash" "grep"
```

### 5. Girdi Doğrulama
```bash
validate_input "$1" "Parameter description"
validate_file "$file" "File description"
```

---

## 📊 SCRIPT İSTATİSTİKLERİ

- **Toplam Script:** 98
- **Bash Script'leri:** 43
- **PHP Script'leri:** 55
- **Aktif Context7 Script'leri:** 12
- **Pre-commit Hook Script'leri:** 4
- **Scheduler Script'leri:** 1

---

## 🔗 İLGİLİ DOKÜMANTASYON

- `SCRIPT_INVENTORY_2025-11-11.md` - Script envanteri
- `SCRIPTS_STATUS_REPORT_2025-11-11.md` - Durum raporu
- `SCRIPT_IMPROVEMENTS_2025-11-11.md` - İyileştirme önerileri
- `README_CONTEXT7_SCANNER.md` - Context7 scanner dokümantasyonu

---

**Son Güncelleme:** 2025-11-11  
**Durum:** ✅ AKTİF