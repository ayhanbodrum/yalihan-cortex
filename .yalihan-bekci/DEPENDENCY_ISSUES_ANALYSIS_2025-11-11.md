# Dependency Issues Analysis - 2025-11-11

**Tarih:** 2025-11-11 21:10  
**Durum:** ✅ ANALİZ TAMAMLANDI

---

## 📊 ÖZET

| Paket | Durum | Aksiyon |
|-------|-------|---------|
| bacon/bacon-qr-code | ❌ Kullanılmıyor | Kaldırılabilir |
| barryvdh/laravel-dompdf | ✅ Kullanılıyor | Tutulmalı |
| blade-ui-kit/blade-heroicons | ❌ Kullanılmıyor | Kaldırılabilir |
| blade-ui-kit/blade-icons | ❌ Kullanılmıyor | Kaldırılabilir |
| brick/math | ❌ Kullanılmıyor | Kaldırılabilir |
| carbonphp/carbon-doctrine-types | ❌ Kullanılmıyor | Kaldırılabilir |
| composer/pcre | ⚠️ Dependency | Kaldırılamaz |
| composer/semver | ⚠️ Dependency | Kaldırılamaz |
| darkaonline/l5-swagger | ✅ Kullanılıyor | Tutulmalı |
| dasprid/enum | ❌ Kullanılmıyor | Kaldırılabilir |

---

## ✅ KULLANILAN PAKETLER (2 adet)

### 1. ✅ barryvdh/laravel-dompdf
**Kullanım:** PDF export için kullanılıyor  
**Dosyalar:**
- `app/Http/Controllers/Admin/IlanController.php` - `use Barryvdh\DomPDF\Facade\Pdf;`
- `app/Http/Controllers/Admin/MyListingsController.php` - `use Barryvdh\DomPDF\Facade\Pdf;`
- `resources/views/admin/ilanlar/exports/my-listings-pdf.blade.php`

**Sonuç:** ✅ TUTULMALI

---

### 2. ✅ darkaonline/l5-swagger
**Kullanım:** Swagger/OpenAPI dokümantasyonu için kullanılıyor  
**Dosyalar:**
- `config/l5-swagger.php` - Swagger konfigürasyonu
- `resources/views/vendor/l5-swagger/index.blade.php` - Swagger UI

**Sonuç:** ✅ TUTULMALI

---

## ❌ KULLANILMAYAN PAKETLER (6 adet)

### 1. ❌ bacon/bacon-qr-code
**Durum:** Kullanılmıyor  
**Sebep:** `simplesoftwareio/simple-qrcode` kullanılıyor  
**Kullanım:**
- `app/Services/QRCodeService.php` - `use SimpleSoftwareIO\QrCode\Facades\QrCode;`
- `app/Http/Controllers/Api/QRCodeController.php`

**Sonuç:** ❌ KALDIRILABİLİR

**Kaldırma Komutu:**
```bash
composer remove bacon/bacon-qr-code
```

---

### 2. ❌ blade-ui-kit/blade-heroicons
**Durum:** Kullanılmıyor  
**Sebep:** View dosyalarında heroicons kullanımı bulunamadı  
**Kontrol:** `resources/views` dizininde `@heroicon` veya `heroicons` kullanımı yok

**Sonuç:** ❌ KALDIRILABİLİR

**Kaldırma Komutu:**
```bash
composer remove blade-ui-kit/blade-heroicons
```

---

### 3. ❌ blade-ui-kit/blade-icons
**Durum:** Kullanılmıyor  
**Sebep:** View dosyalarında blade-icons kullanımı bulunamadı  
**Kontrol:** `resources/views` dizininde `@bladeIcon` veya `blade-icons` kullanımı yok

**Sonuç:** ❌ KALDIRILABİLİR

**Kaldırma Komutu:**
```bash
composer remove blade-ui-kit/blade-icons
```

---

### 4. ❌ brick/math
**Durum:** Kullanılmıyor  
**Sebep:** Kod tabanında `Brick\Math` kullanımı bulunamadı  
**Kontrol:** `app` dizininde `use Brick\Math` veya `Brick\\Math` kullanımı yok

**Sonuç:** ❌ KALDIRILABİLİR

**Kaldırma Komutu:**
```bash
composer remove brick/math
```

---

### 5. ❌ carbonphp/carbon-doctrine-types
**Durum:** Kullanılmıyor  
**Sebep:** Doctrine ile Carbon entegrasyonu kullanılmıyor  
**Kontrol:** `app` dizininde `CarbonDoctrine` veya `carbon-doctrine` kullanımı yok

**Sonuç:** ❌ KALDIRILABİLİR

**Kaldırma Komutu:**
```bash
composer remove carbonphp/carbon-doctrine-types
```

---

### 6. ❌ dasprid/enum
**Durum:** Kullanılmıyor  
**Sebep:** Laravel'in built-in enum'u kullanılıyor  
**Kullanım:**
- `app/Enums/AnaKategori.php` - `enum AnaKategori: string`
- `app/Enums/YayinTipi.php` - `enum YayinTipi: string`

**Sonuç:** ❌ KALDIRILABİLİR

**Kaldırma Komutu:**
```bash
composer remove dasprid/enum
```

---

## ⚠️ DEPENDENCY PAKETLER (2 adet)

### 1. ⚠️ composer/pcre
**Durum:** Dependency  
**Sebep:** Diğer paketlerin bağımlılığı  
**Not:** Bu paket direkt kullanılmaz, diğer paketler tarafından gerektirilir.

**Sonuç:** ⚠️ KALDIRILAMAZ (Dependency)

---

### 2. ⚠️ composer/semver
**Durum:** Dependency  
**Sebep:** Diğer paketlerin bağımlılığı  
**Not:** Bu paket direkt kullanılmaz, diğer paketler tarafından gerektirilir.

**Sonuç:** ⚠️ KALDIRILAMAZ (Dependency)

---

## 📋 ÖNERİLER

### Kaldırılabilir Paketler (6 adet)

```bash
# Tüm kullanılmayan paketleri kaldır
composer remove \
    bacon/bacon-qr-code \
    blade-ui-kit/blade-heroicons \
    blade-ui-kit/blade-icons \
    brick/math \
    carbonphp/carbon-doctrine-types \
    dasprid/enum
```

### Kaldırma Sonrası Kontrol

```bash
# Composer autoload'u güncelle
composer dump-autoload

# Test çalıştır
php artisan test

# Lint kontrolü
composer lint
```

---

## 📊 İSTATİSTİKLER

- **Toplam Paket:** 10 adet
- **Kullanılan:** 2 adet (%20)
- **Kullanılmayan:** 6 adet (%60)
- **Dependency:** 2 adet (%20)

### Kaldırma Potansiyeli
- **Kaldırılabilir:** 6 paket
- **Tutulmalı:** 2 paket
- **Kaldırılamaz:** 2 paket (dependency)

---

## ✅ SONUÇ

**6 paket kaldırılabilir:**
1. bacon/bacon-qr-code
2. blade-ui-kit/blade-heroicons
3. blade-ui-kit/blade-icons
4. brick/math
5. carbonphp/carbon-doctrine-types
6. dasprid/enum

**2 paket tutulmalı:**
1. barryvdh/laravel-dompdf (PDF export)
2. darkaonline/l5-swagger (API dokümantasyonu)

**2 paket kaldırılamaz:**
1. composer/pcre (dependency)
2. composer/semver (dependency)

---

**Son Güncelleme:** 2025-11-11 21:10  
**Durum:** ✅ DEPENDENCY ISSUES ANALİZİ TAMAMLANDI

