# 🚀 Production Deployment Checklist

**Tarih:** 5 Aralık 2025  
**Versiyon:** 1.0.0  
**Durum:** 📋 Production Hazırlık  
**Context7:** %100 Uyumlu

---

## 📋 GENEL BAKIŞ

Bu checklist, Yalıhan Emlak sistemini production sunucuya deploy etmeden önce yapılması gereken tüm adımları içerir.

---

## ✅ 1. KOD HAZIRLIĞI

### 1.1. Git Kontrolü
- [ ] Tüm değişiklikler commit edildi
- [ ] Main branch'te son versiyon var
- [ ] Merge conflict'ler çözüldü
- [ ] Test'ler geçti (varsa)

### 1.2. Code Quality
- [ ] Linter hataları düzeltildi
- [ ] Context7 compliance %100
- [ ] Dead code temizlendi
- [ ] Code review yapıldı

### 1.3. Dokümantasyon
- [ ] README.md güncel
- [ ] API dokümantasyonu hazır
- [ ] Deployment guide hazır
- [ ] Changelog güncel

---

## 🔐 2. ENVIRONMENT VARIABLES

### 2.1. Gerekli Değişkenler

```env
# Application
APP_NAME="Yalihan Emlak OS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://panel.yalihanemlak.com.tr
APP_KEY=base64:...

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yalihanemlak_ultra
DB_USERNAME=...
DB_PASSWORD=...

# Telegram Bot
TELEGRAM_BOT_TOKEN=...
TELEGRAM_BOT_USERNAME=YalihanCortex_Bot
TELEGRAM_WEBHOOK_URL=https://panel.yalihanemlak.com.tr/api/telegram/webhook
TELEGRAM_ADMIN_CHAT_ID=...

# n8n Integration
N8N_WEBHOOK_URL=https://n8n.yalihanemlak.com.tr
N8N_WEBHOOK_SECRET=...
N8N_GOREV_CREATED_WEBHOOK=...
N8N_ILAN_PRICE_CHANGED_WEBHOOK=...

# AI Services
DEEPSEEK_API_KEY=...
OPENAI_API_KEY=...
GEMINI_API_KEY=...
OLLAMA_URL=http://ollama:11434
ANYTHINGLLM_URL=http://anythingllm:3001/api/v1
ANYTHINGLLM_KEY=...
ANYTHINGLLM_WORKSPACE=yalihan-hukuk

# Queue
QUEUE_CONNECTION=database
DB_QUEUE_TABLE=jobs

# Cache
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Session
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yalihanemlak.com.tr
```

### 2.2. Kontrol Script'i

```bash
#!/bin/bash
# scripts/check-env.sh

echo "🔍 Environment Variables Kontrolü..."
echo ""

REQUIRED_VARS=(
    "APP_KEY"
    "DB_DATABASE"
    "DB_USERNAME"
    "DB_PASSWORD"
    "TELEGRAM_BOT_TOKEN"
    "N8N_WEBHOOK_SECRET"
)

for var in "${REQUIRED_VARS[@]}"; do
    if grep -q "^${var}=" .env; then
        echo "✅ $var"
    else
        echo "❌ $var EKSİK!"
    fi
done
```

---

## 🗄️ 3. DATABASE HAZIRLIĞI

### 3.1. Migration Kontrolü

```bash
# Migration durumunu kontrol et
php artisan migrate:status

# Bekleyen migration'ları çalıştır
php artisan migrate --force

# Rollback test (opsiyonel)
php artisan migrate:rollback --step=1
php artisan migrate
```

### 3.2. Database Backup

```bash
# Production'a geçmeden önce backup al
mysqldump -u root -p yalihanemlak_ultra > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 3.3. Index Kontrolü

```sql
-- Eksik index'leri kontrol et
SHOW INDEXES FROM ilanlar;
SHOW INDEXES FROM kisiler;
SHOW INDEXES FROM talepler;
```

---

## 📦 4. DEPENDENCIES & BUILD

### 4.1. Composer

```bash
# Production dependencies
composer install --no-dev --optimize-autoloader

