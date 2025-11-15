# 🔧 STANDARDIZATION STANDARDS

**Context7 Code Standardization Guidelines**  
**Version:** 1.0.0  
**Last Updated:** 2025-11-05  
**Status:** ✅ ACTIVE

---

## 📋 GENEL BAKIŞ

Bu doküman, tüm kod standardizasyonu işlemlerini tanımlar. Cache, Error Handling ve Logging işlemleri için standart pattern'ler ve best practice'ler içerir.

---

## 🎯 STANDARDIZATION ALANLARI

### 1. ✅ CACHE STANDARDIZATION

#### Standart Service

- **Service:** `App\Services\Cache\CacheService`
- **Helper:** `App\Services\Cache\CacheHelper`

#### Kullanım Kuralları

**❌ YASAK:**

```php
Cache::remember('key', 3600, function() {});
Cache::get('key');
Cache::put('key', $value, 3600);
Cache::forget('key');
```

**✅ ZORUNLU:**

```php
use App\Services\Cache\CacheHelper;

// Cache key formatı: emlak_pro:{namespace}:{key}:{params}
CacheHelper::remember(
    'namespace',      // Örn: 'currency', 'ai', 'ilan'
    'key',            // Örn: 'tcmb_rates_today', 'provider_config'
    'medium',         // TTL preset: very_short, short, medium, long, very_long
    function() {      // Callback
        return $data;
    },
    ['param' => 'value'] // Optional params
);

CacheHelper::forget('namespace', 'key');
CacheHelper::get('namespace', 'key');
```

#### TTL Presets

| Preset       | Süre      | Kullanım Alanı             |
| ------------ | --------- | -------------------------- |
| `very_short` | 60 saniye | Geçici veriler, test       |
| `short`      | 5 dakika  | Hızlı değişen veriler      |
| `medium`     | 1 saat    | Normal cache verileri      |
| `long`       | 24 saat   | Günlük güncellenen veriler |
| `very_long`  | 7 gün     | Nadiren değişen veriler    |

#### Key Format Standardı

```
emlak_pro:{namespace}:{key}:{params}
```

**Örnekler:**

- `emlak_pro:currency:tcmb_rates_today`
- `emlak_pro:ai:provider_config`
- `emlak_pro:ilan:stats:user_123`

---

### 2. ✅ ERROR HANDLING STANDARDIZATION

#### Standart Service

- **Service:** `App\Services\Response\ResponseService`
- **Handler:** `App\Services\Response\ErrorHandlerService`

#### Kullanım Kuralları

**❌ YASAK:**

```php
return response()->json([
    'success' => false,
    'message' => 'Hata oluştu'
], 500);

return redirect()->back()->with('error', 'Hata oluştu');
```

**✅ ZORUNLU:**

```php
use App\Services\Response\ResponseService;

// API Response
try {
    // ... code ...
} catch (\Exception $e) {
    return ResponseService::serverError('Hata mesajı', $e);
}

// Web Response
try {
    // ... code ...
} catch (\Exception $e) {
    if ($request->expectsJson()) {
        return ResponseService::serverError('Hata mesajı', $e);
    }
    return ResponseService::backError('Hata mesajı: ' . $e->getMessage());
}
```

#### Response Formatları

**API Success:**

```json
{
    "success": true,
    "message": "İşlem başarılı",
    "data": {...},
    "timestamp": "2025-11-05T12:00:00Z"
}
```

**API Error:**

```json
{
    "success": false,
    "message": "Hata mesajı",
    "errors": {...},
    "code": "ERROR_CODE",
    "timestamp": "2025-11-05T12:00:00Z"
}
```

#### Exception Handling

```php
// Automatic logging included
ResponseService::serverError('Hata mesajı', $exception);

// With custom context
ResponseService::error('Hata mesajı', 400, [
    'field' => 'validation_error',
    'context' => 'additional_info'
]);
```

---

### 3. ✅ LOGGING STANDARDIZATION

#### Standart Service

- **Service:** `App\Services\Logging\LogService`
- **Helper:** `App\Services\Logging\LogHelper`

#### Kullanım Kuralları

**❌ YASAK:**

```php
Log::info('Mesaj');
Log::error('Hata: ' . $e->getMessage());
Log::warning('Uyarı', ['data' => $data]);
```

**✅ ZORUNLU:**

```php
use App\Services\Logging\LogService;

// Basic logging
LogService::info('İşlem başarılı', ['ilan_id' => 123]);
LogService::error('Hata oluştu', ['context' => '...'], $exception);
LogService::warning('Uyarı', ['data' => $data]);

// Specialized logging
LogService::api('/api/ilanlar', $requestData, $responseData, $duration);
LogService::database('create', 'ilanlar', $data, $affectedRows);
LogService::auth('login', $userId, ['ip' => $ip]);
LogService::ai('generate_description', 'openai', ['context' => '...'], $duration);

// Action logging
LogService::action('create', 'ilan', $ilanId, ['fiyat' => 1000000]);
```

