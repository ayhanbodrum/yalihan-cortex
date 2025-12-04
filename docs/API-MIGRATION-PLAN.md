# API Endpoint Migration Plan

**Context7 Standard:** C7-API-MIGRATION-2025-12-03  
**Version:** 1.0.0  
**Status:** 📋 Plan - Uygulanacak

---

## 🎯 Amaç

Eski hardcoded API endpoint'lerini yeni merkezi API yönetim sistemine geçirmek.

---

## 📋 Tespit Edilen Dosyalar

### 1. JavaScript Dosyaları (public/js/)

1. ✅ `context7-live-search.js` - Zaten güncellendi
2. ⏳ `context7.js`
3. ⏳ `context7-live-search-simple.js`
4. ⏳ `admin/event-booking-manager.js`
5. ⏳ `modules/ilan-form.js`
6. ⏳ `admin/location-map-helper.js`
7. ⏳ `address-select.js`
8. ⏳ `admin/smart-calculator.js`
9. ⏳ `context7-features-system.js`
10. ⏳ `ilan-create-fixes.js`

### 2. JavaScript Dosyaları (resources/js/)

1. ⏳ `admin/ilan-create/tkgm-autofill.js`
2. ⏳ `admin/ilan-create/location.js`
3. ⏳ `valuation-dashboard.js`
4. ⏳ `pwa.js`
5. ⏳ `performance.js`
6. ⏳ `ilan-create-fixes.js`
7. ⏳ `components/ilan/portal-manager.js`
8. ⏳ `components/UnifiedPersonSelector.js`
9. ⏳ `components/LocationSystemTester.js`
10. ⏳ `components/LocationManager.js`

### 3. PHP Controller Dosyaları

1. ⏳ `app/Http/Controllers/AI/AdvancedAIController.php`
2. ⏳ `app/Http/Controllers/Api/TelegramWebhookController.php`
3. ⏳ `app/Http/Controllers/Api/ListingSearchController.php`

---

## 🔄 Migration Süreci

### Adım 1: Hardcoded Endpoint'leri Tespit Et

```bash
# Tüm hardcoded endpoint'leri bul
grep -r "'/api/\|"/api/" public/js/ resources/js/ app/Http/Controllers/ --include="*.js" --include="*.php"
```

### Adım 2: Config Dosyalarına Eksik Endpoint'leri Ekle

1. `config/api-endpoints.php`'ye ekle
2. `public/js/api-config.js`'ye ekle

### Adım 3: Kodları Güncelle

**Önce:**
```javascript
fetch('/api/location/districts/48')
```

**Sonra:**
```javascript
fetch(window.APIConfig.location.districts(48))
```

### Adım 4: Test Et

```bash
php artisan api:validate-routes
php artisan api:test-endpoints
```

---

## 📝 Migration Checklist

### public/js/ Dosyaları

- [ ] `context7.js`
- [ ] `context7-live-search-simple.js`
- [ ] `admin/event-booking-manager.js`
- [ ] `modules/ilan-form.js`
- [ ] `admin/location-map-helper.js`
- [ ] `address-select.js`
- [ ] `admin/smart-calculator.js`
- [ ] `context7-features-system.js`
- [ ] `ilan-create-fixes.js`

### resources/js/ Dosyaları

- [ ] `admin/ilan-create/tkgm-autofill.js`
- [ ] `admin/ilan-create/location.js`
- [ ] `valuation-dashboard.js`
- [ ] `pwa.js`
- [ ] `performance.js`
- [ ] `ilan-create-fixes.js`
- [ ] `components/ilan/portal-manager.js`
- [ ] `components/UnifiedPersonSelector.js`
- [ ] `components/LocationSystemTester.js`
- [ ] `components/LocationManager.js`

### PHP Controller Dosyaları

- [ ] `app/Http/Controllers/AI/AdvancedAIController.php`
- [ ] `app/Http/Controllers/Api/TelegramWebhookController.php`
- [ ] `app/Http/Controllers/Api/ListingSearchController.php`

---

## 🎯 Öncelik Sırası

1. **Yüksek Öncelik:** Sık kullanılan dosyalar
   - `context7.js`
   - `admin/ilan-create/location.js`
   - `components/LocationManager.js`

2. **Orta Öncelik:** Orta sıklıkta kullanılan dosyalar
   - `modules/ilan-form.js`
   - `admin/smart-calculator.js`
   - `components/UnifiedPersonSelector.js`

3. **Düşük Öncelik:** Az kullanılan dosyalar
   - `pwa.js`
   - `performance.js`
   - `components/LocationSystemTester.js`

---

## 📊 İlerleme Takibi

**Toplam Dosya:** 23  
**Tamamlanan:** 1 (context7-live-search.js)  
**Kalan:** 22  
**İlerleme:** %4.3

---

## 🔧 Otomatik Migration Script

Gelecekte otomatik migration için script oluşturulabilir:

```bash
php artisan api:migrate-endpoints
```

Bu script:
1. Hardcoded endpoint'leri tespit eder
2. Config dosyalarına ekler
3. Kodları otomatik günceller
4. Test eder

---

**Last Updated:** 2025-12-03  
**Maintainer:** Context7 System

