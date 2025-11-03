# 📅 Takvim Entegrasyon Sistemi

**Tarih:** 27 Ekim 2025  
**Versiyon:** 1.0.0  
**Durum:** ✅ Temel Yapı Oluşturuldu

---

## 📋 GENEL BAKIŞ

Takvim entegrasyon sistemi, yazlık kiralama ilanlarının rezervasyon takvimini dış platformlarla (Airbnb, Booking.com, Google Calendar) senkronize etmek için geliştirilmiştir.

## 🏗️ MİMARİ

### Database Yapısı

#### 1. `ilan_takvim_sync` Tablosu

```sql
CREATE TABLE ilan_takvim_sync (
    id BIGINT PRIMARY KEY,
    ilan_id BIGINT (FK -> ilanlar),
    platform ENUM('airbnb', 'booking_com', 'google_calendar', 'calendar_dot_com'),
    external_calendar_id VARCHAR(255),
    external_listing_id VARCHAR(255),
    sync_enabled BOOLEAN DEFAULT FALSE,
    auto_sync BOOLEAN DEFAULT TRUE,
    last_sync_at TIMESTAMP,
    next_sync_at TIMESTAMP,
    sync_interval_minutes INT DEFAULT 60,
    sync_settings TEXT (JSON),
    api_key VARCHAR(255),
    api_secret VARCHAR(255),
    sync_status ENUM('active', 'paused', 'failed', 'disconnected'),
    last_error VARCHAR(255),
    last_error_at TIMESTAMP,
    sync_count INT DEFAULT 0,
    error_count INT DEFAULT 0,
    timestamps
);
```

**İndeksler:**
- `unique(ilan_id, platform)` - Bir ilan için her platformta tek kayıt
- `index(platform, sync_enabled)` - Aktif senkronizasyonlar
- `index(last_sync_at, next_sync_at)` - Otomatik senkronizasyon

#### 2. `yazlik_doluluk_durumlari` Tablosu

```sql
CREATE TABLE yazlik_doluluk_durumlari (
    id BIGINT PRIMARY KEY,
    ilan_id BIGINT (FK -> ilanlar),
    tarih DATE,
    durum ENUM('musait', 'rezerve', 'bloke', 'bakim', 'temizlik', 'kapali'),
    aciklama TEXT,
    ozel_fiyat JSON,
    rezervasyon_id BIGINT (FK -> yazlik_rezervasyonlar),
    created_by BIGINT (FK -> users),
    timestamps
);
```

**İndeksler:**
- `unique(ilan_id, tarih)` - Her tarih için tek durum
- `index(ilan_id, durum)` - Durum bazlı sorgular
- `index(tarih, durum)` - Tarih aralığı sorguları

---

## 📦 MODELLER

### IlanTakvimSync Model

**Dosya:** `app/Models/IlanTakvimSync.php`

**Metodlar:**
```php
// İlişkiler
public function ilan(): BelongsTo

// Scope'lar
public function scopeActive($query)
public function scopePlatform($query, $platform)
public function scopeNeedsSync($query)

// Helper Metodlar
public function markAsSynced()
public function markAsFailed($error)
```

**Kullanım:**
```php
$sync = IlanTakvimSync::where('ilan_id', 1)->where('platform', 'airbnb')->first();

if ($sync->needsSync()) {
    $result = $calendarService->syncCalendar(1, 'airbnb');
    if ($result['success']) {
        $sync->markAsSynced();
    } else {
        $sync->markAsFailed($result['message']);
    }
}
```

### YazlikDolulukDurumu Model

**Dosya:** `app/Models/YazlikDolulukDurumu.php`

**Metodlar:**
```php
// İlişkiler
public function ilan(): BelongsTo
public function rezervasyon(): BelongsTo
public function createdBy(): BelongsTo

// Scope'lar
public function scopeMusait($query)
public function scopeRezerve($query)
public function scopeBloke($query)
public function scopeByIlan($query, $ilanId)
public function scopeByDateRange($query, $startDate, $endDate)

// Durum Metodları
public function isMusait()
public function isRezerve()
public function isBloke()

// Durum Ayarlama
public function setRezerve($rezervasyonId = null, $aciklama = null)
public function setMusait()
public function setBloke($aciklama = null)
```

---

## 🔧 SERVİSLER

### CalendarSyncService

**Dosya:** `app/Services/CalendarSyncService.php`

**Metodlar:**

#### 1. syncCalendar()
Belirli bir ilan ve platform için senkronizasyon yapar.

```php
$service = new CalendarSyncService();
$result = $service->syncCalendar($ilanId, 'airbnb');

// Response:
[
    'success' => true,
    'message' => 'Senkronizasyon başarılı',
    'dates' => 5  // Senkronize edilen tarih sayısı
]
```

#### 2. syncAllCalendars()
Tüm aktif senkronizasyonları çalıştırır.

