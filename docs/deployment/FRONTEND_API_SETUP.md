# 🔐 Frontend API Setup Guide

**Tarih:** 01 Aralık 2025  
**Context7 Standard:** C7-FRONTEND-API-2025-12-01

---

## 📋 KURULUM ADIMLARI

### 1. Environment Variables

`.env` dosyasına ekleyin:

```env
# Frontend API Configuration
FRONTEND_API_KEY=your-secret-api-key-here-min-32-chars
FRONTEND_API_ALLOWED_IPS=172.17.0.0/16,10.0.0.0/8
FRONTEND_API_LOG_REQUESTS=false
FRONTEND_API_RATE_LIMIT=60
```

**Önemli:**
- `FRONTEND_API_KEY`: En az 32 karakter, güçlü bir random string
- `FRONTEND_API_ALLOWED_IPS`: Docker network IP aralıkları (virgülle ayrılmış)
- `FRONTEND_API_LOG_REQUESTS`: Production'da `false` (performans için)
- `FRONTEND_API_RATE_LIMIT`: Dakika başına istek limiti

### 2. API Key Oluşturma

```bash
# Güçlü bir API key oluştur
php artisan tinker
>>> Str::random(64)
```

Veya:

```bash
openssl rand -hex 32
```

### 3. Vitrin (Next.js/Laravel) Yapılandırması

**Next.js için:**

```javascript
// .env.local
NEXT_PUBLIC_API_URL=http://panel-app:8000/api/frontend
INTERNAL_API_KEY=your-secret-api-key-here
```

**API çağrısı:**

```javascript
// lib/api.js
const API_URL = process.env.NEXT_PUBLIC_API_URL;
const API_KEY = process.env.INTERNAL_API_KEY;

export async function fetchProperties(filters = {}) {
  const queryString = new URLSearchParams(filters).toString();
  const response = await fetch(`${API_URL}/properties?${queryString}`, {
    headers: {
      'X-Internal-API-Key': API_KEY,
      'Content-Type': 'application/json',
    },
  });

  if (!response.ok) {
    throw new Error('API request failed');
  }

  return response.json();
}
```

**Laravel için:**

```php
// config/services.php (Vitrin projesinde)
'panel_api' => [
    'url' => env('PANEL_API_URL', 'http://panel-app:8000/api/frontend'),
    'key' => env('PANEL_API_KEY', ''),
],

// Service
class PanelApiService
{
    public function getProperties(array $filters = [])
    {
        $response = Http::withHeaders([
            'X-Internal-API-Key' => config('services.panel_api.key'),
        ])->get(config('services.panel_api.url') . '/properties', $filters);

        return $response->json();
    }
}
```

---

## 🔒 GÜVENLİK KONTROLLERİ

### 1. API Key Doğrulama

Her istekte `X-Internal-API-Key` header'ı kontrol edilir:

```bash
# ✅ DOĞRU
curl -H "X-Internal-API-Key: your-secret-api-key" \
     http://panel-app:8000/api/frontend/properties

# ❌ YANLIŞ
curl http://panel-app:8000/api/frontend/properties
# Response: {"success":false,"error":"Unauthorized"}
```

### 2. IP Whitelisting

Sadece Docker network IP'lerinden erişim:

```php
// config/services.php
'allowed_ips' => [
    '172.17.0.0/16',  // Docker default network
    '10.0.0.0/8',     // Docker custom network
],
```

### 3. Rate Limiting

Dakika başına 60 istek limiti (configurable):

```php
// routes/api.php
Route::middleware(['throttle:60,1'])->group(...);
```

---

## 📊 CACHING STRATEJİSİ

### Cache Tags

```php
// PropertyFeedService
Cache::tags(['frontend-properties'])->remember(...);
```

### Cache Invalidation

İlan güncellendiğinde cache'i temizle:

```php
// app/Models/Ilan.php
protected static function booted()
{
    static::updated(function ($ilan) {
        Cache::tags(['frontend-properties'])->flush();
    });
}
```

### Cache TTL

- **Featured Properties:** 5 dakika (300 saniye)
- **Property Detail:** 10 dakika (600 saniye)
- **Property List:** 5 dakika (300 saniye)

---

## 🧪 TEST ETME

### 1. API Key Test

```bash
# Terminal'den test
curl -H "X-Internal-API-Key: your-secret-api-key" \
     http://localhost:8000/api/frontend/properties/featured
```

### 2. Vitrin Entegrasyonu

```javascript
// Next.js test
const response = await fetchProperties({ category: 'villa', limit: 6 });
console.log(response);
```

### 3. Docker Network Test

```bash
# Docker container içinden
docker exec -it yalihan-web curl -H "X-Internal-API-Key: your-secret-api-key" \
     http://panel-app:8000/api/frontend/properties
```

---

## 🐛 TROUBLESHOOTING

### Problem: 401 Unauthorized

**Çözüm:**
1. `.env` dosyasında `FRONTEND_API_KEY` tanımlı mı?
2. Vitrin projesinde `INTERNAL_API_KEY` doğru mu?
3. Header adı `X-Internal-API-Key` (büyük/küçük harf duyarlı)

### Problem: 403 Forbidden

**Çözüm:**
1. IP whitelist kontrolü: `config('services.frontend_api.allowed_ips')`
2. Docker network IP'si doğru mu?

### Problem: Cache çalışmıyor

**Çözüm:**
1. Redis çalışıyor mu? `redis-cli ping`
2. Cache driver `redis` mi? `config('cache.default')`
3. Cache tags destekleniyor mu? (Redis gerekli)

---

## 📈 PERFORMANS İYİLEŞTİRMELERİ

### 1. Response Compression

```nginx
# Nginx config
gzip on;
gzip_types application/json;
```

### 2. Database Indexing

```sql
CREATE INDEX idx_ilan_status_yayin ON ilanlar(status, yayin_tipi);
CREATE INDEX idx_ilan_kategori ON ilanlar(kategori_id);
```

### 3. Eager Loading

```php
// N+1 problem'ini önle
Ilan::with(['kategori', 'il', 'ilce'])->get();
```

---

## ✅ DEPLOYMENT CHECKLIST

- [ ] `.env` dosyasında `FRONTEND_API_KEY` tanımlı
- [ ] Vitrin projesinde `INTERNAL_API_KEY` yapılandırıldı
- [ ] Docker network IP whitelist ayarlandı
- [ ] Rate limiting aktif
- [ ] Redis cache çalışıyor
- [ ] API endpoint'leri test edildi
- [ ] Cache invalidation çalışıyor
- [ ] Error logging aktif

---

**Son Güncelleme:** 01 Aralık 2025

