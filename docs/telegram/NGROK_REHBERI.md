# 🌐 ngrok Rehberi - Telegram Bot için

**Tarih:** 01 Aralık 2025  
**Amaç:** Local development için Telegram webhook bağlantısı

---

## 🤔 ngrok Nedir?

**ngrok**, local sunucunuzu (localhost) internet üzerinden erişilebilir hale getiren bir **tunnel (tünel)** servisidir.

### Basit Açıklama:
```
Local Sunucunuz (localhost:8000)
    ↓
ngrok Tüneli (https://abc123.ngrok-free.app)
    ↓
İnternet (Telegram sunucuları erişebilir)
```

---

## 🎯 Neden Gerekiyor?

### Sorun:
- Telegram sunucuları **localhost** veya **127.0.0.1** adresini göremez
- Webhook için **public URL** gerekir
- Local development'ta public URL yoktur

### Çözüm:
- ngrok, local sunucunuzu **public URL** ile erişilebilir yapar
- Telegram webhook'ları bu URL'e gönderilir
- ngrok, istekleri local sunucunuza yönlendirir

---

## 📋 KURULUM

### macOS:
```bash
# Homebrew ile
brew install ngrok/ngrok/ngrok

# veya manuel
# https://ngrok.com/download adresinden indirin
```

### Linux:
```bash
# Snap ile
snap install ngrok

# veya manuel indirme
```

### Windows:
```bash
# Chocolatey ile
choco install ngrok

# veya manuel indirme
```

---

## 🚀 KULLANIM

### 1. Temel Kullanım
```bash
# Laravel sunucusu çalışıyor olmalı
php artisan serve  # Port 8000

# Yeni terminal açın ve ngrok başlatın
ngrok http 8000
```

### 2. Çıktı
ngrok başladığında şunu göreceksiniz:
```
Session Status                online
Account                       your-email@example.com
Version                       3.27.0
Region                        Europe (eu)
Web Interface                 http://127.0.0.1:4040

Forwarding                    https://abc123.ngrok-free.app -> http://localhost:8000
```

**Önemli:** `https://abc123.ngrok-free.app` → Bu sizin public URL'iniz!

### 3. Webhook Ayarlama
```bash
# Telegram webhook'unu ngrok URL'ine ayarlayın
curl -X POST "https://api.telegram.org/botBOT_TOKEN/setWebhook?url=https://abc123.ngrok-free.app/api/telegram/webhook"
```

---

## 🔍 ngrok Web Interface

ngrok başladığında şu adrese gidebilirsiniz:
```
http://127.0.0.1:4040
```

Burada görebilirsiniz:
- Gelen istekler (Requests)
- Yanıtlar (Responses)
- İstatistikler (Statistics)

---

## ⚙️ GELİŞMİŞ KULLANIM

### 1. Özel Domain (Ücretli Plan)
```bash
ngrok http 8000 --domain=your-custom-domain.ngrok-free.app
```

### 2. Authentication (Güvenlik)
```bash
# Basic Auth ekle
ngrok http 8000 --basic-auth="username:password"
```

### 3. Region Seçimi
```bash
# Avrupa bölgesi
ngrok http 8000 --region=eu

# Amerika bölgesi
ngrok http 8000 --region=us
```

### 4. Config Dosyası
```bash
# ~/.ngrok2/ngrok.yml
authtoken: YOUR_AUTH_TOKEN
tunnels:
  laravel:
    addr: 8000
    proto: http
```

Kullanım:
```bash
ngrok start laravel
```

---

## 🎯 TELEGRAM BOT İÇİN KULLANIM

### Adım 1: Laravel Sunucusu
```bash
php artisan serve
# veya
php artisan serve --port=8000
```

### Adım 2: ngrok Başlat
```bash
ngrok http 8000
```

### Adım 3: URL'i Kopyala
```
Forwarding: https://abc123.ngrok-free.app -> http://localhost:8000
```

### Adım 4: Webhook Ayarla
```bash
curl -X POST "https://api.telegram.org/bot7834521220:AAFLKxa18v4UFPj46Fh-esL-8uMdmuXxy70/setWebhook?url=https://abc123.ngrok-free.app/api/telegram/webhook"
```

### Adım 5: Test Et
```
1. Telegram'da bot'a mesaj gönderin
2. ngrok web interface'te istekleri görün
3. Laravel log'larında mesajları kontrol edin
```

