# 📚 Yalıhan Bekçi Öğrenme Raporu - 11 Kasım 2025

**Tarih:** 11 Kasım 2025  
**Versiyon:** Context7 v5.4.0  
**Öğrenme Tipi:** Context7 Uyumluluk + Bug Fix

---

## 🎯 ÖĞRENİLEN KONULAR

### 1. Telescope Context7 Uyumluluk Düzeltmeleri

**Konu:** Laravel Telescope'un config ve service provider'ı Context7 standartlarına uyumlu hale getirildi.

**Öğrenilen Pattern'ler:**
- ✅ Telescope config Context7 uyumluluğu
- ✅ Hassas veriler gizleme pattern'i (`password`, `api_token`, `secret`)
- ✅ ignore_paths performans optimizasyonu (`telescope*`, `horizon*`)
- ✅ Spatie Permission gate kontrolü
- ✅ Local vs Production farklı davranış pattern'i

**Dosyalar:**
- `config/telescope.php`
- `app/Providers/TelescopeServiceProvider.php`

**Knowledge Base:** `.yalihan-bekci/knowledge/telescope-context7-compliance-2025-11-11.json`

---

### 2. Kişi Ekleme Sayfası kisi_tipi Hatası Düzeltmesi

**Konu:** Kişi ekleme sayfasında `kisi_tipi` field'ının NOT NULL constraint hatası düzeltildi.

**Hata:**
```
SQLSTATE[23000]: Integrity constraint violation: 1048 Column 'kisi_tipi' cannot be null
```

**Öğrenilen Pattern'ler:**
- ✅ Required field validation pattern'i (controller + form)
- ✅ Default değer fallback pattern'i (Service layer)
- ✅ Context7 field naming standardı (`adres` → `adres_detay`)
- ✅ Veritabanı constraint ile validation senkronizasyonu

**Dosyalar:**
- `app/Http/Controllers/Admin/KisiController.php`
- `resources/views/admin/kisiler/create.blade.php`
- `app/Modules/Crm/Services/KisiService.php`

**Knowledge Base:** `.yalihan-bekci/knowledge/kisi-tipi-required-fix-2025-11-11.json`

---

## 🔍 ÖĞRENİLEN PATTERN'LER

### 1. **Telescope Hassas Veriler Gizleme Pattern'i**

```php
// ✅ Context7: Hassas request parametrelerini gizle
Telescope::hideRequestParameters([
    '_token',
    'password',
    'password_confirmation',
    'api_token',
    'secret',
]);

// ✅ Context7: Hassas header'ları gizle
Telescope::hideRequestHeaders([
    'cookie',
    'x-csrf-token',
    'x-xsrf-token',
    'authorization',
    'api-key',
]);
```

**Kullanım Senaryosu:** Production environment'te hassas verilerin Telescope'da görünmesini önlemek.

---

### 2. **Required Field Validation Pattern'i**

```php
// Controller
'kisi_tipi' => 'required|string|in:Müşteri,Potansiyel,...'

// Form
<select name="kisi_tipi" required>
    <option value="">Seçin...</option>
    ...
</select>

// Service (Fallback)
if (empty($data['kisi_tipi'])) {
    $data['kisi_tipi'] = 'Müşteri'; // Default değer
}
```

**Kullanım Senaryosu:** Veritabanı NOT NULL constraint'i olan field'lar için üç katmanlı koruma (validation + form + service).

---

### 3. **Context7 Field Naming Standardı**

```php
// ❌ YASAK
'adres' => 'nullable|string'

// ✅ DOĞRU
'adres_detay' => 'nullable|string'
```

**Kullanım Senaryosu:** Database column adı ile form field adının eşleşmesi gerektiğinde Context7 standardına uygun field adı kullanılmalı.

---

### 4. **ignore_paths Performans Optimizasyonu**

```php
'ignore_paths' => [
    'livewire*',
    'nova-api*',
    'pulse*',
    '_boost*',
    'telescope*', // Telescope kendi isteklerini ignore et
    'horizon*',   // Horizon isteklerini ignore et
],
```

**Kullanım Senaryosu:** Gereksiz log kayıtlarını önlemek ve performansı artırmak için monitoring tool'larının kendi isteklerini ignore etmesi.

---

### 5. **Spatie Permission Gate Kontrolü**

```php
Gate::define('viewTelescope', function ($user = null) {
    if ($this->app->environment('local')) {
        return true;
    }
    
    if (!$user) {
        return false;
    }
    
    return $user->hasRole(['superadmin', 'admin']) ||
           $user->email === config('app.admin_email');
});
```

**Kullanım Senaryosu:** Production environment'te rol tabanlı erişim kontrolü için Spatie Permission entegrasyonu.

---

## 📊 CONTEXT7 UYUMLULUK KURALLARI

### ✅ Yasak Pattern'ler (Kontrol Edildi)

- `durum` → `status` kullanılmalı
- `is_active` → `status` kullanılmalı
- `aktif` → `status` kullanılmalı
- `sehir` → `il` kullanılmalı
- `musteri_tipi` → `kisi_tipi` kullanılmalı
- `adres` → `adres_detay` kullanılmalı (database column name)
- `btn-*`, `card-*`, `form-control` → Tailwind CSS utility classes kullanılmalı

### ✅ Zorunlu Pattern'ler (Kullanılıyor)

- `status` field kullanımı
- `kisi_tipi` field kullanımı
- `adres_detay` field kullanımı
- Context7 yorumları (`✅ Context7:`)
- Spatie Permission entegrasyonu
- Tailwind CSS utility classes

---

## 🎓 ÖNEMLİ DERSLER

1. **Veritabanı Constraint Senkronizasyonu:**
   - Migration'larda nullable/required kontrolü yapılmalı
   - Controller validation'ları veritabanı constraint'leri ile senkronize tutulmalı
   - Form'da required attribute ve controller'da validation birlikte kullanılmalı

2. **Güvenlik Pattern'leri:**
   - Hassas veriler production environment'te gizlenmeli
   - Monitoring tool'larında hassas bilgiler loglanmamalı
   - Erişim kontrolü Spatie Permission ile yapılmalı

3. **Performans Optimizasyonu:**
   - Monitoring tool'larının kendi isteklerini ignore etmesi gereksiz log kayıtlarını önler
   - Local vs Production farklı davranış pattern'i performansı artırır

4. **Service Layer Fallback:**
   - Kritik field'lar için default değer fallback eklenmeli
   - Fallback durumunda warning log kaydedilmeli

---

## 📚 İLGİLİ DOKÜMANTASYON

- `.context7/TELESCOPE_CONTEXT7_COMPLIANCE_2025-11-11.md`
- `.context7/authority.json`
- `.context7/FORM_DESIGN_STANDARDS.md`
- `.yalihan-bekci/knowledge/telescope-context7-compliance-2025-11-11.json`
- `.yalihan-bekci/knowledge/kisi-tipi-required-fix-2025-11-11.json`

---

**Rapor Tarihi:** 11 Kasım 2025  
**Context7 Versiyon:** v5.4.0  
**Durum:** ✅ ÖĞRENME TAMAMLANDI

