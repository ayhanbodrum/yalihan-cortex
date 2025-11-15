# Takvim Senkronizasyon API Dokümantasyonu

**Tarih:** 27 Ekim 2025  
**Durum:** ✅ Tamamlandı

---

## 📋 Genel Bakış

Bu API, yazlık ilanları için takvim senkronizasyon işlemlerini yönetir:

- ✅ Airbnb, Booking.com, Google Calendar entegrasyonları
- ✅ Doluluk durumu yönetimi
- ✅ Manuel senkronizasyon
- ✅ Otomatik senkronizasyon
- ✅ Tarih engelleme/boşaltma

---

## 🔗 API Endpoints

**Base URL:** `/api/admin/calendars/{ilanId}`

**Authentication:** Gerekli (middleware: web, auth)

---

### 1. Senkronizasyonları Listele

**GET** `/api/admin/calendars/{ilanId}/syncs`

Bir ilanın tüm senkronizasyon ayarlarını getirir.

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "ilan_id": 123,
            "platform": "airbnb",
            "external_listing_id": "airbnb-12345",
            "sync_enabled": true,
            "sync_token": null,
            "last_sync_at": "2025-10-27 12:00:00",
            "last_sync_status": "success",
            "created_at": "2025-10-27 10:00:00",
            "updated_at": "2025-10-27 12:00:00"
        }
    ]
}
```

---

### 2. Senkronizasyon Oluştur

**POST** `/api/admin/calendars/{ilanId}/syncs`

Yeni bir senkronizasyon ayarı oluşturur.

**Request Body:**

```json
{
    "platform": "airbnb",
    "external_listing_id": "airbnb-12345",
    "sync_enabled": true
}
```

**Validation:**

- `platform`: required|in:airbnb,booking_com,google_calendar
- `external_listing_id`: required|string
- `sync_enabled`: boolean

**Response:**

```json
{
    "success": true,
    "message": "Senkronizasyon oluşturuldu",
    "data": {
        "id": 1,
        "ilan_id": 123,
        "platform": "airbnb",
        "external_listing_id": "airbnb-12345",
        "sync_enabled": true,
        "created_at": "2025-10-27 12:00:00"
    }
}
```

---

### 3. Manuel Senkronizasyon

**POST** `/api/admin/calendars/{ilanId}/manual-sync`

Manuel olarak senkronizasyonu tetikler.

**Request Body:**

```json
{
    "platform": "airbnb"
}
```

**Validation:**

- `platform`: required|in:airbnb,booking_com,google_calendar

**Response:**

```json
{
    "success": true,
    "message": "Senkronizasyon başarılı",
    "data": {
        "dates": 5
    }
}
```

---

### 4. Takvim Bilgilerini Getir

**GET** `/api/admin/calendars/{ilanId}/calendar`

İlanın 90 günlük takvim/doluluk bilgilerini getirir.

**Response:**

```json
{
    "success": true,
    "data": {
        "ilan_id": 123,
        "availability": [
            {
                "date": "2025-11-01",
                "status": "available",
                "reason": null
            },
            {
                "date": "2025-11-05",
                "status": "reserved",
                "reason": "Rezervasyon - Müşteri X"
            },
            {
                "date": "2025-11-10",
                "status": "blocked",
                "reason": "Bakım"
            }
        ]
    }
}
```

**Status Değerleri:**

- `available`: Müsait
- `reserved`: Rezerve
- `blocked`: Engellenmiş
- `maintenance`: Bakım

---

### 5. Tarih Engelleme

**POST** `/api/admin/calendars/{ilanId}/block`

Belirtilen tarihleri engeller (bloke eder).

**Request Body:**

```json
{
    "dates": ["2025-11-15", "2025-11-16", "2025-11-17"],
    "reason": "Bakım nedeniyle kapalı"
}
```

**Validation:**

- `dates`: required|array
- `dates.*`: required|date
- `reason`: nullable|string

**Response:**

```json
{
    "success": true,
    "message": "Tarihler engellendi",
    "data": [
        {
            "id": 1,
            "ilan_id": 123,
            "tarih": "2025-11-15",
            "durum": "blocked",
            "not": "Bakım nedeniyle kapalı"
        }
    ]
}
```

---

### 6. Senkronizasyon Güncelle

**POST** `/api/admin/calendars/{ilanId}/syncs/{syncId}`

Senkronizasyon ayarlarını günceller.

**Request Body:**

```json
{
    "external_listing_id": "airbnb-99999",
    "sync_enabled": false
}
```

**Validation:**

- `external_listing_id`: string
- `sync_enabled`: boolean

**Response:**

```json
{
    "success": true,
    "message": "Senkronizasyon güncellendi",
    "data": {
        "id": 1,
        "ilan_id": 123,
        "platform": "airbnb",
        "external_listing_id": "airbnb-99999",
        "sync_enabled": false,
        "updated_at": "2025-10-27 13:00:00"
    }
}
```

---

### 7. Senkronizasyon Sil

**DELETE** `/api/admin/calendars/{ilanId}/syncs/{syncId}`

Senkronizasyon ayarını siler.

**Response:**

```json
{
    "success": true,
    "message": "Senkronizasyon silindi"
}
```

---

## 🔄 Kullanım Örnekleri

### JavaScript (Fetch API)

```javascript
// Senkronizasyonları listele
async function getSyncs(ilanId) {
    const response = await fetch(`/api/admin/calendars/${ilanId}/syncs`, {
        method: 'GET',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    });

    return await response.json();
}

// Yeni senkronizasyon oluştur
async function createSync(ilanId, platform, listingId) {
    const response = await fetch(`/api/admin/calendars/${ilanId}/syncs`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            platform: platform,
            external_listing_id: listingId,
            sync_enabled: true,
        }),
    });

    return await response.json();
}

// Manuel senkronizasyon
async function manualSync(ilanId, platform) {
    const response = await fetch(`/api/admin/calendars/${ilanId}/manual-sync`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            platform: platform,
        }),
    });

    return await response.json();
}

// Tarih engelleme
async function blockDates(ilanId, dates, reason) {
    const response = await fetch(`/api/admin/calendars/${ilanId}/block`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            dates: dates,
            reason: reason,
        }),
    });

    return await response.json();
}
```

---

## 🔒 Authentication

Tüm endpoint'ler `web` ve `auth` middleware'leri ile korunur.

**Gereksinim:**

- Kullanıcı giriş yapmış olmalı
- Session cookie geçerli olmalı

---

## 📊 Hata Yönetimi

### 422 Validation Error

```json
{
    "success": false,
    "errors": {
        "platform": ["Platform seçimi geçersiz"]
    }
}
```

### 404 Not Found

```json
{
    "success": false,
    "message": "İlan bulunamadı"
}
```

### 500 Server Error

```json
{
    "success": false,
    "message": "Senkronizasyon hatası: <hata mesajı>"
}
```

---

## ✅ Tamamlanan İşler

- [x] Controller oluşturuldu
- [x] Route'lar eklendi
- [x] API endpoint'leri tamamlandı
- [x] Dokümantasyon oluşturuldu
- [ ] Frontend entegrasyonu (ileri tarih)
- [ ] Test'ler (ileri tarih)

---

**Son Güncelleme:** 27 Ekim 2025  
**Durum:** ✅ Tamamlandı
