# 🔧 Status Kolonu Global Standart

**Versiyon:** 1.0.0  
**Tarih:** 22 Kasım 2025  
**Durum:** ✅ AKTİF - PERMANENT STANDART

---

## 📋 Standart Tanımı

### Database

```sql
status TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0=inactive, 1=active (Context7 boolean)'
```

### Model

```php
protected $casts = [
    'status' => 'boolean',
];
```

### Code Usage

```php
// ✅ DOĞRU
where('status', true)
where('status', false)
update(['status' => true])
update(['status' => false])

// ❌ YANLIŞ (YASAK)
where('status', 'Aktif')
where('status', 'Pasif')
update(['status' => 'Aktif'])
update(['status' => 'Pasif'])
```

---

## 🚫 Yasak Pattern'ler

### Database Types

- ❌ `VARCHAR(255)`
- ❌ `VARCHAR(50)`
- ❌ `ENUM('Aktif','Pasif')`
- ❌ `STRING`

### Code Values

- ❌ `'Aktif'`
- ❌ `'Pasif'`
- ❌ `'active'`
- ❌ `'inactive'`
- ❌ `where('status', 'Aktif')`
- ❌ `update(['status' => 'Aktif'])`

---

## ✅ Zorunlu Pattern'ler

### Yeni Tablolar İçin

```php
// Migration
$table->tinyInteger('status')->default(1)->comment('0=inactive, 1=active');

// Model
protected $casts = ['status' => 'boolean'];

// Code
where('status', true)
update(['status' => true])
```

---

## ⚠️ İstisnalar

Karmaşık status'lar (çoklu durum) için VARCHAR veya ENUM kullanılabilir:

- `blog_posts`: 'draft', 'published', 'scheduled'
- `eslesmeler`: 'beklemede', 'eslesti', 'iptal'
- `gorevler`: 'Beklemede', 'Devam Ediyor', 'Tamamlandi'
- `yazlik_rezervasyonlar`: 'beklemede', 'onaylandi', 'iptal', 'tamamlandi'
- `sites`: 'active', 'inactive', 'pending'

**Kural:** Sadece aktif/pasif durumu olan tablolar boolean olmalı.

---

## 📊 Migration Sonuçları

**Düzeltilen Tablolar:** 16 tablo

**Önce:**
- VARCHAR(255) + 'Aktif'/'Pasif': 10 tablo
- ENUM('Aktif','Pasif'): 6 tablo

**Sonra:**
- TINYINT(1) boolean: 36 tablo (tüm basit status'lar)

---

## 🎯 Faydalar

- ✅ IDE'ler (trea, warp, cursor) tutarlı tip kontrolü yapabilir
- ✅ Kodda her yerde true/false kullanılabilir
- ✅ 'Aktif'/'Pasif' string karışıklığı olmaz
- ✅ Context7 standartlarına %100 uyumlu
- ✅ Bakım kolaylığı

---

## 📚 Referans

- `.context7/authority.json` - Global standart tanımı
- `yalihan-bekci/knowledge/status-column-global-standard-2025-11-22.json` - Bekçi bilgi tabanı
- `database/migrations/2025_11_22_152526_fix_all_status_columns_to_boolean_global_fix.php` - Migration

---

**Son Güncelleme:** 22 Kasım 2025  
**Enforcement:** STRICT - Yeni tablolarda status kolonu MUTLAKA TINYINT(1) olmalı