#### Log Levels

| Level      | Kullanım            | Otomatik Context |
| ---------- | ------------------- | ---------------- |
| `debug`    | Development only    | ✅               |
| `info`     | Normal operations   | ✅               |
| `warning`  | Potansiyel sorunlar | ✅               |
| `error`    | Hatalar             | ✅               |
| `critical` | Kritik hatalar      | ✅               |

#### Automatic Context

Tüm log kayıtlarına otomatik olarak eklenir:

- `timestamp` - ISO 8601 format
- `url` - Request URL
- `method` - HTTP method
- `user_id` - Authenticated user ID
- `ip` - Client IP address

#### Log Channels

| Channel    | Kullanım                  |
| ---------- | ------------------------- |
| `stack`    | Default (tüm loglar)      |
| `api`      | API request/response logs |
| `database` | Database operations       |
| `auth`     | Authentication events     |
| `payment`  | Payment transactions      |
| `ai`       | AI operations             |

---

## 🔄 MIGRATION PATTERN

### Adım 1: Import Ekle

```php
use App\Services\Cache\CacheHelper;
use App\Services\Response\ResponseService;
use App\Services\Logging\LogService;
```

### Adım 2: Cache Migration

```php
// ÖNCE
return Cache::remember('key', 3600, function() {
    return $data;
});

// SONRA
return CacheHelper::remember(
    'namespace',
    'key',
    'medium', // 1 hour
    function() {
        return $data;
    }
);
```

### Adım 3: Error Handling Migration

```php
// ÖNCE
try {
    // ... code ...
} catch (\Exception $e) {
    Log::error('Hata: ' . $e->getMessage());
    return response()->json(['error' => 'Hata'], 500);
}

// SONRA
try {
    // ... code ...
} catch (\Exception $e) {
    // ✅ STANDARDIZED: Using ResponseService (automatic logging)
    if ($request->expectsJson()) {
        return ResponseService::serverError('Hata mesajı', $e);
    }
    return ResponseService::backError('Hata mesajı: ' . $e->getMessage());
}
```

### Adım 4: Logging Migration

```php
// ÖNCE
Log::error('Hata oluştu', ['data' => $data]);

// SONRA
// ✅ STANDARDIZED: Using LogService
LogService::error('Hata oluştu', ['data' => $data], $exception);
```

---

## 📊 MIGRATION CHECKLIST

### Cache Standardization

- [ ] `Cache::remember` → `CacheHelper::remember`
- [ ] `Cache::get` → `CacheHelper::get`
- [ ] `Cache::put` → `CacheHelper::put`
- [ ] `Cache::forget` → `CacheHelper::forget`
- [ ] Key format kontrolü
- [ ] TTL preset kullanımı

### Error Handling Standardization

- [ ] `response()->json()` → `ResponseService::success/error`
- [ ] `redirect()->back()->with('error')` → `ResponseService::backError`
- [ ] Exception handler'ları güncelle
- [ ] Automatic logging kontrolü

### Logging Standardization

- [ ] `Log::info` → `LogService::info`
- [ ] `Log::error` → `LogService::error`
- [ ] `Log::warning` → `LogService::warning`
- [ ] Context bilgisi ekle
- [ ] Specialized method kullanımı (api, database, auth, ai)

---

## 🎯 BEST PRACTICES

### 1. Cache

- ✅ Namespace kullanımı zorunlu
- ✅ TTL preset kullan (sabit değer değil)
- ✅ Parametreli key'ler için params array kullan
- ✅ Cache invalidation stratejisi belirle

### 2. Error Handling

- ✅ Automatic logging kullan
- ✅ Structured error responses
- ✅ Exception type handling
- ✅ User-friendly error messages

### 3. Logging

- ✅ Context bilgisi ekle
- ✅ Specialized methods kullan (api, database, auth, ai)
- ✅ Sensitive data loglamaktan kaçın
- ✅ Log levels doğru kullan

---

## 📚 REFERENCE

### Service Dosyaları

- `app/Services/Cache/CacheService.php`
- `app/Services/Cache/CacheHelper.php`
- `app/Services/Response/ResponseService.php`
- `app/Services/Response/ErrorHandlerService.php`
- `app/Services/Logging/LogService.php`
- `app/Services/Logging/LogHelper.php`

### Authority File

- `.context7/authority.json` - `standardization_standards_2025_11_05`

### Knowledge Base

- `.yalihan-bekci/knowledge/standardization-standards-2025-11-05.json`

---

## ✅ COMPLIANCE

**Status:** ✅ ACTIVE  
**Enforcement:** STRICT  
**Last Updated:** 2025-11-05  
**Version:** 1.0.0

Tüm yeni kodlar bu standartlara uygun olmalıdır. Mevcut kodlar migration sürecinde standardize edilmektedir.

---

**Context7 Standardization Standards v1.0.0**
