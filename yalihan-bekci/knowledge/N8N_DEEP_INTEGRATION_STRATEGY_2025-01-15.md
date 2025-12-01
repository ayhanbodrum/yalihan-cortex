# 🔄 n8n Derin Entegrasyon Stratejisi - Yalıhan Emlak

**Tarih:** 15 Ocak 2025  
**Durum:** 📊 Analiz Tamamlandı  
**Hedef:** Tüm modülleri n8n ile entegre etmek

---

## 📊 PROJE YAPISI ANALİZİ

### 🏗️ Modül Yapısı (15 Modül)

#### 1. **Emlak Modülü** (Core)
- **Models:** Ilan, IlanFotografi, IlanOzellik, Proje, Feature
- **Events:** `IlanCreated` ✅ (Mevcut)
- **Observers:** `IlanObserver` ✅ (Mevcut)
- **Potansiyel n8n Entegrasyonları:**
  - ✅ İlan oluşturuldu → n8n (YAPILDI)
  - ⚠️ İlan güncellendi → n8n (YAPILMADI)
  - ⚠️ İlan fiyat değişti → n8n (YAPILMADI)
  - ⚠️ İlan silindi → n8n (YAPILMADI)
  - ⚠️ İlan status değişti → n8n (YAPILMADI)
  - ⚠️ Fotoğraf eklendi → n8n (YAPILMADI)

