# Admin Layout Standartları

## 🎯 Amaç

Tüm admin sayfalarında layout tutarlılığını sağlamak ve `admin.layouts.neo` gibi geçersiz layout kullanımlarını önlemek.

## ✅ Standart

**Tüm admin sayfaları şu layout'u kullanmalı:**

```blade
@extends('admin.layouts.admin')
```

## ❌ Yasak Layout'lar

Aşağıdaki layout kullanımları **YASAKTIR**:

- `@extends('admin.layouts.neo')` ❌ (Bu layout mevcut değil)
- `@extends('admin.layouts.app')` ❌ (Wrapper, doğrudan kullanılmamalı)
- `@extends('layouts.app')` ❌ (Deprecated)

## 🔍 Kontrol Yöntemleri

### 1. Otomatik Pre-Commit Kontrolü

Her commit öncesi otomatik kontrol yapılır:

```bash
# Pre-commit hook otomatik çalışır
git commit -m "feat: yeni özellik"
```

### 2. Manuel Kontrol Scripti

```bash
# Tüm admin sayfalarını kontrol et
./scripts/check-admin-layouts.sh
```

### 3. Manuel Grep Kontrolü

```bash
# Geçersiz layout kullanımlarını bul
grep -r "@extends('admin.layouts.\(neo\|app\)')" resources/views/admin/ --include="*.blade.php"
```

## 🛠️ Düzeltme

Eğer geçersiz layout kullanımı bulunursa:

1. **Dosyayı aç:**
   ```bash
   nano resources/views/admin/[dosya-yolu].blade.php
   ```

2. **Layout'u düzelt:**
   ```blade
   # ÖNCE (YANLIŞ)
   @extends('admin.layouts.neo')
   
   # SONRA (DOĞRU)
   @extends('admin.layouts.admin')
   ```

3. **Kontrol et:**
   ```bash
   ./scripts/check-admin-layouts.sh
   ```

## 📋 Checklist

Yeni admin sayfası oluştururken:

- [ ] `@extends('admin.layouts.admin')` kullanıldı mı?
- [ ] `admin.layouts.neo` kullanılmadı mı?
- [ ] `admin.layouts.app` kullanılmadı mı?
- [ ] Pre-commit hook kontrolünden geçti mi?

## 🚨 Hata Durumları

### Hata: `View [admin.layouts.neo] not found`

**Sebep:** Geçersiz layout kullanımı

**Çözüm:**
```blade
@extends('admin.layouts.admin')  // ✅ DOĞRU
```

### Hata: Layout tutarsızlığı

**Sebep:** Farklı sayfalarda farklı layout'lar kullanılıyor

**Çözüm:** Tüm sayfaları `admin.layouts.admin` kullanacak şekilde güncelle

## 📚 İlgili Dosyalar

- **Pre-commit Hook:** `.githooks/pre-commit`
- **Kontrol Scripti:** `scripts/check-admin-layouts.sh`
- **Layout Dosyası:** `resources/views/admin/layouts/admin.blade.php`

## 🔄 Güncelleme Tarihi

- **2025-12-05:** İlk dokümantasyon oluşturuldu
- **2025-12-05:** Pre-commit hook'a layout kontrolü eklendi
- **2025-12-05:** Kontrol scripti oluşturuldu

