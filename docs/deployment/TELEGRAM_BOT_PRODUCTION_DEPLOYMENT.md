# 🚀 Telegram Bot Production Deployment Rehberi

**Tarih:** 01 Aralık 2025  
**Bot:** @YalihanCortex_Bot  
**Durum:** Local'den Production'a Geçiş

---

## 📊 MEVCUT DURUM

### Local Development

- ✅ Kodlar local'de çalışıyor
- ✅ ngrok ile test ediliyor
- ✅ Bot eşleştirme başarılı
- ✅ Komutlar çalışıyor (TaskProcessor düzeltildi)

### Production Sunucu

- ✅ Domain: `panel.yalihanemlak.com.tr`
- ✅ Cloudflare Tunnel mevcut
- ⏳ Kodlar henüz production'a alınmadı

---

## 🎯 PRODUCTION'A ALMAK GEREKİYOR MU?

### ✅ EVET, GEREKİYOR!

**Nedenler:**

1. **Süreklilik:** ngrok Free Plan'da URL değişir, production'da sabit URL gerekir
2. **Güvenilirlik:** Cloudflare Tunnel daha stabil
3. **Performans:** Production sunucu daha hızlı
4. **Erişilebilirlik:** 7/24 çalışmalı (ngrok'u açık tutmak zor)

---

## 📋 DEPLOYMENT ADIMLARI

### 1. Kodları Production'a Al

#### Git ile (Önerilen):

```bash
# Local'de commit yapın
git add .
git commit -m "feat: Telegram Bot Cortex Architecture - Production ready"
git push origin main

# Production sunucuda
cd /path/to/production
git pull origin main
```

#### Manuel (Alternatif):

```bash
# Dosyaları production'a kopyalayın:
- app/Services/Telegram/TelegramBrain.php
- app/Services/Telegram/Processors/*.php
- app/Http/Controllers/Api/TelegramWebhookController.php
- app/Http/Middleware/VerifyCsrfToken.php (CSRF güncellemesi)
- routes/api.php (webhook route)
- database/migrations/*_add_telegram_cortex_fields_to_users_table.php
```

---

### 2. Environment Değişkenleri

Production `.env` dosyasına ekleyin:

```env
# Telegram Bot Configuration
TELEGRAM_BOT_TOKEN=7834521220:AAFLKxa18v4UFPj46Fh-esL-8uMdmuXxy70
TELEGRAM_BOT_USERNAME=YalihanCortex_Bot
TELEGRAM_ADMIN_CHAT_ID=515406829
TELEGRAM_TEAM_CHANNEL_ID=-1003037949764

# Webhook URL (Production)
TELEGRAM_WEBHOOK_URL=https://panel.yalihanemlak.com.tr/api/telegram/webhook

# AI Services (Production URL'leri)
OLLAMA_URL=http://ollama:11434
ANYTHINGLLM_URL=http://anythingllm:3001/api/v1
ANYTHINGLLM_KEY=your_production_key
ANYTHINGLLM_WORKSPACE=yalihan-hukuk
ANYTHINGLLM_TIMEOUT=120

# Voice-to-CRM
WHISPER_URL=http://whisper:9000
```

---

### 3. Database Migration

```bash
# Production sunucuda
php artisan migrate
```

**Kontrol:**

```bash
# users tablosunda yeni kolonlar var mı?
php artisan tinker --execute="echo Schema::hasColumn('users', 'telegram_id') ? '✅' : '❌';"
```

---

### 4. Webhook'u Production URL'ine Ayarla

```bash
# Production webhook URL'i
curl -X POST "https://api.telegram.org/bot7834521220:AAFLKxa18v4UFPj46Fh-esL-8uMdmuXxy70/setWebhook?url=https://panel.yalihanemlak.com.tr/api/telegram/webhook"
```

**Kontrol:**

```bash
curl -s "https://api.telegram.org/bot7834521220:AAFLKxa18v4UFPj46Fh-esL-8uMdmuXxy70/getWebhookInfo" | python3 -m json.tool
```

**Beklenen:**

```json
{
    "ok": true,
    "result": {
        "url": "https://panel.yalihanemlak.com.tr/api/telegram/webhook",
        "pending_update_count": 0,
        "last_error_message": null
    }
}
```

---

### 5. Cache Temizliği

```bash
# Production sunucuda
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
```

---

### 6. Queue Worker (Kritik!)

Telegram bildirimleri için queue worker çalışmalı:

#### Supervisor ile (Önerilen):

```bash
# /etc/supervisor/conf.d/cortex-queue-worker.conf
[program:cortex-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/production/artisan queue:work --queue=cortex-notifications --tries=3 --timeout=60
autostart=true
autorestart=true
user=www-data
numprocs=1
stdout_logfile=/path/to/production/storage/logs/queue-worker.log
```

```bash
# Supervisor'ı yeniden yükle
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start cortex-queue-worker:*
```

#### Manuel (Test için):

```bash
# Arka planda çalıştır
nohup php artisan queue:work --queue=cortex-notifications --tries=3 > storage/logs/queue-worker.log 2>&1 &
```

---

### 7. Cloudflare Tunnel Kontrolü

Cloudflare Tunnel'ın çalıştığından emin olun:

```bash
# Tunnel durumunu kontrol et
cloudflared tunnel list

# Tunnel çalışıyor olmalı ve panel.yalihanemlak.com.tr'ye yönlendirmeli
```

---

## 🧪 PRODUCTION TEST ADIMLARI

### 1. Webhook Endpoint Testi

```bash
# Tarayıcıdan veya curl ile
curl https://panel.yalihanemlak.com.tr/api/telegram/webhook/test
```

**Beklenen:**

```json
{
    "success": true,
    "message": "Telegram webhook endpoint is active"
}
```

### 2. Bot Testi

```
1. Telegram'da @YalihanCortex_Bot'u açın
2. /start komutu gönderin
3. Eşleştirme kodu gönderin
4. /ozet komutu test edin
5. /gorevler komutu test edin
```

### 3. Log Kontrolü

```bash
# Production log'larını izleyin
tail -f storage/logs/laravel.log | grep -i telegram
```

---

## ⚠️ ÖNEMLİ NOTLAR

### 1. ngrok vs Cloudflare Tunnel

- **Local Development:** ngrok kullanın
- **Production:** Cloudflare Tunnel kullanın (zaten mevcut)

### 2. Webhook URL Değişikliği

- Local'de: `https://ngrok-url.ngrok-free.app/api/telegram/webhook`
- Production'da: `https://panel.yalihanemlak.com.tr/api/telegram/webhook`

### 3. Environment Değişkenleri

- Production `.env` dosyası local'den farklı olmalı
- Production URL'leri kullanın
- API key'leri production key'leri olmalı

### 4. Queue Worker

- **Kritik:** Queue worker çalışmazsa Telegram bildirimleri gönderilmez
- Supervisor ile otomatik başlatma önerilir
- Log'ları düzenli kontrol edin

---

## 🔄 DEPLOYMENT SCRIPT

Otomatik deployment için:

```bash
# Production sunucuda
./scripts/deploy-cortex.sh
```

Bu script:

- ✅ Environment değişkenlerini kontrol eder
- ✅ Migration'ları çalıştırır
- ✅ Cache'i temizler
- ✅ Servis sağlık kontrolleri yapar
- ✅ Queue worker durumunu kontrol eder

---

## 📊 DEPLOYMENT CHECKLIST

- [ ] Kodlar production'a alındı (git pull veya manuel)
- [ ] Environment değişkenleri production `.env`'e eklendi
- [ ] Database migration çalıştırıldı
- [ ] Webhook production URL'ine ayarlandı
- [ ] Cache temizlendi
- [ ] Queue worker başlatıldı (Supervisor veya manuel)
- [ ] Cloudflare Tunnel çalışıyor
- [ ] Webhook endpoint test edildi
- [ ] Bot test edildi (eşleştirme, komutlar)
- [ ] Log'lar kontrol edildi

---

## 🎯 SONUÇ

### Local Development

- ✅ Test için yeterli
- ⚠️ ngrok URL değişir
- ⚠️ Sürekli açık tutmak gerekir

### Production Deployment

- ✅ **GEREKLİ** - Sürekli çalışması için
- ✅ Cloudflare Tunnel zaten mevcut
- ✅ Sabit URL (panel.yalihanemlak.com.tr)
- ✅ 7/24 erişilebilir

---

**Son Güncelleme:** 01 Aralık 2025
