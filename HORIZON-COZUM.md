# 🔧 Horizon Çalışmıyor - Çözüm

**Tarih:** 2025-11-04  
**Sorun:** `http://127.0.0.1:8000/horizon/monitoring` açılmıyor

---

## ✅ TESPİT

```yaml
Horizon Kurulu: ✅ Evet (composer.json'da var)
Config Dosyası: ✅ Var (config/horizon.php)
Worker Çalışıyor: ❌ Hayır (başlatılmamış)
```

---

## 🎯 SEBEP

**Horizon worker başlatılmamış!**

```yaml
Horizon'un çalışması için:
  1. Laravel server çalışmalı ✅ (php artisan serve)
  2. Horizon worker çalışmalı ❌ (php artisan horizon)
  
Şu anda sadece 1 var, 2 eksik!
```

---

## 🚀 ÇÖZÜM (Hemen Şimdi)

### Terminal 1 (Zaten çalışıyor):
```bash
php artisan serve
# Server: http://127.0.0.1:8000
```

### Terminal 2 (YENİ - Horizon worker):
```bash
cd /Users/macbookpro/Projects/yalihanemlakwarp
php artisan horizon

# Yanıt:
# Horizon started successfully.
# Processing jobs from: default, emails, notifications
```

### Kontrol:
```bash
# Artık çalışmalı:
open http://127.0.0.1:8000/horizon
```

---

## 📊 HORIZON vs TELESCOPE

### Telescope (Zaten çalışıyor):
```yaml
URL: http://127.0.0.1:8000/telescope
Ne yapar: Request debugging, error tracking
Worker gerekli: ❌ Hayır (otomatik çalışır)
```

### Horizon (Çalışmıyor):
```yaml
URL: http://127.0.0.1:8000/horizon
Ne yapar: Queue monitoring, background jobs
Worker gerekli: ✅ EVET (manuel başlatılmalı!)
```

**Fark:** Telescope otomatik çalışır, Horizon worker gerektirir!

---

## 🎯 KALICI ÇÖZÜM (Opsiyonel)

### Development için:
```bash
# Her proje açılışında:
Terminal 1: php artisan serve
Terminal 2: php artisan horizon
```

### VS Code Tasks (Otomatik):
```json
// .vscode/tasks.json
{
    "version": "2.0.0",
    "tasks": [
        {
            "label": "Start Laravel Server",
            "type": "shell",
            "command": "php artisan serve",
            "isBackground": true
        },
        {
            "label": "Start Horizon",
            "type": "shell",
            "command": "php artisan horizon",
            "isBackground": true
        }
    ]
}
```

### Tmux/Screen (Advanced):
```bash
# Tek komutla her şeyi başlat:
tmux new-session -d -s yalihan 'php artisan serve'
tmux split-window -v 'php artisan horizon'
tmux attach-session -t yalihan
```

---

## ⚠️ ÖZEL NOTLAR

### 1. Redis Gerekli:
```bash
# Eğer Redis yoksa:
brew install redis         # macOS
brew services start redis

# Kontrol:
redis-cli ping
# Yanıt: PONG
```

### 2. Queue Connection:
```env
# .env dosyasında:
QUEUE_CONNECTION=redis  # (database değil!)
```

### 3. Horizon Kullanımı:
```yaml
Gerekli mi?
  ✅ Email/SMS queue varsa
  ✅ Image processing (background)
  ✅ Report generation
  ❌ Sadece sync operations varsa

Sizin projede:
  ? Email queue var mı?
  ? Background job var mı?
  ? Varsa → Horizon gerekli
  ? Yoksa → Gereksiz (kapatabilirsiniz)
```

---

## 🎊 SONUÇ

**Hızlı Çözüm:**
```bash
# Yeni terminal aç:
cd /Users/macbookpro/Projects/yalihanemlakwarp
php artisan horizon

# Artık çalışır:
http://127.0.0.1:8000/horizon
```

**Kalıcı Çözüm:**
- VS Code tasks ekle (otomatik başlat)
- Ya da tmux/screen kullan

**İhtiyaç Kontrolü:**
- Queue kullanıyorsanız → Çalıştırın
- Queue kullanmıyorsanız → Gerek yok

---

**Detaylı Açıklama:** `HORIZON-VS-TELESCOPE-ACIKLAMA.md`

