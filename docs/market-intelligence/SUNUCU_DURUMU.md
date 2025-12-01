# 🖥️ Sunucu Durumu ve URL'ler

**Tarih:** 2025-11-29  
**Son Kontrol:** 2025-11-29 23:15

---

## ✅ ÇALIŞAN SUNUCULAR

### 1. Laravel Sunucusu

**Durum:** ✅ **ÇALIŞIYOR**

**URL:** `http://127.0.0.1:8000`

**Komut:**
```bash
php artisan serve --host=127.0.0.1 --port=8000
```

**Kontrol:**
```bash
curl http://127.0.0.1:8000
```

**Market Intelligence URL'leri:**
- Dashboard: `http://127.0.0.1:8000/admin/market-intelligence/dashboard`
- Settings: `http://127.0.0.1:8000/admin/market-intelligence/settings`
- Compare: `http://127.0.0.1:8000/admin/market-intelligence/compare`
- Trends: `http://127.0.0.1:8000/admin/market-intelligence/trends`

**API Endpoints:**
- Active Regions: `http://127.0.0.1:8000/api/market-intelligence/active-regions`
- Sync (n8n): `http://127.0.0.1:8000/api/admin/market-intelligence/sync`

---

### 2. Context7 MCP Sunucuları

**Durum:** ✅ **ÇALIŞIYOR** (2 instance)

**Process:**
- Context7 MCP Instance 1: Aktif
- Context7 MCP Instance 2: Aktif

**Kullanım:** Cursor IDE ile otomatik entegre

---

## ⏳ BEKLEYEN SUNUCULAR

### 3. Yalıhan Bekçi MCP Sunucusu

**Durum:** ⏳ **BAŞLATILDI** (Hazır olması bekleniyor)

**URL:** `http://localhost:3334`

**Başlatma:**
```bash
cd yalihan-bekci
./bekci.sh start
```

**Durum Kontrol:**
```bash
./bekci.sh status
```

**Endpoint'ler:**
- Status: `http://localhost:3334/`
- Context7 Rules: `http://localhost:3334/context7/rules`
- System Status: `http://localhost:3334/system/status`

**Log:**
```bash
tail -f /tmp/yalihan-bekci.log
```

---

## 📋 TÜM URL'LER ÖZET

### Market Intelligence Web Routes

| Sayfa | URL | Durum |
|-------|-----|-------|
| Dashboard | `http://127.0.0.1:8000/admin/market-intelligence/dashboard` | ⏳ View bekleniyor |
| Settings | `http://127.0.0.1:8000/admin/market-intelligence/settings` | ⏳ View bekleniyor |
| Compare | `http://127.0.0.1:8000/admin/market-intelligence/compare` | ⏳ View bekleniyor |
| Trends | `http://127.0.0.1:8000/admin/market-intelligence/trends` | ⏳ View bekleniyor |

### Market Intelligence API Routes

| Endpoint | Method | URL | Durum |
|----------|--------|-----|-------|
| Active Regions | GET | `http://127.0.0.1:8000/api/market-intelligence/active-regions` | ✅ Hazır |
| Save Settings | POST | `http://127.0.0.1:8000/api/market-intelligence/settings` | ✅ Hazır |
| Delete Setting | DELETE | `http://127.0.0.1:8000/api/market-intelligence/settings/{id}` | ✅ Hazır |
| Toggle Setting | PATCH | `http://127.0.0.1:8000/api/market-intelligence/settings/{id}/toggle` | ✅ Hazır |
| Sync (n8n) | POST | `http://127.0.0.1:8000/api/admin/market-intelligence/sync` | ✅ Hazır |

### Yalıhan Bekçi MCP

| Endpoint | Method | URL | Durum |
|----------|--------|-----|-------|
| Status | GET | `http://localhost:3334/` | ⏳ Bekçi başlatıldı |
| Context7 Rules | GET | `http://localhost:3334/context7/rules` | ⏳ Bekçi başlatıldı |
| System Status | GET | `http://localhost:3334/system/status` | ⏳ Bekçi başlatıldı |

---

## 🚀 HIZLI BAŞLATMA

### Tüm Sunucuları Başlat

```bash
# 1. Laravel Sunucusu (Zaten çalışıyor)
# php artisan serve --host=127.0.0.1 --port=8000

# 2. Yalıhan Bekçi MCP
cd yalihan-bekci
./bekci.sh start

# 3. Durum Kontrol
./bekci.sh status
curl http://localhost:3334/
```

---

## 🔍 SORUN GİDERME

### Bekçi Başlamıyor

```bash
# Log kontrol
tail -f /tmp/yalihan-bekci.log

# Process kontrol
ps aux | grep node | grep 3334

# Port kontrol
lsof -i :3334

# Manuel başlatma
cd yalihan-bekci/server
npm run start
```

### Laravel Sunucusu Çalışmıyor

```bash
# Port kontrol
lsof -i :8000

# Yeniden başlat
php artisan serve --host=127.0.0.1 --port=8000
```

---

**Son Güncelleme:** 2025-11-29  
**Versiyon:** 1.0.0






