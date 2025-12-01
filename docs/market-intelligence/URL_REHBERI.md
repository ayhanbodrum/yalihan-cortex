# 🔗 Market Intelligence - URL Rehberi

**Tarih:** 2025-11-29  
**Base URL:** `http://127.0.0.1:8000` (Local) veya `https://yourdomain.com` (Production)

---

## 📋 WEB ROUTES (Admin Panel)

### Dashboard
```
GET /admin/market-intelligence/dashboard
```
**Açıklama:** Pazar İstihbaratı ana dashboard sayfası  
**Durum:** ⏳ View oluşturulmalı

### Settings (Bölge Seçim Paneli)
```
GET /admin/market-intelligence/settings
```
**Açıklama:** Kullanıcının bölge seçim yapacağı panel  
**Durum:** ⏳ View oluşturulmalı

### Fiyat Karşılaştırma
```
GET /admin/market-intelligence/compare
GET /admin/market-intelligence/compare/{ilan_id}
```
**Açıklama:** İlan fiyat karşılaştırması sayfası  
**Durum:** ⏳ View oluşturulmalı

### Piyasa Trendleri
```
GET /admin/market-intelligence/trends
```
**Açıklama:** Piyasa trendleri ve grafikler  
**Durum:** ⏳ View oluşturulmalı

---

## 🔌 API ROUTES

### 1. Aktif Bölgeleri Getir (n8n Bot için)

```
GET /api/market-intelligence/active-regions
```

**Açıklama:** n8n bot'unun hangi bölgeleri tarayacağını döndürür

**Middleware:** `['web', 'auth']` - Giriş yapmış kullanıcı gerekli

**Örnek Yanıt:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "il_id": 7,
            "il_adi": "Antalya",
            "ilce_id": 123,
            "ilce_adi": "Muratpaşa",
            "mahalle_id": null,
            "mahalle_adi": null,
            "is_active": true,
            "priority": 1,
            "is_global": false,
            "location_text": "Antalya - Muratpaşa"
        }
    ],
    "message": "Aktif bölgeler listelendi"
}
```

**Test:**
```bash
curl -X GET "http://127.0.0.1:8000/api/market-intelligence/active-regions" \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=..." # Giriş yapmış kullanıcı cookie'si gerekli
```

---

### 2. Bölge Ayarları Kaydet

```
POST /api/market-intelligence/settings
```

**Açıklama:** Kullanıcının seçtiği bölgeleri kaydeder

**Middleware:** `['web', 'auth']` - Giriş yapmış kullanıcı gerekli

**Request Body:**
```json
{
    "regions": [
        {
            "il_id": 7,
            "ilce_id": 123,
            "mahalle_id": 456,
            "status": true,
            "priority": 1
        }
    ]
}
```

**Örnek Yanıt:**
```json
{
    "success": true,
    "data": {
        "saved_count": 1
    },
    "message": "Bölge ayarları başarıyla kaydedildi"
}
```

**Test:**
```bash
curl -X POST "http://127.0.0.1:8000/api/market-intelligence/settings" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=..." \
  -d '{
    "regions": [
        {
            "il_id": 7,
            "ilce_id": 123,
            "status": true,
            "priority": 1
        }
    ]
}'
```

---

### 3. Bölge Ayarı Sil

```
DELETE /api/market-intelligence/settings/{id}
```

**Açıklama:** Belirli bir bölge ayarını siler

**Middleware:** `['web', 'auth']` - Giriş yapmış kullanıcı gerekli

**Test:**
```bash
curl -X DELETE "http://127.0.0.1:8000/api/market-intelligence/settings/1" \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=..."
```

---

### 4. Bölge Ayarı Aktif/Pasif Yap

```
PATCH /api/market-intelligence/settings/{id}/toggle
```

**Açıklama:** Bölge ayarını aktif/pasif yapar

**Middleware:** `['web', 'auth']` - Giriş yapmış kullanıcı gerekli

**Test:**
```bash
curl -X PATCH "http://127.0.0.1:8000/api/market-intelligence/settings/1/toggle" \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=..."
```

---

### 5. Veri Senkronizasyonu (n8n Bot için)

```
POST /api/admin/market-intelligence/sync
```

**Açıklama:** n8n bot'unun çektiği ilanları Laravel'e gönderir

**Middleware:** Yok (CSRF exempt, n8n secret middleware ile korumalı)

**Request Body:**
```json
{
    "source": "sahibinden",
    "region": {
        "il_id": 7,
        "ilce_id": 123
    },
    "listings": [
        {
            "external_id": "123456",
            "url": "https://sahibinden.com/ilan/123456",
            "title": "Deniz Manzaralı 3+1 Daire",
            "price": 1500000,
            "currency": "TRY",
            "location_il": "Antalya",
            "location_ilce": "Muratpaşa",
            "location_mahalle": "Konyaaltı",
            "m2_brut": 120,
            "m2_net": 100,
            "room_count": "3+1",
            "listing_date": "2025-11-15",
            "snapshot_data": {
                "test": true
            }
        }
    ]
}
```

**Örnek Yanıt:**
```json
{
    "success": true,
    "data": {
        "synced_count": 1,
        "new_count": 1,
        "updated_count": 0,
        "source": "sahibinden"
    },
    "message": "1 ilan senkronize edildi (1 yeni, 0 güncellendi)"
}
```

**Test:**
```bash
curl -X POST "http://127.0.0.1:8000/api/admin/market-intelligence/sync" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "source": "sahibinden",
    "listings": [
        {
            "external_id": "TEST_123",
            "title": "Test İlan",
            "price": 1500000,
            "currency": "TRY",
            "location_il": "Antalya",
            "listing_date": "2025-11-15"
        }
    ]
}'
```

---

## 🛡️ YALIHAN BEKÇİ MCP SUNUCU

### Bekçi MCP Sunucu URL'leri

**Base URL:** `http://localhost:3334`

