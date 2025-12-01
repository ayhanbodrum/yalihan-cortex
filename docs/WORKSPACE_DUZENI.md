# 📁 Workspace Düzeni - Yalıhan Emlak AI

**Tarih:** 29 Kasım 2025  
**Durum:** ✅ Aktif Workspace'ler  
**URL:** https://ai.yalihanemlak.com.tr/

---

## 🎯 Workspace Yapısı

Proje, **5 ana workspace** altında organize edilmiştir:

### ✅ 1. Sistem İşletme

**Amaç:** Sistem yönetimi, operasyonel işlemler, genel sistem bakımı

**Dosyalar:**
- `docs/active/` - Konsolide dokümantasyon
- `docs/technical/system/` - Sistem mimarisi
- `docs/technical/README.md` - Teknik dokümantasyon
- `docs/development/` - Geliştirme rehberleri
- `docs/maintenance/` - Bakım ve güncelleme dokümanları
- `app/Console/Commands/` - Artisan komutları
- `scripts/` - Sistem script'leri

**İlgili Modüller:**
- Admin Panel
- User Management
- System Configuration
- Logging & Monitoring

---

### ✅ 2. Hukuk / İmar

**Amaç:** Hukuki işlemler, imar durumu kontrolleri, sözleşme yönetimi

**Dosyalar:**
- `docs/integrations/tkgm/` - TKGM entegrasyonu
- `app/Services/TKGM/` - TKGM servisleri
- `app/Models/ArsaCalculation.php` - Arsa hesaplamaları
- `database/migrations/*_arsa*.php` - Arsa migration'ları
- `docs/ai/YALIHAN_CORTEX_VISION_2.0.md` - Hukuki kontrol sistemi

**İlgili Özellikler:**
- İmar Durumu Kontrolü
- Ada/Parsel Sorgulama
- KAKS/TAKS Hesaplamaları
- Hukuki Doküman Yönetimi
- Contract Guard (Otomatik Hukuki Kontrol)

**Not:** AnythingLLM'de `yalihan-hukuk` workspace'i kullanılır.

---

### ✅ 3. Teknik Operasyon

**Amaç:** Teknik geliştirme, API entegrasyonları, performans optimizasyonu

**Dosyalar:**
- `docs/technical/` - Teknik dokümantasyon
- `docs/technical/api/` - API dokümantasyonu
- `docs/technical/performance/` - Performans optimizasyonu
- `docs/integrations/` - Dış entegrasyonlar
- `app/Services/` - Servis katmanı
- `app/Http/Controllers/Api/` - API controller'ları
- `routes/api.php` - API route'ları

**İlgili Sistemler:**
- REST API
- Hybrid Search System
- Context7 Integration
- MCP Servers
- Performance Monitoring

---

### ✅ 4. Pazar İstihbaratı

**Amaç:** Dış kaynaklardan piyasa verilerini toplama, analiz ve karşılaştırma

**Dosyalar:**
- `docs/market-intelligence/` - Pazar istihbaratı dokümantasyonu
  - `PAZAR_ISTIHBARATI_SISTEMI.md` - Sistem açıklaması
  - `VERI_CEKME_STRATEJISI.md` - Veri çekme stratejisi
  - `MULTI_DATABASE_SETUP.md` - Çoklu veritabanı kurulumu
  - `MARKET_INTELLIGENCE_SYSTEM_EXPLAINED.md` - Detaylı açıklama
- `app/Models/MarketListing.php` - Market listing modeli
- `database/migrations/*_market_listings*.php` - Market listings migration
- `app/Http/Controllers/Admin/MarketIntelligenceController.php` - Controller
- `config/database.php` - Market intelligence database config

**İlgili Özellikler:**
- Sahibinden, Hepsiemlak, Emlakjet veri çekme
- Fiyat karşılaştırması
- Piyasa trend analizi
- Bölge bazlı istatistikler
- AI destekli fiyat önerileri

**Database:** Ayrı veritabanı (`market_intelligence` connection)

---

### ✅ 5. Alarm (Critical Logs)

**Amaç:** Kritik hatalar, sistem uyarıları, log yönetimi