```php
$results = $service->syncAllCalendars();

// Response:
[
    'success' => 8,
    'failed' => 2,
    'total' => 10
]
```

#### 3. createSync()
Yeni bir senkronizasyon kaydı oluşturur.

```php
$sync = $service->createSync($ilanId, 'airbnb', [
    'external_listing_id' => 'abc123',
    'sync_enabled' => true,
    'auto_sync' => true,
    'sync_interval_minutes' => 60,
]);
```

---

## 🌐 PLATFORM ENTEGRASYONLARI

### Desteklenen Platformlar

1. **Airbnb**
   - API: Airbnb API v3
   - Gerekli: OAuth token + Listing ID
   - Sync: Availability calendar

2. **Booking.com**
   - API: Booking.com Connectivity API
   - Gerekli: API key + hotel ID
   - Sync: Availability data

3. **Google Calendar**
   - API: Google Calendar API v3
   - Gerekli: OAuth credentials + Calendar ID
   - Sync: Event-based sync

4. **Calendar.com**
   - API: Calendar.com API
   - Gerekli: API key
   - Sync: Availability sync

### Platform Metodları

```php
private function pushToAirbnb($sync, $dates)
{
    // Airbnb API entegrasyonu
    // POST /v3/availability_calendars/{calendar_id}/update
}

private function pushToBookingCom($sync, $dates)
{
    // Booking.com API entegrasyonu
    // POST /v3/hotels/{hotel_id}/availability
}

private function pushToGoogleCalendar($sync, $dates)
{
    // Google Calendar API entegrasyonu
    // Insert events for blocked dates
}
```

---

## 📊 DOLULUK DURUMLARI

### Durum Tipleri

```php
'musait'     // Rezervasyon yapılabilir
'rezerve'    // Rezervasyon var
'bloke'      // Bloke edilmiş (sebep belirtilmeli)
'bakim'      // Bakım/temizlik
'temizlik'   // Temizlik yapılıyor
'kapali'     // Kapalı
```

### Kullanım Örnekleri

```php
// Rezervasyon oluşturulduğunda
YazlikDolulukDurumu::create([
    'ilan_id' => $ilan->id,
    'tarih' => $checkIn,
    'durum' => 'rezerve',
    'rezervasyon_id' => $rezervasyon->id,
]);

// Rezervasyon iptal edildiğinde
$doluluk = YazlikDolulukDurumu::where('rezervasyon_id', $rezervasyonId)->first();
$doluluk->setMusait();

// Manuel blokaj
YazlikDolulukDurumu::updateOrCreate(
    ['ilan_id' => $ilan->id, 'tarih' => $tarih],
    ['durum' => 'bloke', 'aciklama' => 'Özel etkinlik']
);

// Tarih aralığı kontrolü
$doluluklar = YazlikDolulukDurumu::byIlan($ilanId)
    ->byDateRange($checkIn, $checkOut)
    ->whereNotIn('durum', ['musait'])
    ->count();

if ($doluluklar > 0) {
    throw new Exception('Bu tarihlerde rezervasyon yapılamaz');
}
```

---

## ⚙️ OTOMATIK SENKRONİZASYON

### Cron Job Kurulumu

**Dosya:** `app/Console/Commands/SyncCalendars.php`

```php
php artisan make:command SyncCalendars
```

**Schedule:** Her saat başı çalışır

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('calendars:sync')->hourly();
}
```

**Kod:**
```php
public function handle()
{
    $service = new CalendarSyncService();
    $results = $service->syncAllCalendars();

    $this->info("Synced: {$results['success']}, Failed: {$results['failed']}");
}
```

---

## 📝 API ENDPOINT'LERİ

### Takvim Sync API

```yaml
POST /api/admin/calendars/sync/{ilan}
Body: {
    "platform": "airbnb",
    "external_listing_id": "abc123"
}

GET /api/admin/calendars/{ilan}/syncs
Response: [{
    "id": 1,
    "platform": "airbnb",
    "sync_status": "active",
    "last_sync_at": "2025-10-27 10:00:00",
    "sync_count": 15
}]

POST /api/admin/calendars/{ilan}/manual-sync
Body: {
    "platform": "airbnb"
}
Response: {
    "success": true,
    "message": "Senkronizasyon başarılı",
    "dates": 5
}
```

### Doluluk Durumları API

```yaml
GET /api/admin/doluluk/{ilan}/calendar
Query: ?month=10&year=2025
Response: [{
    "date": "2025-10-15",
    "durum": "rezerve",
    "rezervasyon_id": 123
}]

POST /api/admin/doluluk/{ilan}/block
Body: {
    "start_date": "2025-10-15",
    "end_date": "2025-10-20",
    "durum": "bloke",
    "aciklama": "Bakım"
}
```

---

## 🎯 KULLANIM SENARYOLARI

### Senaryo 1: İlk Senkronizasyon Kurulumu

```php
// 1. Airbnb'de listing oluştur
$airbnbListingId = "12345678";

