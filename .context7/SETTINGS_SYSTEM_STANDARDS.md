# 📋 Site Ayarları Sistemi Standartları

**Context7 Standard:** `C7-SETTINGS-MERGE-2025-11-05`  
**Tarih:** 5 Kasım 2025  
**Versiyon:** 1.0.0  
**Durum:** ✅ AKTIF - ZORUNLU

---

## 🎯 AMAÇ

Site ayarları sistemindeki duplikasyonları kaldırmak ve **tek kaynak prensibi** (Single Source of Truth) uygulamak.

---

## ✅ ZORUNLU STANDARTLAR

### 1. **Tek Model: Setting**

```php
// ✅ DOĞRU
use App\Models\Setting;

$value = Setting::get('site_name');
Setting::set('site_name', 'Yalıhan Emlak', 'general');

// ❌ YANLIŞ
use App\Models\SiteSetting; // DEPRECATED
SiteSetting::getValue('site_name'); // DEPRECATED
```

### 2. **Tek Controller: AyarlarController**

```php
// ✅ DOĞRU
Route::prefix('/ayarlar')->name('ayarlar.')->group(function () {
    Route::get('/', [AyarlarController::class, 'index']);
});

// ❌ YANLIŞ
SettingsController // DEPRECATED - Kaldırıldı
UserSettingsController // DEPRECATED - Kaldırıldı
```

### 3. **AI Ayarları Ayrı Sayfa**

```php
// ✅ DOĞRU
Route::prefix('/ai-settings')->name('ai-settings.')->group(function () {
    Route::get('/', [AISettingsController::class, 'index']);
});

// admin/ayarlar sayfasından link ile yönlendirilir
<a href="{{ route('admin.ai-settings.index') }}">AI & Yapay Zeka</a>
```

### 4. **Helper Fonksiyonlar**

```php
// ✅ DOĞRU
setting('site_name', 'Default');
setting_set('site_name', 'Yalıhan Emlak', 'general');
setting_group('general');

// Helper fonksiyonlar Setting modelini kullanır
// app/Helpers/settings_helper.php
```

---

## 🚫 YASAK PATTERN'LER

### Deprecated Models

```php
❌ SiteSetting::getValue($key, $default)
✅ Setting::get($key, $default)

❌ SiteSetting::setValue($key, $value, $group)
✅ Setting::set($key, $value, $group, $type, $description)

❌ SiteSetting::getGroup($group)
✅ Setting::getByGroup($group)
```

### Deprecated Controllers

```php
❌ SettingsController
❌ UserSettingsController
✅ AyarlarController (kullanılmalı)
```

### Deprecated Routes

```php
❌ admin.settings.update
❌ admin.settings.location
❌ admin.user-settings.*
✅ admin.ayarlar.* (kullanılmalı)
```

### Deprecated Database

```php
❌ DB::table('site_settings')
✅ Setting:: (Eloquent model)
```

---

## 📚 MİGRASYON REHBERİ

### SiteSetting → Setting

| Eski Kod | Yeni Kod |
|----------|----------|
| `SiteSetting::getValue($key, $default)` | `Setting::get($key, $default)` |
| `SiteSetting::setValue($key, $value, $group)` | `Setting::set($key, $value, $group, $type, $description)` |
| `SiteSetting::getGroup($group)` | `Setting::getByGroup($group)` |
| `SiteSetting::getPublicSettings()` | `Setting::where('group', 'public')->get()` |

### DB::table() → Eloquent

| Eski Kod | Yeni Kod |
|----------|----------|
| `DB::table('site_settings')->where('key', $key)->value('value')` | `Setting::get($key)` |
| `DB::table('site_settings')->updateOrInsert(...)` | `Setting::set($key, $value, $group, $type, $description)` |

---

## 🔄 VERİTABANI MİGRASYONU

### Önemli Not

`site_settings` tablosundaki veriler `settings` tablosuna migrate edilmeli.

**Production'da manuel migration gerekebilir:**

```sql
-- site_settings → settings migration
INSERT INTO settings (key, value, type, description, group, created_at, updated_at)
SELECT key, value, type, description, COALESCE(group, 'general'), created_at, updated_at
FROM site_settings
ON DUPLICATE KEY UPDATE 
    value = VALUES(value),
    updated_at = VALUES(updated_at);
```

---

## 📁 DOSYA YAPISI

```
app/
├── Models/
│   ├── Setting.php ✅ (AKTIF)
│   └── SiteSetting.php ⚠️ (DEPRECATED - wrapper methods)
├── Http/Controllers/Admin/
│   ├── AyarlarController.php ✅ (AKTIF)
│   ├── AISettingsController.php ✅ (AKTIF - AI ayarları)
│   ├── SettingsController.php ❌ (KALDIRILDI)
│   └── UserSettingsController.php ❌ (KALDIRILDI)
└── Helpers/
    └── settings_helper.php ✅ (AKTIF)

routes/
└── admin.php
    ├── admin.ayarlar.* ✅ (AKTIF)
    ├── admin.ai-settings.* ✅ (AKTIF)
    ├── admin.settings.* ❌ (KALDIRILDI)
    └── admin.user-settings.* ❌ (KALDIRILDI)
```

---

## 🎯 ENFORCEMENT

**Seviye:** STRICT

- ✅ Yeni kodda SiteSetting kullanımı YASAK
- ✅ Placeholder controller oluşturulmamalı
- ✅ Route duplikasyonları önlenmeli
- ✅ DB::table() yerine Eloquent kullanılmalı

**Otomatik Kontrol:**
- Pre-commit hook'larında kontrol edilmeli
- Code review'da kontrol edilmeli
- Yalıhan Bekçi otomatik kontrol yapmalı

---

## 📖 REFERANSLAR

- **Authority File:** `.context7/authority.json` → `settings_system_standards_2025_11_05`
- **Knowledge Base:** `.yalihan-bekci/knowledge/settings-system-merge-2025-11-05.json`
- **Standard:** `C7-SETTINGS-MERGE-2025-11-05`

---

**Son Güncelleme:** 5 Kasım 2025  
**Versiyon:** 1.0.0

