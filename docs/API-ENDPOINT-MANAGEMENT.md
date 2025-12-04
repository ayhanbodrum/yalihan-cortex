# API Endpoint Management System

**Context7 Standard:** C7-API-ENDPOINT-MANAGEMENT-2025-12-03  
**Version:** 1.0.0  
**Status:** ✅ Production Ready

---

## 🎯 Amaç

API endpoint'lerin sürekli sorun vermesini önlemek için kalıcı bir yönetim sistemi oluşturuldu.

## 📋 Sorunlar ve Çözümler

### ❌ Önceki Sorunlar

1. **Route Çakışmaları**: Aynı route'lar farklı yerlerde tanımlanmış
2. **Hardcoded Endpoint'ler**: JavaScript'te endpoint'ler doğrudan yazılmış
3. **Response Format Tutarsızlıkları**: Bazı endpoint'ler ResponseService kullanmıyor
4. **Dokümantasyon Eksikliği**: Hangi endpoint'in nerede olduğu net değil
5. **Test Eksikliği**: Endpoint'lerin çalışıp çalışmadığı kontrol edilmiyor

### ✅ Çözümler

1. **Merkezi Endpoint Registry**: `config/api-endpoints.php`
2. **JavaScript API Config**: `public/js/api-config.js`
3. **Route Validator**: `php artisan api:validate-routes`
4. **API Documentation Generator**: `php artisan api:generate-docs`
5. **Endpoint Health Check**: `php artisan api:test-endpoints`

---

## 🏗️ Sistem Yapısı

### 1. Backend: PHP Config (`config/api-endpoints.php`)

Tüm endpoint'ler merkezi bir config dosyasında tanımlanır:

```php
return [
    'location' => [
        'districts' => '/api/location/districts/{id}',
        'neighborhoods' => '/api/location/neighborhoods/{id}',
    ],
    'categories' => [
        'subcategories' => '/api/categories/sub/{parentId}',
    ],
    // ...
];
```

### 2. Frontend: JavaScript Config (`public/js/api-config.js`)

JavaScript'te endpoint'ler merkezi config'den alınır:

```javascript
// ❌ YANLIŞ (Hardcoded)
fetch('/api/location/districts/48')

// ✅ DOĞRU (Merkezi config)
fetch(window.APIConfig.location.districts(48))
```

### 3. Route Validator

Route çakışmalarını otomatik tespit eder:

```bash
php artisan api:validate-routes
```

**Çıktı:**
```
✅ No conflicts or issues found!
   Total API routes: 175
```

### 4. API Documentation Generator

Otomatik dokümantasyon oluşturur:

```bash
php artisan api:generate-docs
```

**Çıktı:** `docs/api-endpoints.md`

### 5. Endpoint Health Check

Tüm endpoint'leri test eder:

```bash
php artisan api:test-endpoints
```

**Çıktı:**
```
📊 Test Results:
   Total: 50
   ✅ Passed: 48
   ❌ Failed: 2
```

---

## 📖 Kullanım Kılavuzu

### Yeni Endpoint Ekleme

1. **Backend Config'e Ekle** (`config/api-endpoints.php`):
```php
'my_module' => [
    'my_endpoint' => '/api/my-module/endpoint/{id}',
],
```

2. **JavaScript Config'e Ekle** (`public/js/api-config.js`):
```javascript
myModule: {
    myEndpoint: (id) => `/api/my-module/endpoint/${id}`,
},
```

3. **Route Ekle** (`routes/api.php` veya `routes/api/v1/*.php`):
```php
Route::get('/my-module/endpoint/{id}', [MyController::class, 'method']);
```

4. **Validate Et**:
```bash
php artisan api:validate-routes
```

### JavaScript'te Kullanım

**Önceki Yöntem (Yasak):**
```javascript
// ❌ Hardcoded endpoint
fetch('/api/location/districts/48')
```

**Yeni Yöntem (Zorunlu):**
```javascript
// ✅ Merkezi config kullan
fetch(window.APIConfig.location.districts(48))
```

### Parametreli Endpoint'ler

```javascript
// Tek parametre
window.APIConfig.location.districts(48)

// Çoklu parametre
window.APIConfig.location.nearby(39.9, 32.8, 1000)

// Optional parametre
window.APIConfig.categories.fields(categoryId, publicationTypeId)
```

---

## 🔧 Maintenance Commands

### Route Validation
```bash
php artisan api:validate-routes
```
- Route çakışmalarını tespit eder
- URI sorunlarını bulur (double slash, trailing slash)
- CI/CD pipeline'a eklenebilir

### Documentation Generation
```bash
php artisan api:generate-docs
php artisan api:generate-docs --output=docs/custom-api-docs.md
```
- Otomatik markdown dokümantasyon oluşturur
- Kategorilere göre organize eder
- Her endpoint için method, URI, controller bilgisi

### Health Check
```bash
php artisan api:test-endpoints
php artisan api:test-endpoints --base-url=http://staging.example.com
```
- Tüm GET endpoint'lerini test eder
- HTTP status kodlarını kontrol eder
- Başarısız endpoint'leri raporlar

---

## 📊 Best Practices

### 1. Endpoint Naming

✅ **DOĞRU:**
- `/api/location/districts/{id}`
- `/api/categories/sub/{parentId}`
- `/api/kisiler/search`

❌ **YANLIŞ:**
- `/api/location/getDistricts/{id}` (get prefix gereksiz)
- `/api/categories/subcategories/{parentId}` (plural/singular karışıklığı)
- `/api/kisi/search` (inconsistent plural)

### 2. Response Format

**ZORUNLU:** Tüm endpoint'ler `ResponseService` kullanmalı:

```php
// ✅ DOĞRU
return ResponseService::success($data, 'Mesaj');

// ❌ YANLIŞ
return response()->json(['data' => $data]);
```

### 3. JavaScript Usage

**ZORUNLU:** Tüm endpoint'ler `window.APIConfig`'den alınmalı:

```javascript
// ✅ DOĞRU
fetch(window.APIConfig.location.districts(ilId))

// ❌ YANLIŞ
fetch(`/api/location/districts/${ilId}`)
```

---

## 🚨 Migration Guide

### Mevcut Kodları Güncelleme

1. **JavaScript Dosyalarını Bul:**
```bash
grep -r "/api/" public/js/ resources/js/
```

2. **Hardcoded Endpoint'leri Değiştir:**
```javascript
// Önce
fetch('/api/location/districts/48')

// Sonra
fetch(window.APIConfig.location.districts(48))
```

3. **Test Et:**
```bash
php artisan api:validate-routes
php artisan api:test-endpoints
```

---

## 📈 Monitoring

### CI/CD Integration

```yaml
# .github/workflows/api-validation.yml
- name: Validate API Routes
  run: php artisan api:validate-routes

- name: Test API Endpoints
  run: php artisan api:test-endpoints
```

### Scheduled Checks

```php
// app/Console/Kernel.php
$schedule->command('api:validate-routes')
    ->daily()
    ->at('02:00');
```

---

## 📚 Related Documentation

- [API Routes Structure](routes/api/README.md)
- [Context7 Standards](.context7/authority.json)
- [ResponseService Documentation](docs/ResponseService.md)

---

**Last Updated:** 2025-12-03  
**Maintainer:** Context7 System

