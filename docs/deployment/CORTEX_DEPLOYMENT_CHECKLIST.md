# 🚀 Cortex Telegram Entegrasyonu - Deployment Checklist

**Tarih:** 2025-11-30  
**Versiyon:** 2.1.0  
**Durum:** Production Ready

---

## ⚠️ ÖNEMLİ UYARI

Bu checklist'i **production'a almadan önce** mutlaka tamamlayın. Eksik adımlar sistemin çalışmamasına neden olabilir.

---

## 📋 1. ENVIRONMENT DEĞİŞKENLERİ

### `.env` Dosyası Kontrolü

Aşağıdaki değişkenlerin **gerçek verilerle** dolu olduğundan emin olun:

```env
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
TELEGRAM_ADMIN_CHAT_ID=987654321

# AnythingLLM Configuration (RAG için)
ANYTHINGLLM_URL=http://127.0.0.1:3001/api/v1
ANYTHINGLLM_KEY=your_anythingllm_api_key_here
ANYTHINGLLM_WORKSPACE=yalihan-hukuk
ANYTHINGLLM_TIMEOUT=60

# Ollama Configuration (Local LLM)
OLLAMA_URL=http://ollama:11434

# Queue Configuration
QUEUE_CONNECTION=database
DB_QUEUE_TABLE=jobs
```

### Kontrol Komutu

```bash
# .env dosyasında gerekli değişkenlerin varlığını kontrol et
grep -E "TELEGRAM_BOT_TOKEN|TELEGRAM_ADMIN_CHAT_ID|ANYTHINGLLM" .env
```

**Beklenen Çıktı:**
```
TELEGRAM_BOT_TOKEN=123456:ABC-DEF...
TELEGRAM_ADMIN_CHAT_ID=987654321
ANYTHINGLLM_URL=http://127.0.0.1:3001/api/v1
ANYTHINGLLM_KEY=...
```

---

## 🔄 2. QUEUE WORKER YAPILANDIRMASI

### Kritik: Queue Worker Çalışmalı

Telegram bildirimleri `cortex-notifications` kuyruğuna atılıyor. Bu kuyruğun çalışması için **queue worker** sürekli çalışmalı.

### Supervisor Yapılandırması (Önerilen)

**Dosya:** `/etc/supervisor/conf.d/cortex-queue-worker.conf`

```ini
[program:cortex-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/yalihanai/artisan queue:work --queue=cortex-notifications --tries=3 --timeout=60
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/yalihanai/storage/logs/queue-worker.log
stopwaitsecs=3600
```

**Supervisor Komutları:**

```bash
# Supervisor yapılandırmasını yeniden yükle
sudo supervisorctl reread
sudo supervisorctl update

# Queue worker'ı başlat
sudo supervisorctl start cortex-queue-worker:*

# Durumu kontrol et
sudo supervisorctl status cortex-queue-worker:*
```

### Manuel Çalıştırma (Test için)

```bash
# Queue worker'ı manuel başlat (arka planda)
php artisan queue:work --queue=cortex-notifications --tries=3 --timeout=60 &
```

### Docker Compose Yapılandırması

**docker-compose.yml** içine ekleyin:

```yaml
services:
  queue-worker:
    build: .
    command: php artisan queue:work --queue=cortex-notifications --tries=3 --timeout=60
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    restart: unless-stopped
```

---

## 🗄️ 3. VERİTABANI MİGRASYONLARI

### Queue Tabloları

Queue worker'ın çalışması için `jobs` tablosu gerekli:

```bash
# Queue tablolarını oluştur
php artisan queue:table
php artisan migrate
```

### Kontrol

```bash
# jobs tablosunun varlığını kontrol et
php artisan tinker
>>> Schema::hasTable('jobs')
=> true
```

---

## 🧹 4. CACHE TEMİZLİĞİ

### Config ve Cache Temizliği

`config/yali_options.php` dosyasında yaptığımız değişikliklerin algılanması için:

```bash
# Config cache'i temizle
php artisan config:clear

# Application cache'i temizle
php artisan cache:clear

# Route cache'i temizle
php artisan route:clear

# View cache'i temizle
php artisan view:clear

# Tüm cache'leri temizle (önerilen)
php artisan optimize:clear
```

### Production'da Cache Oluşturma

```bash
# Production'da cache'leri oluştur (performans için)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 🔍 5. SERVİS SAĞLIK KONTROLLERİ

### Telegram Bot Kontrolü

```bash
# Telegram bot token'ını test et
curl "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getMe"
```

**Beklenen Çıktı:**
```json
{
  "ok": true,
  "result": {
    "id": 123456789,
    "is_bot": true,
    "first_name": "Yalıhan Cortex",
    "username": "yalihan_cortex_bot"
  }
}
```

### AnythingLLM Kontrolü

```bash
# AnythingLLM health check
curl -H "Authorization: Bearer <YOUR_ANYTHINGLLM_KEY>" \
     http://127.0.0.1:3001/api/system/health
