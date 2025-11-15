# 🔍 Horizon vs Telescope - Fark Nedir?

**Tarih:** 2025-11-04  
**Soru:** İki araç aynı işi mi yapıyor?

---

## 🎯 KISA CEVAP

**HAYIR! Farklı şeyler yapıyorlar:**

```yaml
Telescope: REQUEST & DEBUG tool (hataları yakalar)
Horizon: QUEUE MONITORING tool (background job'ları izler)

İkisi de gerekli (farklı amaçlar)
```

---

## 📊 DETAYLI KARŞILAŞTIRMA

### 1️⃣ Laravel Telescope (Debugging Tool)

**Ne yapar:**

```yaml
✅ HTTP Requests izler (tüm route'lar)
✅ Exceptions/Errors yakalar
✅ Database queries gösterir (N+1 problems)
✅ Redis/Cache operations
✅ Mail/Notifications
✅ Model events
✅ Gate checks (authorization)
✅ Views rendered
✅ Console commands
```

**URL:** `http://127.0.0.1:8000/telescope`

**Ne zaman kullan:**

- 🐛 Bug debugging
- 🔍 Slow query tespiti
- 📊 Request analizi
- ❌ Error tracking

**Örnek:**

```
User clicked button → HTTP POST /api/ilanlar/store
Telescope shows:
  - Request payload
  - Database queries (15 queries, 250ms)
  - Exception: SQLSTATE[42S22] Column not found
  - Stack trace
```

---

### 2️⃣ Laravel Horizon (Queue Monitor)

**Ne yapar:**

```yaml
✅ Background job'ları izler (queue workers)
✅ Job throughput (saniyede kaç job)
✅ Failed jobs listesi
✅ Job retry management
✅ Worker memory/CPU usage
✅ Job processing time
✅ Queue metrics (wait time, processing time)
```

**URL:** `http://127.0.0.1:8000/horizon` (monitoring)

**Ne zaman kullan:**

- 📧 Email/SMS queue'ları
- 🖼️ Image processing (resize, compress)
- 📊 Report generation (background)
- 🔄 API sync operations

**Örnek:**

```
Email queued → Job added to queue
Horizon shows:
  - Queue: emails (5 pending, 2 processing)
  - Worker: supervisor-1 (active, 128MB)
  - Processing time: 2.5s
  - Failed: 0
```

---

## ⚠️ HORIZON ÇALIŞMIYOR - SEBEP?

### Muhtemel Sebepler:

**1. Horizon kurulu değil:**

```bash
# Kontrol:
composer show | grep horizon

# Eğer yoksa:
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

**2. Horizon worker çalışmıyor:**

```bash
# Horizon worker başlatılmalı:
php artisan horizon

# Ya da background'da:
php artisan horizon &
```

**3. Queue driver Redis değil:**

```env
# .env dosyasında:
QUEUE_CONNECTION=redis  # (database yerine)
```

**4. Route publish edilmemiş:**

```bash
php artisan horizon:publish
```

---

## 🔧 HORIZON NASIL ÇALIŞTIRILIR?

### Adım 1: Kurulum Kontrol

```bash
# 1. Horizon kurulu mu?
composer show laravel/horizon

# 2. Config var mı?
ls config/horizon.php

# 3. Redis çalışıyor mu?
redis-cli ping
# Yanıt: PONG
```

### Adım 2: Horizon Worker Başlat

```bash
# Development:
php artisan horizon

# Production (supervisor ile):
# /etc/supervisor/conf.d/horizon.conf
```

### Adım 3: Test Et

```bash
# Queue'ya job ekle:
php artisan tinker
> dispatch(new \App\Jobs\TestJob());

# Horizon'da görünmeli:
# http://127.0.0.1:8000/horizon
```

---

## 🎯 HANGİSİNİ KULLAN?

### Telescope Kullan (Debugging):

```yaml
✅ "Neden bu hata oluyor?"
✅ "Hangi query'ler çalışıyor?"
✅ "N+1 problem var mı?"
✅ "Request neden yavaş?"
✅ "Exception nerede fırlatılıyor?"
```

### Horizon Kullan (Queue Monitoring):

```yaml
✅ "Email gönderildi mi?"
✅ "Job'lar işleniyor mu?"
✅ "Failed job var mı?"
✅ "Queue throughput nedir?"
✅ "Worker memory kullanımı?"
```

---

## 📊 KARŞILAŞTIRMA TABLOSU

| Özellik                | Telescope                      | Horizon                  |
| ---------------------- | ------------------------------ | ------------------------ |
| **Amaç**               | Request debugging              | Queue monitoring         |
| **İzler**              | HTTP requests, queries, errors | Background jobs, workers |
| **Gerçek Zamanlı**     | ✅ Evet                        | ✅ Evet                  |
| **Failed Jobs**        | ❌ Hayır                       | ✅ Evet                  |
| **Query Analizi**      | ✅ Evet                        | ❌ Hayır                 |
| **Exception Tracking** | ✅ Evet                        | ❌ Hayır                 |
| **Worker Metrics**     | ❌ Hayır                       | ✅ Evet                  |
| **Kurulum Gerekli**    | ✅ Kurulu (çoğu proje)         | ⚠️ Opsiyonel             |
| **Redis Gerekli**      | ❌ Hayır                       | ✅ Evet                  |
| **Background Process** | ❌ Hayır                       | ✅ Evet (worker)         |

---

## 🚀 SİZİN PROJE İÇİN

### Mevcut Durum:

```yaml
Telescope: ✅ ÇALIŞIYOR
  URL: http://127.0.0.1:8000/telescope
  Kullanım: Error tracking, debugging

Horizon: ❌ ÇALIŞMIYOR (muhtemelen kurulu değil)
  URL: http://127.0.0.1:8000/horizon
  Durum: ?
```

### Öneriler:

**1. Horizon Gerekli mi?**

```yaml
EVET, eğer:
    - Email/SMS queue kullanıyorsanız
    - Background job'larınız varsa
    - Image processing yapıyorsanız
    - Report generation (background)

HAYIR, eğer:
    - Sadece sync operations
    - Queue kullanmıyorsanız
    - Küçük/basit proje
```

**2. Kurulum (Gerekliyse):**

```bash
# 1. Horizon kur
composer require laravel/horizon

# 2. Publish
php artisan horizon:install
php artisan migrate

# 3. Config (.env)
QUEUE_CONNECTION=redis

# 4. Worker başlat
php artisan horizon
```

**3. Sadece Telescope Kullan (Şimdilik):**

```yaml
Eğer queue kullanmıyorsanız: ✅ Telescope yeterli (debugging)
    ❌ Horizon'a gerek yok

Gelecekte queue eklerseniz: ✅ O zaman Horizon kur
```

---

## 💡 SONUÇ

**İki araç FARKLI işler yapar:**

```yaml
Telescope = Request Debugger
"Bu request neden hata verdi?"

Horizon = Queue Monitor
"Bu email gönderildi mi?"

İKİSİ DE GEREKLİ (farklı amaçlar için)
```

**Sizin için:**

- ✅ Telescope kullanmaya devam edin (çalışıyor)
- ⚠️ Horizon'a ihtiyacınız var mı kontrol edin
- ✅ Queue kullanıyorsanız → Horizon kur
- ❌ Queue kullanmıyorsanız → Horizon'a gerek yok

---

**Özet:** Telescope yeterli (şimdilik), Horizon'a ihtiyaç olursa kurarız! 🚀
