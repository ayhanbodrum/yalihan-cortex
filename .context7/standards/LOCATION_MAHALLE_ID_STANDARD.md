# 🗺️ Location System: mahalle_id Standardı

**Tarih:** 31 Ekim 2025  
**Versiyon:** Context7 v3.5.0  
**Öncelik:** 🔴 CRITICAL  
**Durum:** ✅ ACTIVE - Zorunlu

---

## 📋 Özet

Location sisteminde mahalle alanı için **SADECE `mahalle_id` kullanılır**. `semt_id` kullanımı **YASAK**.

---

## 🚫 Yasaklanan Pattern'ler

```javascript
// ❌ YANLIŞ - KULLANMAYIN
document.getElementById('semt_id');
const semtSelect = document.getElementById('semt_id');
$('#semt_id');
name = 'semt_id';
id = 'semt_id';
```

---

## ✅ Doğru Kullanım

```javascript
// ✅ DOĞRU - KULLANIN
document.getElementById('mahalle_id');
const mahalleSelect = document.getElementById('mahalle_id');
name = 'mahalle_id';
id = 'mahalle_id';
```

### Blade Dosyalarında:

```html
<select name="mahalle_id" id="mahalle_id" data-context7-field="mahalle_id">
    <option value="">Mahalle Seçin...</option>
</select>
```

---

## 🗄️ Database Durumu

| Tablo     | mahalle_id | Durum                             |
| --------- | ---------- | --------------------------------- |
| `ilanlar` | ✅ VAR     | Foreign key: mahalleler.id        |
| `kisiler` | ❌ YOK     | By design (sadece il_id, ilce_id) |
| `sites`   | ✅ VAR     | Foreign key: mahalleler.id        |

---

## 🔧 Düzeltilen Dosyalar

### 1. resources/js/admin/ilan-create/location.js

**7 Fonksiyon Düzeltildi:**

1. **updateFormValues()**

    ```javascript
    // ❌ Önce: const semtSelect = document.getElementById('semt_id')
    // ✅ Sonra: const mahalleSelect = document.getElementById('mahalle_id')
    ```

2. **clearIlceler()**

    ```javascript
    // ❌ Önce: const semtSelect = document.getElementById('semt_id')
    // ✅ Sonra: const mahalleSelect = document.getElementById('mahalle_id')
    ```

3. **populateIlceler()**

    ```javascript
    // ❌ Önce: const semtSelect = document.getElementById('semt_id')
    // ✅ Sonra: const mahalleSelect = document.getElementById('mahalle_id')
    ```

4. **clearSemtler()**

    ```javascript
    // ❌ Önce: const semtSelect = document.getElementById('semt_id')
    // ✅ Sonra: const mahalleSelect = document.getElementById('mahalle_id')
    ```

5. **populateSemtler()**

    ```javascript
    // ❌ Önce: const semtSelect = document.getElementById('semt_id')
    // ✅ Sonra: const mahalleSelect = document.getElementById('mahalle_id')
    ```

6. **advancedLocationManager() - Instance 1** (satır 834-880)

    ```javascript
    // ❌ Önce: selectedSemt, semtler
    // ✅ Sonra: selectedMahalle, mahalleler
    ```

7. **advancedLocationManager() - Instance 2** (satır 1279-1332)
    ```javascript
    // ❌ Önce: selectedSemt, semtler
    // ✅ Sonra: selectedMahalle, mahalleler
    ```

---

## 🌐 API Endpoints

```bash
# ✅ Mahalle listesi getir
GET /api/location/neighborhoods/{ilce_id}

# Response format
{
  "success": true,
  "data": [
    {
      "id": 1,
      "mahalle": "Merkez Mahalle",
      "name": "Merkez Mahalle"  // API field: mahalle
    }
  ]
}
```

---

## 🧪 Test Senaryosu

### Test Adımları:

1. İlan oluşturma sayfasını aç: `/admin/ilanlar/create`
2. **İl** seçimi yap → İlçeler dropdown'u dolsun
3. **İlçe** seçimi yap → **Mahalleler dropdown'u dolsun** ✅
4. Console'da hata kontrol et → **"mahalle_id elementi bulunamadı" ÇIKMAMALI** ✅

