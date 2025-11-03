# 🗺️ Google Maps Location System

Bu dokümantasyon, Yalıhan Emlak projesinde kullanılan **Google Maps entegrasyonlu konum sistemi** için detaylı kılavuzdur.

## 📋 Genel Bakış

Bu sistem, [Flynax Location Finder](https://www.flynax.com/plugins/location-finder.html) plugin'inden ilham alınarak geliştirilmiştir ve profesyonel emlak sitelerinde kullanılan standart konum seçimi özelliklerini sunar.

## 🎯 Özellikler

### ✅ Temel Özellikler

-   **🗺️ Google Maps Entegrasyonu** - Gerçek harita ile konum seçimi
-   **📍 Marker Drag & Drop** - Marker'ı sürükleyerek hassas konum
-   **🔍 Google Places API** - Adres arama ve otomatik doldurma
-   **🌍 IP Based Location** - Kullanıcının konumunu otomatik tespit
-   **🔄 Real-time Sync** - Harita ↔ Form alanları senkronizasyonu
-   **📍 Coordinates Storage** - Lat/Lng koordinatları kaydetme
-   **📮 Address Components** - Ülke, şehir, ilçe otomatik ayrıştırma

### ✅ Gelişmiş Özellikler

-   **🎯 Autocomplete Search** - Google Places autocomplete
-   **📍 Double Click to Place** - Haritaya çift tıklayarak konum
-   **🔍 Address Search** - Tam adres arama
-   **🌍 Current Location** - Mevcut konum butonu
-   **📊 Real-time Preview** - Anlık konum bilgisi gösterimi

## 🏗️ Sistem Mimarisi

### Dosya Yapısı

```
resources/views/admin/ilanlar/partials/
├── google-maps-location.blade.php          # Ana konum sistemi
├── modern-location-selector.blade.php      # Alternatif arama tabanlı sistem
├── json-location-selector.blade.php        # JSON tabanlı basit sistem
├── flynax-style-location.blade.php         # Flynax tarzı cascade sistem
└── emlakloc-integration.blade.php          # ❌ DEPRECATED (eski sistem)

routes/
└── api-location.php                        # Location API endpoints

config/
└── services.php                            # Google Maps API key konfigürasyonu
```

### API Endpoints

```
GET /api/location/countries                 # Ülkeler listesi
GET /api/location/cities/{countryId}        # Ülkeye göre şehirler
GET /api/location/districts/{cityId}        # Şehre göre ilçeler
GET /api/location/neighborhoods/{districtId} # İlçeye göre mahalleler
GET /api/location/search?q=query           # Konum arama
GET /api/location/hierarchy/{type}/{id}    # Hiyerarşi bilgisi
```

## 🔧 Kurulum

### 1. Google Maps API Key

```bash
# .env dosyasına ekleyin
GOOGLE_MAPS_API_KEY=your-google-maps-api-key-here
```

### 2. Gerekli Google APIs

-   **Maps JavaScript API** - Harita görüntüleme
-   **Places API** - Adres arama ve autocomplete
-   **Geocoding API** - Koordinat ↔ Adres dönüşümü

### 3. Form Wizard Entegrasyonu

```php
// modern-form-wizard.blade.php içinde
@include('admin.ilanlar.partials.google-maps-location')
```

## 💻 Kullanım

### Temel Kullanım

1. **Adres Arama:** Kullanıcı adres yazar
2. **Marker Drag:** Marker'ı sürükleyerek konum ayarlar
3. **Auto Fill:** Form alanları otomatik doldurulur
4. **Coordinates:** Lat/Lng koordinatları kaydedilir

### JavaScript API

```javascript
// Konum güncelleme
updateLocationFromMarker();

// Adres arama
searchLocation();

// Mevcut konum alma
getCurrentLocation();

// Konum bilgisi güncelleme
updateLocationInfo(place);
```

## 📊 Veri Yapısı

### Hidden Input Fields

```html
<input type="hidden" name="latitude" id="latitude" value="" />
<input type="hidden" name="longitude" id="longitude" value="" />
<input type="hidden" name="formatted_address" id="formatted-address" value="" />
<input type="hidden" name="place_id" id="place-id" value="" />
```

### Address Components

```javascript
{
    country: "Türkiye",
    state: "İstanbul",
    city: "İstanbul",
    district: "Kadıköy",
    route: "Bağdat Caddesi",
    streetNumber: "123",
    postalCode: "34710"
}
```

## 🎨 UI/UX Özellikleri

### Neo Design System Uyumluluğu

-   **neo-card** - Ana konteyner
-   **neo-input** - Adres arama kutusu
-   **neo-btn** - Aksiyon butonları
-   **neo-alert** - Konum bilgi gösterimi

### Responsive Design

-   **Mobile First** - Mobil öncelikli tasarım
-   **Touch Friendly** - Dokunmatik uyumlu
-   **Keyboard Navigation** - Klavye navigasyonu

## 🔒 Güvenlik

### API Key Güvenliği

-   **Environment Variables** - API key .env dosyasında
-   **Domain Restrictions** - Google Console'da domain kısıtlaması
-   **API Quotas** - Günlük kullanım limitleri

### Data Validation

-   **Server-side Validation** - Backend doğrulama
-   **Input Sanitization** - Girdi temizleme
-   **XSS Protection** - Cross-site scripting koruması

## 📈 Performance

### Optimizasyonlar

-   **Lazy Loading** - Harita gerektiğinde yüklenir
-   **Debounced Search** - Arama optimizasyonu
-   **Cached Results** - Sonuç önbellekleme
-   **Minified Assets** - Sıkıştırılmış dosyalar

### Monitoring

-   **API Usage Tracking** - Google API kullanım takibi
-   **Error Logging** - Hata kayıtları
-   **Performance Metrics** - Performans metrikleri

## 🐛 Troubleshooting

### Yaygın Sorunlar

#### 1. Harita Yüklenmiyor

```bash
# Çözüm: API key kontrolü
echo $GOOGLE_MAPS_API_KEY
```

#### 2. Adres Arama Çalışmıyor

```bash
# Çözüm: Places API aktif mi?
# Google Cloud Console → APIs & Services → Enabled APIs
```

#### 3. Marker Drag Çalışmıyor

```javascript
// Çözüm: Event listener kontrolü
marker.addListener("dragend", function () {
    updateLocationFromMarker();
});
```

## 🔄 Migration Guide

### Eski Sistemden Yeni Sisteme Geçiş

#### 1. Eski Dosyaları Kaldır

```bash
# ❌ Artık kullanılmayan
rm resources/views/admin/ilanlar/partials/emlakloc-integration.blade.php
```

#### 2. Yeni Sistemi Aktifleştir

```php
// modern-form-wizard.blade.php
@include('admin.ilanlar.partials.google-maps-location')
```

#### 3. Database Migration

```sql
-- Koordinat alanları ekle
ALTER TABLE ilanlar ADD COLUMN latitude DECIMAL(10, 8) NULL;
ALTER TABLE ilanlar ADD COLUMN longitude DECIMAL(11, 8) NULL;
ALTER TABLE ilanlar ADD COLUMN formatted_address TEXT NULL;
ALTER TABLE ilanlar ADD COLUMN place_id VARCHAR(255) NULL;
```

## 📚 Referanslar

-   [Google Maps JavaScript API](https://developers.google.com/maps/documentation/javascript)
-   [Google Places API](https://developers.google.com/maps/documentation/places/web-service)
-   [Flynax Location Finder](https://www.flynax.com/plugins/location-finder.html)
-   [Context7 Rules](docs/context7-rules.md)
-   [Neo Design System](docs/neo-design-system.md)

## 📝 Changelog

### v1.0.0 (2024-09-30)

-   ✅ Google Maps entegrasyonu eklendi
-   ✅ Marker drag & drop özelliği
-   ✅ Google Places API entegrasyonu
-   ✅ Real-time form sync
-   ✅ Neo Design System uyumluluğu
-   ✅ Responsive design
-   ✅ IP-based location detection

---

**Not:** Bu sistem tamamen [Flynax Location Finder](https://www.flynax.com/plugins/location-finder.html) özelliklerini içerir ve profesyonel emlak sitelerinde kullanılan standarttır.
