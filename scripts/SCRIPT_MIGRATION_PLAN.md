# Script Migration Plan - 2025-11-11

**Tarih:** 2025-11-11 15:45  
**Durum:** 🔄 DEVAM EDİYOR  
**Amaç:** Script'leri helper library kullanacak şekilde güncellemek ve duplicate script'leri birleştirmek

---

## ✅ TAMAMLANAN İŞLEMLER

### 1. Helper Library Oluşturuldu
- ✅ `scripts/lib/common.sh` - Ortak fonksiyonlar
- ✅ `scripts/lib/logger.sh` - Logging fonksiyonları
- ✅ Test edildi ve çalışıyor

### 2. Script Güncellemeleri
- ✅ `context7-daily-check.sh` - Helper library eklendi
- ✅ `check-order-column.sh` - Helper library eklendi
- ✅ Syntax kontrolü başarılı

### 3. Unified Scanner Oluşturuldu
- ✅ `context7-scanner-unified.sh` - Birleştirilmiş scanner
- ✅ Tüm modları destekliyor (full, quick, pre-commit)
- ✅ Tüm formatları destekliyor (text, markdown, json)

---

## 🔄 DEVAM EDEN İŞLEMLER

### 1. Diğer Script'leri Güncelleme

**Öncelikli Script'ler:**
- [ ] `context7-pre-commit-check.sh`
- [ ] `check-secrets.sh`
- [ ] `check-sql-injection.sh`
- [ ] `context7-full-scan.sh`

**Şablon:**
```bash
# Her script'in başına ekle:
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/lib/common.sh"
source "$SCRIPT_DIR/lib/logger.sh"
setup_logging_trap

# Renkli çıktıları değiştir:
# echo -e "${RED}..." → print_error "..."
# echo -e "${GREEN}..." → print_success "..."
# echo -e "${BLUE}..." → print_info "..."
```

---

### 2. Duplicate Script'leri Birleştirme

**Scanner Script'leri:**
- [x] `context7-scanner-unified.sh` oluşturuldu
- [ ] Eski script'leri archive'e taşı:
  - `context7-compliance-scanner.sh` → `archive/`
  - `context7-compliance-scanner-improved.sh` → `archive/`
  - `context7-compliance-scanner.php` → `archive/` (veya tut, PHP versiyonu olarak)

**Plan:**
1. Unified scanner'ı test et
2. Eski script'leri archive'e taşı
3. Referansları güncelle (pre-commit, CI/CD)

---

### 3. Test Script'ini Çalıştırma

**Adımlar:**
1. Tüm script'leri test et
2. Hataları düzelt
3. Test sonuçlarını dokümante et

---

## 📋 SONRAKI ADIMLAR

### Bugün (Kalan İşler)
1. ✅ Unified scanner oluşturuldu
2. 🔄 Diğer script'leri güncelle (4 script)
3. 🔄 Eski scanner script'lerini archive'e taşı

### Bu Hafta
4. Tüm script'leri test et
5. Dokümantasyonu güncelle
6. CI/CD pipeline'ı güncelle

---

## 🎯 HEDEF

- ✅ Tüm script'ler helper library kullanıyor
- ✅ Duplicate script'ler birleştirildi
- ✅ Test script'i çalışıyor
- ✅ Dokümantasyon güncel

---

**Son Güncelleme:** 2025-11-11 15:45  
**Durum:** 🔄 DEVAM EDİYOR

