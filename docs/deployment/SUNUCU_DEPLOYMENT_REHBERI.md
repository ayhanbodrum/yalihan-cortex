# 🚀 Sunucu Deployment Rehberi

**Tarih:** 2025-12-01  
**Versiyon:** 2.1.0  
**Hedef:** Production sunucuya deployment

---

## 📋 ÖN HAZIRLIK

### 1. Sunucu Gereksinimleri

- PHP 8.2+ (8.4 önerilir)
- MySQL 8.0+ veya MariaDB 10.5+
- Composer 2.x
- Node.js 18+ (opsiyonel, frontend için)
- Git
- Supervisor (queue worker için)

### 2. Sunucu Erişimi

```bash
# SSH ile sunucuya bağlan
ssh kullanici@sunucu-ip
```

---

## 🔧 ADIM 1: GIT CLONE/PULL

### İlk Kurulum (Clone)

```bash
# Proje klasörüne git
cd /var/www  # veya proje klasörünüz

# Repository'yi clone et
git clone https://github.com/ayhanbodrum/yalihan-cortex.git yalihanai
cd yalihanai

# Main branch'e geç
git checkout main
```

### Güncelleme (Pull)

```bash
# Proje klasörüne git
cd /var/www/yalihanai  # veya proje klasörünüz

# Son değişiklikleri çek
git pull origin main
```

---

## 🔐 ADIM 2: ENVIRONMENT DOSYASI

### .env Dosyası Oluştur

```bash
# .env.example'dan kopyala
cp .env.example .env

# .env dosyasını düzenle
nano .env  # veya vi, vim
```

### .env Dosyasında Düzenlenecekler

```env
# Application
APP_NAME="Yalihan Emlak OS"
APP_ENV=production
APP_KEY=  # php artisan key:generate ile oluşturulacak
APP_DEBUG=false
APP_URL=https://panel.yalihanemlak.com.tr

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=yalihanemlak_ultra
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# AI Services
DEEPSEEK_API_KEY=your_deepseek_key
ANYTHINGLLM_URL=http://anythingllm:3001
ANYTHINGLLM_KEY=your_anythingllm_key
ANYTHINGLLM_WORKSPACE=yalihan-hukuk
WHISPER_URL=http://whisper:9000

# Telegram
TELEGRAM_BOT_TOKEN=your_telegram_bot_token
TELEGRAM_BOT_USERNAME=YalihanCortex_Bot
TELEGRAM_ADMIN_CHAT_ID=your_chat_id

# Frontend API
FRONTEND_API_KEY=your_frontend_api_key
```

---

## 💾 ADIM 3: VERİTABANI OLUŞTURMA

### MySQL'e Bağlan

```bash
mysql -u root -p
```

### Veritabanı Oluştur

```sql
CREATE DATABASE yalihanemlak_ultra CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'yalihan_user'@'localhost' IDENTIFIED BY 'güvenli_şifre';
GRANT ALL PRIVILEGES ON yalihanemlak_ultra.* TO 'yalihan_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

## 📦 ADIM 4: COMPOSER VE BAĞIMLILIKLAR

```bash
# Composer install
composer install --no-dev --optimize-autoloader

# APP_KEY oluştur
php artisan key:generate

# Storage link
php artisan storage:link
```

---

## 🗄️ ADIM 5: MİGRATION VE SEED

```bash
# Migration çalıştır
php artisan migrate --force

# Seed (opsiyonel - ilk kurulumda)
php artisan db:seed --class=DatabaseSeeder
```

---

## ⚡ ADIM 6: CACHE VE OPTİMİZASYON

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

## 🔄 ADIM 7: QUEUE WORKER (SUPERVISOR)

### Supervisor Config Oluştur

```bash
sudo nano /etc/supervisor/conf.d/yalihan-worker.conf
```

### Supervisor Config İçeriği

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

### Supervisor'ı Başlat

```bash
# Config'i yeniden yükle
sudo supervisorctl reread
sudo supervisorctl update

# Worker'ı başlat
sudo supervisorctl start yalihan-worker:*

# Durumu kontrol et
sudo supervisorctl status
```

---

## 🌐 ADIM 8: WEB SERVER YAPILANDIRMASI

### Nginx Config (Örnek)

```nginx
server {
    listen 80;
    server_name panel.yalihanemlak.com.tr;
    root /var/www/yalihanai/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Config (Örnek)

```apache
<VirtualHost *:80>
    ServerName panel.yalihanemlak.com.tr
    DocumentRoot /var/www/yalihanai/public

    <Directory /var/www/yalihanai/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## 🔒 ADIM 9: DOSYA İZİNLERİ

```bash
# Storage ve cache klasörlerine yazma izni
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# .env dosyası güvenliği
chmod 600 .env
```

---

## ✅ ADIM 10: KONTROL VE TEST

### Sistem Kontrolü

```bash
# Queue worker durumu
sudo supervisorctl status

# Log kontrolü
tail -f storage/logs/laravel.log

# Queue durumu
php artisan queue:work --once
```

### Tarayıcı Testi

1. `https://panel.yalihanemlak.com.tr` adresine git
2. Login sayfasını kontrol et
3. Admin paneline giriş yap
4. AI Dashboard'u kontrol et (`/admin/ai/dashboard`)

---

## 🔄 GÜNCELLEME (PULL)

### Güncelleme Adımları

```bash
# 1. Git pull
cd /var/www/yalihanai
git pull origin main

# 2. Composer update
composer install --no-dev --optimize-autoloader

# 3. Migration
php artisan migrate --force

# 4. Cache temizle ve yeniden oluştur
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Queue worker'ı yeniden başlat
sudo supervisorctl restart yalihan-worker:*
```

---

## 🐛 SORUN GİDERME

### Migration Hatası

```bash
# Migration durumunu kontrol et
php artisan migrate:status

# Belirli migration'ı rollback
php artisan migrate:rollback --step=1

# Tüm migration'ları sıfırla (DİKKAT: Veri kaybı!)
php artisan migrate:fresh
```

### Queue Worker Çalışmıyor

```bash
# Supervisor log kontrolü
sudo tail -f /var/log/supervisor/supervisord.log

# Worker log kontrolü
tail -f storage/logs/worker.log

# Manuel test
php artisan queue:work --once --queue=cortex-notifications
```

### Permission Hatası

```bash
# Storage izinleri
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 📝 NOTLAR

- **İlk Kurulum:** Tüm adımları sırasıyla uygulayın
- **Güncelleme:** Sadece "GÜNCELLEME (PULL)" bölümünü kullanın
- **Backup:** Migration öncesi veritabanı yedeği alın
- **Test:** Production'a geçmeden önce staging'de test edin

---

**Son Güncelleme:** 2025-12-01  
**Hazırlayan:** Yalıhan Bekçi AI System