// 2. Senkronizasyon kaydı oluştur
$sync = $calendarService->createSync($ilanId, 'airbnb', [
    'external_listing_id' => $airbnbListingId,
    'sync_enabled' => true,
    'auto_sync' => true,
]);

// 3. Manuel senkronizasyon yap
$result = $calendarService->syncCalendar($ilanId, 'airbnb');
```

### Senaryo 2: Rezervasyon Sonrası Otomatik Sync

```php
// Rezervasyon oluşturulduğunda
$rezervasyon = YazlikRezervasyon::create([...]);

// Doluluk durumlarını güncelle
YazlikDolulukDurumu::create([
    'ilan_id' => $ilan->id,
    'tarih' => $rezervasyon->check_in,
    'durum' => 'rezerve',
    'rezervasyon_id' => $rezervasyon->id,
]);

// Aktif senkronizasyonları tetikle
$syncs = IlanTakvimSync::where('ilan_id', $ilan->id)
                       ->where('auto_sync', true)
                       ->where('sync_enabled', true)
                       ->get();

foreach ($syncs as $sync) {
    dispatch(new SyncCalendarJob($ilan->id, $sync->platform));
}
```

### Senaryo 3: Tarih Bloklama

```php
// Bakım için tarih bloğu
$dates = CarbonPeriod::create($startDate, $endDate);

foreach ($dates as $date) {
    YazlikDolulukDurumu::updateOrCreate(
        [
            'ilan_id' => $ilanId,
            'tarih' => $date->format('Y-m-d')
        ],
        [
            'durum' => 'bakim',
            'aciklama' => 'Yıllık bakım',
        ]
    );
}

// Senkronizasyonları güncelle
$calendarService->syncCalendar($ilanId);
```

---

## 📊 RAPORLAMA

### Sync İstatistikleri

```php
$stats = IlanTakvimSync::selectRaw('
    platform,
    COUNT(*) as total,
    SUM(CASE WHEN sync_enabled = 1 THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN sync_status = "active" THEN 1 ELSE 0 END) as healthy,
    SUM(sync_count) as total_syncs
')->groupBy('platform')->get();

// Response:
[
    ['platform' => 'airbnb', 'total' => 10, 'active' => 8, 'healthy' => 7, 'total_syncs' => 1250],
    ['platform' => 'booking_com', 'total' => 5, 'active' => 3, 'healthy' => 3, 'total_syncs' => 450],
]
```

### Doluluk Oranı

```php
$startDate = now()->startOfMonth();
$endDate = now()->endOfMonth();

$stats = YazlikDolulukDurumu::where('ilan_id', $ilanId)
    ->whereBetween('tarih', [$startDate, $endDate])
    ->selectRaw('
        COUNT(*) as total_days,
        SUM(CASE WHEN durum = "rezerve" THEN 1 ELSE 0 END) as reserved_days,
        SUM(CASE WHEN durum = "bloke" THEN 1 ELSE 0 END) as blocked_days
    ')
    ->first();

$dolulukOrani = ($stats->reserved_days / $stats->total_days) * 100;
```

---

## 🔐 GÜVENLİK

### API Key Yönetimi

```php
// Şifreli saklama
'api_key' => encrypt($apiKey),
'api_secret' => encrypt($apiSecret),

// Kullanımda decrypt
$apiKey = decrypt($sync->api_key);
```

### Rate Limiting

```php
// Her platform için farklı rate limit
$rateLimits = [
    'airbnb' => 600, // 10 requests per minute
    'booking_com' => 120, // 2 requests per minute
];
```

---

## 🚀 GELECEK GELİŞTİRMELER

1. **Çoklu Platform Senkronizasyonu**
   - Aynı anda birden fazla platform
   - Konflik çözümü (önce rezerve olan kazanır)

2. **Akıllı Fiyat Senkronizasyonu**
   - Dinamik fiyatlandırma
   - Sezona göre otomatik fiyat güncelleme

3. **Rezervasyon Onay Akışı**
   - Otomatik onay
   - Manuel onay workflow'u

4. **Bildirim Sistemi**
   - Email/SMS bildirimleri
   - Slack/Telegram entegrasyonu

---

## 📚 REFERANSLAR

- [İlan Modülleri Analiz Raporu](ILAN_MODULLERI_SISTEM_ANALIZI.md)
- [Yazlık Kiralama Sistemi](YAZLIK_KIRALAMA_SISTEMI.md)
- [Sistem Analiz Özeti](SISTEM_ANALIZ_OZETI.md)

---

**Oluşturulma Tarihi:** 27 Ekim 2025  
**Durum:** ✅ Temel Yapı Oluşturuldu  
**Sonraki Adım:** Platform API entegrasyonları