**Endpoint'ler:**
```
GET  /                          - Bekçi durumu
GET  /context7/rules            - Context7 kuralları
GET  /system/status             - Sistem yapısı
POST /run-tests                 - Test çalıştır
GET  /knowledge                 - Bilgi tabanı sorgulama
GET  /reports                   - Rapor oluşturma
```

**Test:**
```bash
# Bekçi durumu
curl http://localhost:3334/

# Context7 kuralları
curl http://localhost:3334/context7/rules

# Sistem yapısı
curl http://localhost:3334/system/status
```

---

## 🚀 SUNUCU BAŞLATMA

### Laravel Sunucusu

```bash
# Zaten çalışıyor
php artisan serve --host=127.0.0.1 --port=8000
```

**URL:** `http://127.0.0.1:8000`

---

### Yalıhan Bekçi MCP Sunucusu

```bash
cd yalihan-bekci
./bekci.sh start
```

**URL:** `http://localhost:3334`

**Komutlar:**
```bash
./bekci.sh start    # Başlat
./bekci.sh stop     # Durdur
./bekci.sh status   # Durum kontrol
./bekci.sh restart  # Yeniden başlat
./bekci.sh kurallar # Context7 kurallarını göster
./bekci.sh sistem   # Sistem yapısını göster
```

---

## 📊 ÖZET TABLO

| Endpoint | Method | URL | Durum |
|----------|--------|-----|-------|
| Dashboard | GET | `/admin/market-intelligence/dashboard` | ⏳ View bekleniyor |
| Settings | GET | `/admin/market-intelligence/settings` | ⏳ View bekleniyor |
| Compare | GET | `/admin/market-intelligence/compare` | ⏳ View bekleniyor |
| Trends | GET | `/admin/market-intelligence/trends` | ⏳ View bekleniyor |
| Active Regions | GET | `/api/market-intelligence/active-regions` | ✅ Hazır |
| Save Settings | POST | `/api/market-intelligence/settings` | ✅ Hazır |
| Delete Setting | DELETE | `/api/market-intelligence/settings/{id}` | ✅ Hazır |
| Toggle Setting | PATCH | `/api/market-intelligence/settings/{id}/toggle` | ✅ Hazır |
| Sync (n8n) | POST | `/api/admin/market-intelligence/sync` | ✅ Hazır |
| Bekçi Status | GET | `http://localhost:3334/` | ⏳ Bekçi başlatılmalı |

---

## 🔧 HIZLI TEST

### 1. Laravel Sunucusu Kontrol

```bash
curl http://127.0.0.1:8000
```

### 2. Bekçi MCP Sunucusu Kontrol

```bash
curl http://localhost:3334/
```

### 3. Market Intelligence API Test

```bash
php tests/manual/test-market-intelligence.php
```

---

**Son Güncelleme:** 2025-11-29  
**Versiyon:** 1.0.0