#### 2. **CRM Modülü** (Müşteri Yönetimi)
- **Models:** Kisi, Musteri, Randevu, Aktivite, Etiket, IlanTalepEslesme
- **Events:** YOK ❌
- **Observers:** `KisiObserver` (Kısmi)
- **Potansiyel n8n Entegrasyonları:**
  - ⚠️ Yeni kişi oluşturuldu → n8n (N8nService'de var ama kullanılmıyor)
  - ⚠️ Kişi güncellendi → n8n
  - ⚠️ Randevu oluşturuldu → n8n (WhatsApp hatırlatma)
  - ⚠️ Randevu yaklaşıyor → n8n (1 saat önce hatırlatma)
  - ⚠️ Aktivite eklendi → n8n
  - ⚠️ Etiket eklendi → n8n

#### 3. **Talep Modülü** (Müşteri Talepleri)
- **Models:** Talep, TalepAnaliz, IlanTalepEslesme
- **Events:** YOK ❌
- **Observers:** YOK ❌
- **Potansiyel n8n Entegrasyonları:**
  - ⚠️ Yeni talep oluşturuldu → n8n
  - ⚠️ Talep eşleştirildi → n8n (Danışmana bildirim)
  - ⚠️ Talep status değişti → n8n
  - ⚠️ Talep analiz edildi → n8n

#### 4. **Finans Modülü** (Finansal İşlemler)
- **Models:** FinansalIslem, Komisyon
- **Events:** YOK ❌
- **Observers:** YOK ❌
- **Potansiyel n8n Entegrasyonları:**
  - ⚠️ Finansal işlem oluşturuldu → n8n (Muhasebe entegrasyonu)
  - ⚠️ Komisyon hesaplandı → n8n (Danışmana bildirim)
  - ⚠️ Ödeme alındı → n8n (Email/SMS bildirimi)

#### 5. **Takım Yönetimi Modülü**
- **Models:** Gorev, Proje, TakimUyesi, GorevDosya, GorevTakip
- **Events:** YOK ❌
- **Observers:** YOK ❌
- **Potansiyel n8n Entegrasyonları:**
  - ⚠️ Görev oluşturuldu → n8n (Telegram bildirimi)
  - ⚠️ Görev tamamlandı → n8n
  - ⚠️ Görev deadline yaklaşıyor → n8n (1 gün önce hatırlatma)
  - ⚠️ Telegram bot entegrasyonu (Mevcut TelegramBotService var)

#### 6. **Analitik Modülü**
- **Controllers:** DashboardController, IstatistikController, RaporController
- **Potansiyel n8n Entegrasyonları:**
  - ⚠️ Günlük rapor → n8n (Email/Slack)
  - ⚠️ Haftalık performans → n8n
  - ⚠️ Aylık özet → n8n

#### 7. **Bildirimler Modülü**
- **Potansiyel n8n Entegrasyonları:**
  - ⚠️ Bildirim oluşturuldu → n8n (Multi-channel: Email, SMS, WhatsApp, Telegram)

#### 8. **Arsa Modülü**
- **Potansiyel n8n Entegrasyonları:**
  - ⚠️ Arsa hesaplama tamamlandı → n8n
  - ⚠️ TKGM verisi alındı → n8n

#### 9. **Yazlık Kiralama Modülü** (Event Model)
- **Models:** Event (Rezervasyon), Season (Sezon)
- **Potansiyel n8n Entegrasyonları:**
  - ⚠️ Rezervasyon oluşturuldu → n8n (Müşteriye email)
  - ⚠️ Rezervasyon onaylandı → n8n (WhatsApp bildirimi)
  - ⚠️ Rezervasyon iptal edildi → n8n
  - ⚠️ Check-in yaklaşıyor → n8n (1 gün önce hatırlatma)
  - ⚠️ Check-out tamamlandı → n8n (Temizlik ekibine bildirim)

#### 10. **Auth Modülü**
- **Models:** User, Role
- **Potansiyel n8n Entegrasyonları:**
  - ⚠️ Yeni kullanıcı kaydı → n8n (Email doğrulama)
  - ⚠️ Kullanıcı giriş yaptı → n8n (Güvenlik logu)

---

## 🎯 MEVCUT n8n ENTEGRASYONLARI

### ✅ Yapılan Entegrasyonlar

1. **NotifyN8nAboutNewIlan Job** ✅
   - İlan oluşturulduğunda n8n'e bildirim gönderir
   - Queue Job ile asenkron çalışır
   - `X-N8N-SECRET` header ile güvenlik

2. **N8nService** ✅
   - `sendNewIlan()` - İlan bildirimi
   - `sendNewKisi()` - Kişi bildirimi (KULLANILMIYOR)
   - `sendIlanStatusChanged()` - Status değişikliği (KULLANILMIYOR)
   - `sendNotification()` - Genel bildirim (KULLANILMIYOR)
   - `sendDailyReport()` - Günlük rapor (KULLANILMIYOR)
   - `sendRandevuHatirlatma()` - Randevu hatırlatma (KULLANILMIYOR)

3. **N8nWebhookController** ✅
   - `ilanTaslagi()` - AI ilan taslağı webhook
   - `mesajTaslagi()` - AI mesaj taslağı webhook
   - `sozlesmeTaslagi()` - AI sözleşme taslağı webhook
   - `analyzeMarket()` - Emsal arama webhook
   - `createDraftListing()` - Telegram'dan taslak ilan
   - `triggerReverseMatch()` - Tersine eşleştirme tetikleme

### ❌ Eksik Entegrasyonlar

1. **Event/Listener Sistemi Eksik:**
   - Sadece `IlanCreated` event'i var
   - Diğer modüller için event yok
   - Observer'lar eksik

2. **Job Sistemi Eksik:**
   - Sadece `NotifyN8nAboutNewIlan` Job'u var
   - Diğer modüller için Job yok

3. **N8nService Kullanılmıyor:**
   - Service'de metodlar var ama hiçbir yerde kullanılmıyor
   - Observer'larda, Controller'larda kullanılmıyor

---

## 🚀 ÖNERİLEN n8n ENTEGRASYON STRATEJİSİ

### 📋 FAZE 1: Event/Listener Sistemi Oluşturma (Öncelik: YÜKSEK)

#### 1.1. Event'leri Oluştur

```php
// app/Events/IlanUpdated.php
class IlanUpdated {
    public Ilan $ilan;
    public array $changes;
}

// app/Events/IlanPriceChanged.php
class IlanPriceChanged {
    public Ilan $ilan;
    public float $oldPrice;
    public float $newPrice;
}

// app/Events/IlanStatusChanged.php
class IlanStatusChanged {
    public Ilan $ilan;
    public string $oldStatus;
    public string $newStatus;
}

// app/Events/KisiCreated.php
class KisiCreated {
    public Kisi $kisi;
}

// app/Events/RandevuCreated.php
class RandevuCreated {
    public Randevu $randevu;
}

// app/Events/TalepCreated.php
class TalepCreated {
    public Talep $talep;
}

// app/Events/EventCreated.php (Rezervasyon)
class EventCreated {
    public Event $event;
}
```

#### 1.2. Observer'ları Güncelle

```php
// app/Observers/IlanObserver.php
public function updated(Ilan $ilan): void {
    if ($ilan->isDirty('fiyat')) {
        event(new IlanPriceChanged($ilan, $ilan->getOriginal('fiyat'), $ilan->fiyat));
    }
    if ($ilan->isDirty('status')) {
        event(new IlanStatusChanged($ilan, $ilan->getOriginal('status'), $ilan->status));
    }
    event(new IlanUpdated($ilan, $ilan->getChanges()));
}

// app/Observers/KisiObserver.php (YENİ)
public function created(Kisi $kisi): void {
    event(new KisiCreated($kisi));
}
```

#### 1.3. Listener'ları Oluştur

```php
// app/Listeners/NotifyN8nOnIlanPriceChanged.php
class NotifyN8nOnIlanPriceChanged implements ShouldQueue {
    public function handle(IlanPriceChanged $event): void {
        NotifyN8nAboutIlanPriceChange::dispatch($event->ilan->id, $event->oldPrice, $event->newPrice);
    }
}

// app/Listeners/NotifyN8nOnKisiCreated.php
class NotifyN8nOnKisiCreated implements ShouldQueue {
    public function handle(KisiCreated $event): void {
        NotifyN8nAboutNewKisi::dispatch($event->kisi->id);
    }
}
```

### 📋 FAZE 2: Job'ları Oluşturma (Öncelik: YÜKSEK)

#### 2.1. İlan İşlemleri Job'ları

```php
// app/Jobs/NotifyN8nAboutIlanPriceChange.php
class NotifyN8nAboutIlanPriceChange implements ShouldQueue {
    public function __construct(
        public int $ilanId,
        public float $oldPrice,
        public float $newPrice
    ) {}
    
    public function handle(): void {
        // n8n'e fiyat değişikliği bildirimi gönder
    }
}

// app/Jobs/NotifyN8nAboutIlanStatusChange.php
class NotifyN8nAboutIlanStatusChange implements ShouldQueue {
    public function __construct(
        public int $ilanId,
        public string $oldStatus,
        public string $newStatus
    ) {}
}
```

#### 2.2. CRM Job'ları

```php
// app/Jobs/NotifyN8nAboutNewKisi.php
class NotifyN8nAboutNewKisi implements ShouldQueue {
    public function __construct(public int $kisiId) {}
}

// app/Jobs/NotifyN8nAboutRandevuCreated.php
class NotifyN8nAboutRandevuCreated implements ShouldQueue {
    public function __construct(public int $randevuId) {}
}

// app/Jobs/NotifyN8nAboutRandevuReminder.php
class NotifyN8nAboutRandevuReminder implements ShouldQueue {
    public function __construct(public int $randevuId) {}
}
```

#### 2.3. Rezervasyon Job'ları

```php
// app/Jobs/NotifyN8nAboutReservationCreated.php
class NotifyN8nAboutReservationCreated implements ShouldQueue {
    public function __construct(public int $eventId) {}
}

// app/Jobs/NotifyN8nAboutCheckInReminder.php
class NotifyN8nAboutCheckInReminder implements ShouldQueue {
    public function __construct(public int $eventId) {}
}
```

### 📋 FAZE 3: n8n Workflow'ları (Öncelik: ORTA)

#### 3.1. İlan Workflow'ları

**Workflow 1: Yeni İlan → Multi-Channel Yayınlama**
```
1. Webhook Trigger (Laravel)
   ↓
2. Filter (Fiyat > 500.000 TL)
   ↓
3. Split: 3 kanal
   ├─→ Sahibinden API
   ├─→ Hürriyet Emlak API
   └─→ Telegram Channel
```

**Workflow 2: Fiyat Değişikliği → Müşteri Bildirimi**
```
1. Webhook Trigger (Laravel)
   ↓
2. Database Query (İlgili talepleri bul)
   ↓
3. IF (Fiyat düştü)
   ↓
4. Email Node (Müşterilere bildirim)
   ↓
5. WhatsApp Node (Acil müşterilere)
```

**Workflow 3: Status Değişikliği → Danışman Bildirimi**
```
1. Webhook Trigger (Laravel)
   ↓
2. IF (Status: Satıldı)
   ↓
3. Telegram Node (Danışmana bildirim)
   ↓
4. Google Sheets (Satış kaydı)
```

#### 3.2. CRM Workflow'ları

**Workflow 4: Yeni Kişi → CRM Sync**
```
1. Webhook Trigger (Laravel)
   ↓
2. Google Sheets (Müşteri listesi)
   ↓
3. Mailchimp (Email listesi)
   ↓
4. WhatsApp Business API (Hoş geldin mesajı)
```

**Workflow 5: Randevu Hatırlatma**
```
1. Cron Node (Her saat)
   ↓
2. HTTP Request (Laravel API: /api/randevular/yaklasan)
   ↓
3. IF (1 saat içinde randevu var)
   ↓
4. WhatsApp Node (Hatırlatma mesajı)
   ↓
5. Calendar Node (Google Calendar güncelle)
```

#### 3.3. Rezervasyon Workflow'ları

**Workflow 6: Rezervasyon Onayı → Müşteri Bildirimi**
```
1. Webhook Trigger (Laravel)
   ↓
2. Email Node (Onay emaili)
   ↓
3. WhatsApp Node (Rezervasyon detayları)
   ↓
4. Google Calendar (Rezervasyon ekle)
```

**Workflow 7: Check-in Hatırlatma**
```
1. Cron Node (Her gün 09:00)
   ↓
2. HTTP Request (Laravel API: /api/events/checkin-yaklasan)
   ↓
3. IF (1 gün içinde check-in var)
   ↓
4. Email Node (Check-in bilgileri)
   ↓
5. WhatsApp Node (Adres ve kod)
```

### 📋 FAZE 4: Scheduled Jobs (Öncelik: DÜŞÜK)

#### 4.1. Günlük Raporlar

```php
// app/Console/Commands/SendDailyReportToN8n.php
class SendDailyReportToN8n extends Command {
    public function handle() {
        $report = $this->generateDailyReport();
        app(N8nService::class)->sendDailyReport($report);
    }
}
```

#### 4.2. Haftalık Özetler

```php
// app/Console/Commands/SendWeeklySummaryToN8n.php
```

---

## 🔧 TEKNİK DETAYLAR

### Event Service Provider Güncellemesi

```php
// app/Providers/EventServiceProvider.php
protected $listen = [
    IlanCreated::class => [
        FindMatchingDemands::class,
        NotifyN8nOnIlanCreated::class, // YENİ
    ],
    IlanPriceChanged::class => [
        NotifyN8nOnIlanPriceChanged::class, // YENİ
    ],
    IlanStatusChanged::class => [
        NotifyN8nOnIlanStatusChanged::class, // YENİ
    ],
    KisiCreated::class => [
        NotifyN8nOnKisiCreated::class, // YENİ
    ],
    RandevuCreated::class => [
        NotifyN8nOnRandevuCreated::class, // YENİ
    ],
    EventCreated::class => [
        NotifyN8nOnReservationCreated::class, // YENİ
    ],
];
```

### Config Güncellemesi

```php
// config/services.php
'n8n' => [
    'webhook_url' => env('N8N_WEBHOOK_URL', 'http://localhost:5678'),
    'webhook_secret' => env('N8N_WEBHOOK_SECRET', ''),
    'timeout' => env('N8N_TIMEOUT', 30),
    
    // İlan webhook'ları
    'new_ilan_webhook_url' => env('N8N_NEW_ILAN_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/ilan-olustu'),
    'ilan_price_changed_webhook_url' => env('N8N_ILAN_PRICE_CHANGED_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/ilan-fiyat-degisti'),
    'ilan_status_changed_webhook_url' => env('N8N_ILAN_STATUS_CHANGED_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/ilan-durum-degisti'),
    
    // CRM webhook'ları
    'new_kisi_webhook_url' => env('N8N_NEW_KISI_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/kisi-olustu'),
    'randevu_created_webhook_url' => env('N8N_RANDEVU_CREATED_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/randevu-olustu'),
    'randevu_reminder_webhook_url' => env('N8N_RANDEVU_REMINDER_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/randevu-hatirlatma'),
    
    // Rezervasyon webhook'ları
    'reservation_created_webhook_url' => env('N8N_RESERVATION_CREATED_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/rezervasyon-olustu'),
    'checkin_reminder_webhook_url' => env('N8N_CHECKIN_REMINDER_WEBHOOK', 'https://n8n.yalihanemlak.com.tr/webhook/checkin-hatirlatma'),
];
```

---

## 📊 ÖNCELİK MATRİSİ

### 🔴 YÜKSEK ÖNCELİK (Hemen Yapılmalı)

1. **IlanPriceChanged Event + Job** ⭐⭐⭐
   - İş değeri: Yüksek (Müşteri bildirimi)
   - Teknik zorluk: Düşük
   - Etki: Büyük

2. **IlanStatusChanged Event + Job** ⭐⭐⭐
   - İş değeri: Yüksek (Danışman bildirimi)
   - Teknik zorluk: Düşük
   - Etki: Büyük

3. **KisiCreated Event + Job** ⭐⭐
   - İş değeri: Orta (CRM sync)
   - Teknik zorluk: Düşük
   - Etki: Orta

### 🟡 ORTA ÖNCELİK (1-2 Hafta İçinde)

4. **RandevuCreated Event + Job** ⭐⭐
   - İş değeri: Orta (Randevu hatırlatma)
   - Teknik zorluk: Orta
   - Etki: Orta

5. **EventCreated (Rezervasyon) Event + Job** ⭐⭐
   - İş değeri: Orta (Müşteri bildirimi)
   - Teknik zorluk: Orta
   - Etki: Orta

6. **TalepCreated Event + Job** ⭐
   - İş değeri: Düşük
   - Teknik zorluk: Düşük
   - Etki: Düşük

### 🟢 DÜŞÜK ÖNCELİK (Gelecek Sprint'te)

7. **Günlük/Haftalık Raporlar** ⭐
8. **Görev Yönetimi Entegrasyonu** ⭐
9. **Finans Modülü Entegrasyonu** ⭐

---

## 🎯 UYGULAMA PLANI

### Hafta 1: Event/Listener Sistemi
- [ ] IlanPriceChanged Event + Listener + Job
- [ ] IlanStatusChanged Event + Listener + Job
- [ ] IlanObserver güncellemesi
- [ ] Test ve doğrulama

### Hafta 2: CRM Entegrasyonu
- [ ] KisiCreated Event + Listener + Job
- [ ] KisiObserver oluşturma
- [ ] RandevuCreated Event + Listener + Job
- [ ] Test ve doğrulama

### Hafta 3: Rezervasyon Entegrasyonu
- [ ] EventCreated (Rezervasyon) Event + Listener + Job
- [ ] Check-in hatırlatma sistemi
- [ ] Test ve doğrulama

### Hafta 4: n8n Workflow'ları
- [ ] İlan workflow'ları oluşturma
- [ ] CRM workflow'ları oluşturma
- [ ] Rezervasyon workflow'ları oluşturma
- [ ] Test ve doğrulama

---

## 📝 SONUÇ

**Mevcut Durum:**
- ✅ 1 Event (IlanCreated)
- ✅ 1 Job (NotifyN8nAboutNewIlan)
- ✅ 1 Service (N8nService - kullanılmıyor)
- ✅ 1 Controller (N8nWebhookController)

**Hedef Durum:**
- ✅ 10+ Event
- ✅ 10+ Job
- ✅ 10+ n8n Workflow
- ✅ Tüm modüller entegre

**Tahmini Süre:** 4 hafta  
**Kaynak Gereksinimi:** 1 developer  
**ROI:** Yüksek (Otomasyon, zaman tasarrufu, müşteri memnuniyeti)

---

**Son Güncelleme:** 15 Ocak 2025  
**Hazırlayan:** Yalıhan Bekçi AI System  
**Status:** ✅ Analiz Tamamlandı - Uygulamaya Hazır