# Autoload optimize
composer dump-autoload --optimize
```

### 4.2. NPM (Frontend)

```bash
# Dependencies install
npm ci

# Production build
npm run build

# Veya development build
npm run dev
```

### 4.3. Storage Link

```bash
# Storage link oluştur
php artisan storage:link
```

---

## ⚡ 5. CACHE & OPTIMIZATION

### 5.1. Cache Temizliği

```bash
# Tüm cache'leri temizle
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear
php artisan event:clear
```

### 5.2. Cache Oluşturma (Production)

```bash
# Config cache
php artisan config:cache

# Route cache
php artisan route:cache

# View cache
php artisan view:cache

# Event cache (varsa)
php artisan event:cache
```

---

## 🔄 6. QUEUE WORKER

### 6.1. Supervisor Configuration

**Dosya:** `/etc/supervisor/conf.d/yalihan-worker.conf`

```ini
[program:yalihan-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/yalihanai/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --queue=cortex-notifications,default
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/yalihanai/storage/logs/worker.log
stopwaitsecs=3600
```

### 6.2. Supervisor Komutları

```bash
# Config'i yeniden yükle
sudo supervisorctl reread
sudo supervisorctl update

# Worker'ı başlat
sudo supervisorctl start yalihan-worker:*

# Durumu kontrol et
sudo supervisorctl status yalihan-worker:*

# Log kontrolü
tail -f /var/www/yalihanai/storage/logs/worker.log
```

---

## 🌐 7. WEB SERVER CONFIGURATION

### 7.1. Nginx Configuration

**Dosya:** `/etc/nginx/sites-available/yalihanai`

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name panel.yalihanemlak.com.tr;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name panel.yalihanemlak.com.tr;

    root /var/www/yalihanai/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/panel.yalihanemlak.com.tr/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/panel.yalihanemlak.com.tr/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Laravel Configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # File Upload Size
    client_max_body_size 50M;
}
```

### 7.2. PHP-FPM Configuration

**Dosya:** `/etc/php/8.4/fpm/pool.d/www.conf`

```ini
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.4-fpm.sock
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 50
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

---

## 🔐 8. SSL CERTIFICATE

### 8.1. Let's Encrypt

```bash
# Certbot install
sudo apt-get update
sudo apt-get install certbot python3-certbot-nginx

# SSL certificate oluştur
sudo certbot --nginx -d panel.yalihanemlak.com.tr

# Auto-renewal test
sudo certbot renew --dry-run
```

### 8.2. SSL Kontrolü

```bash
# SSL durumunu kontrol et
openssl s_client -connect panel.yalihanemlak.com.tr:443 -servername panel.yalihanemlak.com.tr
```

---

## 📱 9. TELEGRAM BOT SETUP

### 9.1. Webhook Ayarlama

```bash
# Webhook URL'ini ayarla
curl -X POST "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/setWebhook" \
  -d "url=https://panel.yalihanemlak.com.tr/api/telegram/webhook"

# Webhook durumunu kontrol et
curl -X GET "https://api.telegram.org/bot${TELEGRAM_BOT_TOKEN}/getWebhookInfo"
```

### 9.2. Webhook Test

```bash
# Test endpoint'i kontrol et
curl https://panel.yalihanemlak.com.tr/api/telegram/webhook/test
```

---

## 🔄 10. N8N INTEGRATION

### 10.1. Webhook URL'leri Kontrolü

```bash
# n8n webhook URL'lerini kontrol et
echo $N8N_GOREV_CREATED_WEBHOOK
echo $N8N_ILAN_PRICE_CHANGED_WEBHOOK

# Manuel test
curl -X POST "${N8N_GOREV_CREATED_WEBHOOK}" \
  -H "X-N8N-SECRET: ${N8N_WEBHOOK_SECRET}" \
  -H "Content-Type: application/json" \
  -d '{"test": true}'
```

### 10.2. n8n Workflow Kontrolü

- [ ] Tüm workflow'lar aktif
- [ ] Webhook URL'leri doğru
- [ ] Authentication header doğru
- [ ] Test mesajları çalışıyor

---

## 📊 11. MONITORING & LOGGING

### 11.1. Log Rotation

**Dosya:** `/etc/logrotate.d/yalihanai`

```
/var/www/yalihanai/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 11.2. Monitoring Setup

```bash
# Log monitoring
tail -f /var/www/yalihanai/storage/logs/laravel.log

# Queue monitoring
tail -f /var/www/yalihanai/storage/logs/worker.log

# Error tracking (Sentry, Bugsnag, vs.)
# Kurulum yapılmalı
```

---

## 🧪 12. POST-DEPLOYMENT TESTS

### 12.1. Health Check

```bash
# Application health
curl https://panel.yalihanemlak.com.tr/api/health

# Database connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Queue worker
php artisan queue:work --once
```

### 12.2. Functionality Tests

- [ ] Login çalışıyor
- [ ] İlan listesi açılıyor
- [ ] İlan ekleme çalışıyor
- [ ] Telegram bot yanıt veriyor
- [ ] n8n webhook'ları çalışıyor
- [ ] Email gönderimi çalışıyor

---

## 📝 13. DEPLOYMENT SCRIPT

**Dosya:** `scripts/deploy-production.sh`

```bash
#!/bin/bash

set -e

echo "🚀 Production Deployment Başlıyor..."

# 1. Git pull
echo "📥 Git pull..."
git pull origin main

# 2. Composer install
echo "📦 Composer install..."
composer install --no-dev --optimize-autoloader

# 3. NPM build
echo "🎨 Frontend build..."
npm ci
npm run build

# 4. Migration
echo "🗄️ Database migration..."
php artisan migrate --force

# 5. Cache clear
echo "🧹 Cache temizliği..."
php artisan config:clear
php artisan route:clear
php artisan cache:clear
php artisan view:clear

# 6. Cache rebuild
echo "⚡ Cache rebuild..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Storage link
echo "🔗 Storage link..."
php artisan storage:link

# 8. Queue restart
echo "🔄 Queue worker restart..."
sudo supervisorctl restart yalihan-worker:*

# 9. PHP-FPM restart
echo "🔄 PHP-FPM restart..."
sudo systemctl restart php8.4-fpm

# 10. Nginx reload
echo "🔄 Nginx reload..."
sudo nginx -t && sudo systemctl reload nginx

echo "✅ Deployment tamamlandı!"
```

**Kullanım:**
```bash
chmod +x scripts/deploy-production.sh
./scripts/deploy-production.sh
```

---

## ✅ FINAL CHECKLIST

### Pre-Deployment
- [ ] Kod hazır
- [ ] Environment variables ayarlandı
- [ ] Database backup alındı
- [ ] SSL certificate hazır
- [ ] Queue worker config hazır

### Deployment
- [ ] Git pull yapıldı
- [ ] Dependencies install edildi
- [ ] Migration çalıştırıldı
- [ ] Cache temizlendi ve rebuild edildi
- [ ] Storage link oluşturuldu

### Post-Deployment
- [ ] Health check başarılı
- [ ] Queue worker çalışıyor
- [ ] Telegram webhook aktif
- [ ] n8n webhook'ları çalışıyor
- [ ] Monitoring aktif
- [ ] Log rotation aktif

---

## 🚨 ROLLBACK PLAN

Eğer bir sorun çıkarsa:

```bash
# 1. Git rollback
git reset --hard HEAD~1

# 2. Cache temizle
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# 3. Queue restart
sudo supervisorctl restart yalihan-worker:*

# 4. PHP-FPM restart
sudo systemctl restart php8.4-fpm
```

---

**Hazırlayan:** Yalıhan Technical Team  
**Tarih:** 5 Aralık 2025  
**Versiyon:** 1.0.0  
**Durum:** ✅ Production Deployment Checklist Hazır