**Dosyalar:**
- `storage/logs/` - Laravel log dosyaları
- `app/Models/Context7ComplianceLog.php` - Context7 compliance logları
- `app/Services/Logging/LogService.php` - Log servisi
- `app/Models/AiLog.php` - AI işlem logları
- `docs/features/ANALYTICS_SISTEMI_DOKUMANTASYONU.md` - Analytics sistemi

**İlgili Sistemler:**
- Error Tracking
- Context7 Compliance Monitoring
- AI Operation Logging
- Performance Monitoring
- Critical Alert System

**Log Seviyeleri:**
- `critical` - Acil müdahale gerektiren
- `error` - Hata durumları
- `warning` - Uyarılar
- `info` - Bilgilendirme

---

## 📊 Workspace İlişkileri

```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│   🏛️ YALIHAN EMLAK AI WORKSPACE YAPISI                 │
│                                                         │
│   ┌─────────────────┐  ┌─────────────────┐            │
│   │ Sistem İşletme │  │ Teknik Operasyon │            │
│   │                 │  │                 │            │
│   │ • Admin Panel  │  │ • API           │            │
│   │ • User Mgmt    │  │ • Services      │            │
│   │ • Config       │  │ • Integrations  │            │
│   └────────┬────────┘  └────────┬────────┘            │
│            │                     │                      │
│            └──────────┬──────────┘                      │
│                       │                                 │
│            ┌──────────▼──────────┐                      │
│            │  Pazar İstihbaratı  │                      │
│            │                     │                      │
│            │ • Market Data       │                      │
│            │ • Price Analysis    │                      │
│            │ • Trend Reports     │                      │
│            └──────────┬──────────┘                      │
│                       │                                 │
│   ┌───────────────────┴───────────────────┐            │
│   │                                       │            │
│   │  ┌──────────────┐  ┌──────────────┐  │            │
│   │  │ Hukuk/İmar   │  │ Alarm        │  │            │
│   │  │              │  │ (Critical)   │  │            │
│   │  │ • TKGM       │  │              │  │            │
│   │  │ • İmar       │  │ • Logs       │  │            │
│   │  │ • Contracts  │  │ • Alerts     │  │            │
│   │  └──────────────┘  └──────────────┘  │            │
│   │                                       │            │
│   └───────────────────────────────────────┘            │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## 🔗 Dosya Erişim Yolları

### Sistem İşletme
```bash
# Konsolide dokümantasyon
docs/active/

# Sistem mimarisi
docs/technical/system/

# Geliştirme rehberleri
docs/development/
```

### Hukuk / İmar
```bash
# TKGM entegrasyonu
docs/integrations/tkgm/

# Arsa hesaplamaları
app/Services/PropertyValuation/

# İmar durumu
app/Models/ArsaCalculation.php
```

### Teknik Operasyon
```bash
# API dokümantasyonu
docs/technical/api/

# Servis katmanı
app/Services/

# API controller'ları
app/Http/Controllers/Api/
```

### Pazar İstihbaratı
```bash
# Dokümantasyon
docs/market-intelligence/

# Model
app/Models/MarketListing.php

# Controller
app/Http/Controllers/Admin/MarketIntelligenceController.php
```

### Alarm (Critical Logs)
```bash
# Log dosyaları
storage/logs/

# Log servisi
app/Services/Logging/LogService.php

# Compliance logları
app/Models/Context7ComplianceLog.php
```

---

## 📝 Workspace Kullanım Notları

1. **Sistem İşletme:** Genel sistem yönetimi ve operasyonel işlemler
2. **Hukuk / İmar:** Hukuki işlemler ve imar durumu kontrolleri
3. **Teknik Operasyon:** API, servisler ve teknik entegrasyonlar
4. **Pazar İstihbaratı:** Dış kaynaklardan piyasa verisi toplama
5. **Alarm (Critical Logs):** Kritik hatalar ve sistem uyarıları

---

**Son Güncelleme:** 29 Kasım 2025  
**Versiyon:** 1.0.0  
**Durum:** ✅ Aktif