```

### Ollama Kontrolü

```bash
# Ollama health check
curl http://ollama:11434/api/tags
```

---

## 📊 6. LOG KONTROLLERİ

### Log Dosyaları

```bash
# Queue worker loglarını kontrol et
tail -f storage/logs/queue-worker.log

# Laravel loglarını kontrol et
tail -f storage/logs/laravel.log

# Telegram bildirim loglarını kontrol et
grep "TelegramService" storage/logs/laravel.log
```

---

## 🧪 7. TEST SENARYOSU

### Manuel Test

1. **Test İlan Oluştur:**
   - Admin panelinden yeni bir ilan oluştur
   - Skor > 90 olan bir eşleşme olması için uygun kriterler seç

2. **Queue Kontrolü:**
   ```bash
   # jobs tablosunda bekleyen işleri kontrol et
   php artisan tinker
   >>> DB::table('jobs')->count()
   ```

3. **Telegram Bildirimi Kontrolü:**
   - Yöneticinin Telegram'ına bildirim gelip gelmediğini kontrol et
   - Mesaj formatını kontrol et

4. **ai_logs Kontrolü:**
   ```bash
   php artisan tinker
   >>> DB::table('ai_logs')->where('request_type', 'notification_sent')->latest()->first()
   ```

---

## 🔧 8. MONİTÖRİNG VE ALARM

### Queue Worker Monitoring

```bash
# Queue worker'ın çalışıp çalışmadığını kontrol et
ps aux | grep "queue:work"

# Bekleyen job sayısını kontrol et
php artisan queue:monitor cortex-notifications
```

### Alarm Kurulumu (Opsiyonel)

**Cron Job:** Her 5 dakikada bir queue worker'ı kontrol et

```bash
# Crontab'a ekle
*/5 * * * * /path/to/check-queue-worker.sh
```

**check-queue-worker.sh:**
```bash
#!/bin/bash
if ! pgrep -f "queue:work.*cortex-notifications" > /dev/null; then
    echo "Queue worker durdu!" | mail -s "Cortex Queue Worker Alert" admin@yalihanemlak.com.tr
    # Supervisor'ı yeniden başlat
    supervisorctl restart cortex-queue-worker:*
fi
```

---

## ✅ 9. DEPLOYMENT CHECKLIST ÖZET

- [ ] `.env` dosyasında `TELEGRAM_BOT_TOKEN` ve `TELEGRAM_ADMIN_CHAT_ID` dolu
- [ ] `ANYTHINGLLM_URL` ve `ANYTHINGLLM_KEY` yapılandırıldı
- [ ] `OLLAMA_URL` yapılandırıldı
- [ ] Queue worker Supervisor ile çalışıyor
- [ ] `jobs` tablosu oluşturuldu ve migrate edildi
- [ ] Cache'ler temizlendi (`php artisan optimize:clear`)
- [ ] Telegram bot token test edildi
- [ ] AnythingLLM health check yapıldı
- [ ] Ollama health check yapıldı
- [ ] Test senaryosu çalıştırıldı
- [ ] Log dosyaları kontrol edildi
- [ ] Monitoring kuruldu (opsiyonel)

---

## 🚨 SORUN GİDERME

### Queue Worker Çalışmıyor

```bash
# Supervisor loglarını kontrol et
sudo tail -f /var/log/supervisor/supervisord.log

# Queue worker'ı manuel başlat ve hataları gör
php artisan queue:work --queue=cortex-notifications --tries=3 -v
```

### Telegram Bildirimi Gitmiyor

1. Bot token'ı kontrol et
2. Admin chat ID'yi kontrol et
3. Queue worker'ın çalıştığını kontrol et
4. `ai_logs` tablosunda hata mesajlarını kontrol et

### AnythingLLM Bağlantı Hatası

1. `ANYTHINGLLM_URL` doğru mu?
2. `ANYTHINGLLM_KEY` geçerli mi?
3. AnythingLLM servisi çalışıyor mu?
4. Firewall/Network erişimi var mı?

---

## 📚 İLGİLİ DOKÜMANTASYON

- **System Architecture:** `docs/ai/YALIHAN_CORTEX_ARCHITECTURE_V2.1.md`
- **Telegram Service:** `app/Services/TelegramService.php`
- **HandleUrgentMatch:** `app/Modules/Cortex/Opportunity/Listeners/HandleUrgentMatch.php`

---

**Son Güncelleme:** 2025-11-30  
**Hazırlayan:** Yalıhan Cortex Architecture Team  
**Durum:** ✅ Production Ready