---

## ⚠️ ÖNEMLİ NOTLAR

### 1. ngrok Free Plan Limitleri
- **URL Değişimi:** Her başlatmada URL değişir
- **Bağlantı Limiti:** Sınırlı
- **Süre Limiti:** 2 saat (bazı durumlarda)

### 2. ngrok'u Açık Tutun
- ngrok'u kapatırsanız webhook çalışmaz
- Terminal'i kapatmayın
- `Ctrl+C` ile durdurmadan önce webhook'u kaldırın

### 3. Production için
- Production'da ngrok kullanmayın
- Cloudflare Tunnel veya doğrudan public IP kullanın
- SSL sertifikası zorunludur

---

## 🔧 SORUN GİDERME

### Sorun 1: "ngrok: command not found"
```bash
# Kurulum kontrolü
which ngrok

# PATH'e ekleyin veya yeniden kurun
```

### Sorun 2: "Port already in use"
```bash
# Farklı port kullanın
ngrok http 8001

# veya Laravel'i farklı portta başlatın
php artisan serve --port=8001
```

### Sorun 3: "Tunnel session failed"
```bash
# ngrok'u yeniden başlatın
# veya farklı region deneyin
ngrok http 8000 --region=us
```

### Sorun 4: Webhook 404 hatası
```bash
# 1. ngrok'un çalıştığını kontrol edin
curl http://localhost:4040/api/tunnels

# 2. Laravel sunucusunun çalıştığını kontrol edin
curl http://localhost:8000/api/telegram/webhook/test

# 3. Webhook URL'ini kontrol edin
curl "https://api.telegram.org/botBOT_TOKEN/getWebhookInfo"
```

---

## 📊 ngrok Web Interface Kullanımı

### 1. İstekleri İzleme
```
http://127.0.0.1:4040
```

Burada görebilirsiniz:
- Gelen tüm HTTP istekleri
- Request/Response detayları
- Timing bilgileri
- Header'lar

### 2. Replay İstekleri
- İstekleri tekrar gönderebilirsiniz
- Debug için çok kullanışlı

### 3. Request Inspector
- Her isteği detaylı inceleyebilirsiniz
- Request body, headers, query params

---

## 🎯 TELEGRAM BOT İÇİN ÖZEL AYARLAR

### 1. Otomatik Webhook Ayarlama Script
```bash
#!/bin/bash
# scripts/telegram-ngrok-setup.sh

# ngrok başlat (arka planda)
ngrok http 8000 > /tmp/ngrok.log 2>&1 &
sleep 3

# URL'i al
NGROK_URL=$(curl -s http://localhost:4040/api/tunnels | grep -o '"public_url":"https://[^"]*"' | head -1 | cut -d'"' -f4)

# Webhook ayarla
curl -X POST "https://api.telegram.org/bot7834521220:AAFLKxa18v4UFPj46Fh-esL-8uMdmuXxy70/setWebhook?url=${NGROK_URL}/api/telegram/webhook"

echo "✅ Webhook ayarlandı: ${NGROK_URL}/api/telegram/webhook"
```

### 2. ngrok URL'ini .env'e Kaydetme
```bash
# ngrok URL'ini al
NGROK_URL=$(curl -s http://localhost:4040/api/tunnels | grep -o '"public_url":"https://[^"]*"' | head -1 | cut -d'"' -f4)

# .env'e ekle
echo "TELEGRAM_WEBHOOK_URL=${NGROK_URL}/api/telegram/webhook" >> .env
```

---

## 🔄 ALTERNATİFLER

### 1. Cloudflare Tunnel (Production)
```bash
# Daha stabil, ücretsiz
cloudflared tunnel --url http://localhost:8000
```

### 2. localtunnel
```bash
npm install -g localtunnel
lt --port 8000
```

### 3. serveo
```bash
ssh -R 80:localhost:8000 serveo.net
```

---

## 📝 ÖZET

1. **ngrok nedir?** → Local sunucuyu public URL ile erişilebilir yapan tünel servisi
2. **Neden gerekli?** → Telegram webhook'ları için public URL gerekir
3. **Nasıl kullanılır?** → `ngrok http 8000` → URL'i kopyala → Webhook ayarla
4. **Önemli:** ngrok'u açık tutun, URL değişebilir

---

**Son Güncelleme:** 01 Aralık 2025