### Beklenen Davranış:

- ✅ İlçe seçildiğinde mahalle dropdown populate olmalı
- ✅ Mahalle dropdown enabled olmalı
- ❌ Console'da "semt_id elementi bulunamadı" hatası OLMAMALI
- ✅ `/api/location/neighborhoods/{ilce_id}` API çağrısı yapılmalı

---

## 📍 Dosya Konumları

```bash
# Context7 Kurallar
.context7/authority.json (v3.5.0)
.context7/LOCATION_MAHALLE_ID_STANDARD.md (bu dosya)

# Yalıhan Bekçi Knowledge Base
yalihan-bekci/knowledge/location-mahalle-id-standard.json

# Kod Dosyaları
resources/js/admin/ilan-create/location.js
resources/views/admin/ilanlar/components/location-map.blade.php

# Database Migrations
database/migrations/2025_10_10_073304_create_ilanlar_table.php
database/migrations/2025_10_16_220234_create_sites_table.php
database/migrations/2025_10_22_160000_create_site_apartmanlar_table.php
```

---

## 🎯 Enforcement

### Pre-Commit Hook

```bash
# Check for 'semt_id' usage
grep -r "semt_id" resources/js/admin/ilan-create/location.js && exit 1
grep -r "semt_id" resources/views/admin/ilanlar/components/ && exit 1
```

### Linter Rule

```json
{
    "rules": {
        "no-semt-id": {
            "pattern": "getElementById\\('semt_id'\\)",
            "message": "Context7 İhlali: 'semt_id' kullanımı yasak, 'mahalle_id' kullanın",
            "severity": "error"
        }
    }
}
```

### Code Review Checklist

- [ ] `semt_id` kullanımı yok mu?
- [ ] `mahalle_id` tutarlı kullanılmış mı?
- [ ] Console hatası temiz mi?
- [ ] API endpoint doğru mu?

---

## 🚨 Kritik Notlar

### 1. Kisiler Tablosu

- **kisiler** tablosunda `mahalle_id` kolonu **YOK** (by design)
- Sadece `il_id` ve `ilce_id` var
- CRM kişiler için mahalle seviyesi detay ZORUNLU DEĞİL

### 2. İlanlar & Sites Tablosu

- **ilanlar** ve **sites** tablolarında `mahalle_id` kolonu **VAR**
- Foreign key constraint: `mahalleler.id`
- Mahalle seçimi yapılabilir (nullable)

### 3. Terminoloji

- ✅ Türkçe: **Mahalle** (standart)
- ❌ Eski: **Semt** (deprecated)
- Database kolon: `mahalle_id`
- Database tablo: `mahalleler`
- API field: `mahalle`

---

## 📚 İlgili Dökümanlar

- Context7 Authority: `.context7/authority.json`
- Location System: `resources/js/admin/ilan-create/location.js`
- API Routes: `routes/api.php`
- Yalıhan Bekçi: `yalihan-bekci/knowledge/location-mahalle-id-standard.json`

---

## 📅 Tarihçe

| Tarih      | Versiyon | Değişiklik                                              |
| ---------- | -------- | ------------------------------------------------------- |
| 2025-10-31 | v3.5.0   | mahalle_id standardı belirlendi, 7 fonksiyon düzeltildi |
| 2025-10-31 | -        | Yalıhan Bekçi'ye bildirildi, kurallara eklendi          |
| 2025-10-31 | -        | Cursor Memory'ye kaydedildi, MCP'ler öğrendi            |

---

## ✅ Kontrol Listesi

- [x] location.js dosyası düzeltildi (7 fonksiyon)
- [x] Build başarılı (npm run build)
- [x] Yalıhan Bekçi bilgilendirildi
- [x] Context7 Authority güncellendi
- [x] Cursor Memory kaydedildi
- [x] Dokümantasyon oluşturuldu
- [ ] Production test yapıldı
- [ ] Tüm sayfalarda kontrol edildi

---

**Son Güncelleme:** 31 Ekim 2025  
**Hazırlayan:** Context7 AI Assistant  
**Doğrulayan:** Yalıhan Bekçi AI Guardian System
