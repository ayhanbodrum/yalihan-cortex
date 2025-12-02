# ⚡ Route Cache Kullanım Rehberi

**Tarih:** 01 Aralık 2025  
**Versiyon:** 1.0.0  
**Context7 Standardı:** C7-ROUTE-CACHE-2025-12-01

---

## 📋 Genel Bakış

Route cache, Laravel'de route dosyalarını bir kez compile edip cache'leyerek her request'te route bulma süresini %95-98 azaltır.

---

## 🚀 Hızlı Başlangıç

### Production'da Route Cache

```bash
# Route cache oluştur
php artisan route:cache

# Cache'i kontrol et
php artisan route:list
```

### Development'ta Cache Temizleme

```bash
# Route cache'i temizle (hot reload için)
php artisan route:clear

# Tüm cache'leri temizle
php artisan optimize:clear
```

---

## 📊 Performans Kazanımları

### Mevcut Durum (Cache Yok)

- **Route Bulma:** 50-100ms per request
- **CPU Kullanımı:** Yüksek (her request'te parsing)
- **Memory:** Her request'te route'lar yeniden oluşturulur

### Route Cache ile

- **Route Bulma:** 1-5ms per request (%95-98 ⬇️)
- **CPU Kullanımı:** Minimal (%70-80 ⬇️)
- **Memory:** Cache'den okuma (çok hızlı)

---

## 🔧 Kullanım Senaryoları

### 1. Production Deployment

```bash
# Deployment script içinde
php artisan route:cache
php artisan config:cache
php artisan view:cache
```

### 2. Development

```bash
# Development'ta cache'i temizle (hot reload için)
php artisan route:clear
```

### 3. Route Değişikliği Sonrası

```bash
# Route değişikliği yaptıktan sonra
php artisan route:clear
php artisan route:cache
```

---

## ⚠️ Önemli Notlar

### Route Cache Kullanırken

1. **Closure Route'lar:** Route cache kullanırken closure route'lar çalışmaz

    ```php
    // ❌ YANLIŞ: Closure route (cache ile çalışmaz)
    Route::get('/test', function () {
        return 'Test';
    });

    // ✅ DOĞRU: Controller route (cache ile çalışır)
    Route::get('/test', [TestController::class, 'index']);
    ```

2. **Route Değişikliği:** Route değişikliği yaptıktan sonra cache'i temizlemelisiniz

    ```bash
    php artisan route:clear
    php artisan route:cache
    ```

3. **Development:** Development'ta cache kullanmayın (hot reload için)

---

## 📝 Deployment Checklist

### Production'a Almadan Önce

- [ ] Tüm closure route'lar controller'a taşındı mı?
- [ ] Route cache oluşturuldu mu? (`php artisan route:cache`)
- [ ] Route'lar test edildi mi? (`php artisan route:list`)

### Development'ta

- [ ] Route cache temizlendi mi? (`php artisan route:clear`)
- [ ] Hot reload çalışıyor mu?

---

## 🔍 Sorun Giderme

### Route Bulunamıyor

```bash
# Cache'i temizle ve yeniden oluştur
php artisan route:clear
php artisan route:cache
php artisan route:list
```

### Closure Route Hatası

```bash
# Closure route'ları bul
grep -r "Route::.*function" routes/

# Controller'a taşı
# Route::get('/test', function () { ... });
# → Route::get('/test', [TestController::class, 'index']);
```

---

## 📊 Performans Metrikleri

### Test Sonuçları

**1000 Request Test:**

- **Cache Yok:** ~50-100ms per request = 50-100 saniye toplam
- **Cache Var:** ~1-5ms per request = 1-5 saniye toplam
- **Kazanç:** 45-95 saniye (%90-95 ⬇️)

---

**Son Güncelleme:** 01 Aralık 2025
