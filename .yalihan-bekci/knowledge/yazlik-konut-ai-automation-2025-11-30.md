# 🏠 Yazlık & Konut AI Otomasyon Sistemi

**Tarih:** 2025-11-30  
**Context7:** C7-YAZLIK-KONUT-AI-AUTOMATION-2025-11-30  
**Versiyon:** 1.0.0

---

## 📋 ÖZET

Yazlık kiralama ve Konut kategorileri için AI destekli otomatik fiyatlandırma ve akıllı validasyon sistemleri.

**Amaç:** Danışmanların manuel hesaplama yapmadan, sistemin otomatik olarak fiyatlandırma ve validasyon yapmasını sağlamak.

---

## 🏖️ YAZLIK OTOMATIK FİYATLANDIRMA

### Configuration

**Dosya:** `config/yali_options.php`

```php
'pricing_rules' => [
    'discounts' => [
        'weekly' => 0.05,  // %5 İndirim
        'monthly' => 0.15, // %15 İndirim
    ],
    'seasonal_multipliers' => [
        'yaz' => 1.00,      // %100 (Baz Fiyat)
        'ara_sezon' => 0.70, // %70
        'kis' => 0.50,      // %50
    ],
],
'sezon_tipleri' => [
    'yaz' => ['label' => 'Yaz Sezonu (Haziran-Ağustos)', 'color' => 'yellow', 'icon' => '☀️'],
    'ara_sezon' => ['label' => 'Ara Sezon (Eylül-Ekim / Nisan-Mayıs)', 'color' => 'orange', 'icon' => '🍂'],
    'kis' => ['label' => 'Kış Sezonu (Kasım-Mart)', 'color' => 'blue', 'icon' => '❄️'],
],
```

### API Endpoint

**Route:** `POST /api/ai/calculate-seasonal-price`

**Controller:** `App\Http\Controllers\Api\IlanAIController@calculateSeasonalPrice`

**Input:**
- `gunluk_fiyat` (required|numeric|min:0.01)

**Output:**
```json
{
    "success": true,
    "data": {
        "gunluk_fiyat": 10000,
        "haftalik_fiyat": 66500,
        "aylik_fiyat": 255000,
        "sezonluk_fiyatlar": {
            "yaz": {
                "gunluk": 10000,
                "haftalik": 66500,
                "aylik": 255000
            },
            "ara_sezon": {
                "gunluk": 7000,
                "haftalik": 46550,
                "aylik": 178500
            },
            "kis": {
                "gunluk": 5000,
                "haftalik": 33250,
                "aylik": 127500
            }
        }
    }
}
```

### Hesaplama Formülleri

- **Haftalık:** `günlük × 7 × (1 - 0.05) = günlük × 6.65`
- **Aylık:** `günlük × 30 × (1 - 0.15) = günlük × 25.5`
- **Kış Günlük:** `günlük × 0.50`
- **Ara Sezon Günlük:** `günlük × 0.70`

### UI Entegrasyonu

**Dosya:** `resources/views/admin/ilanlar/components/category-fields/kiralik-fields.blade.php`

**Özellikler:**
- Sadece Yazlık kategorisi seçildiğinde görünür
- Günlük fiyat input'una "⚡ Otomatik Hesapla" butonu eklenir
- Loading state ve flash effect
- Otomatik form doldurma (haftalik_fiyat, aylik_fiyat)

---

## 🏠 KONUT AKILLI VALİDASYON

### Configuration

**Dosya:** `config/yali_options.php`

```php
'oda_sayisi_options' => [
    ['value' => '1+0', 'label' => '1+0 (Stüdyo)', 'color' => 'text-blue-600 bg-blue-50 border-blue-200', 'icon' => '🏠'],
    ['value' => '1+1', 'label' => '1+1', 'color' => 'text-blue-700 bg-blue-100 border-blue-300', 'icon' => '👥'],
    ['value' => '2+1', 'label' => '2+1', 'color' => 'text-green-600 bg-green-50 border-green-200', 'icon' => '👨‍👩‍👧'],
    ['value' => '3+1', 'label' => '3+1', 'color' => 'text-orange-600 bg-orange-50 border-orange-200', 'icon' => '👨‍👩‍👧‍👦'],
    ['value' => '4+1', 'label' => '4+1', 'color' => 'text-purple-600 bg-purple-50 border-purple-200', 'icon' => '🏰'],
    ['value' => '5+1', 'label' => '5+1', 'color' => 'text-purple-800 bg-purple-100 border-purple-300', 'icon' => '🏰'],
],
```

### Validation Rules

**Dosya:** `app/Services/CategoryFieldValidator.php`

**getKonutRules():**
- `oda_sayisi`: required|string|in:[config values]
- `brut_metrekare`: required|numeric|min:10|max:10000
- `net_metrekare`: required|numeric|min:10|max:10000
- `bina_yasi`: required|numeric
- `isinma_tipi`: required|string

**validateKonut() - Custom Validation:**
- Net m² > Brüt m² kontrolü
- Hata mesajı: "Net metrekare, Brüt metrekareden büyük olamaz!"

### API Endpoint

**Route:** `POST /api/ai/calculate-konut-metrics`

**Controller:** `App\Http\Controllers\Api\IlanAIController@calculateKonutMetrics`

**Input:**
- `satis_fiyati` (required|numeric|min:0.01)
- `brut_m2` (required|numeric|min:10)

**Output:**
```json
{
    "success": true,
    "data": {
        "m2_birim_fiyat": 35000,
        "formatted": "35.000 TL/m²",
        "satis_fiyati": 3500000,
        "brut_m2": 100,
        "formula": "3500000 / 100 = 35000",
        "piyasa_analizi": {
            "durum": "ortalamada",
            "piyasa_ortalamasi": 35000,
            "fark_yuzdesi": 0
        }
    }
}
```

### UI Entegrasyonu

**Dosya 1:** `resources/views/admin/ilanlar/components/field-dependencies-dynamic.blade.php`

**Özellikler:**
- `createSelect()` metoduna oda_sayisi renklendirmesi eklendi
- Config'den renkli seçenekler yükleniyor
- Seçildiğinde select input'u o renge bürünüyor

**Dosya 2:** `resources/views/admin/ilanlar/components/category-fields/konut-fields.blade.php`

**Özellikler:**
- Konut Akıllı Validasyon kartı
- Canlı kontrol (Alpine.js):
  - `net_m2` veya `brut_m2` değiştiğinde anlık kontrol
  - Hata varsa input çerçevesi kırmızı
  - "Net > Brüt Olamaz" uyarısı
- Birim fiyat badge:
  - Fiyat ve brüt m² girildiğinde otomatik hesaplama
  - "Birim: 35.000 TL/m²" formatında gösterim
  - JS ile anlık hesaplama

---

## 🔄 İŞ AKIŞLARI

### Yazlık Fiyatlandırma Akışı

1. Kullanıcı Yazlık kategorisini seçer
2. Yazlık Otomatik Fiyatlandırma kartı görünür
3. Günlük Fiyat: 10.000 TL girilir
4. "⚡ Otomatik Hesapla" butonuna tıklanır
5. API'ye POST isteği gönderilir
6. Sistem hesaplar:
   - Haftalık: 66.500 TL (%5 indirimli)
   - Aylık: 255.000 TL (%15 indirimli)
   - Sezonluk fiyatlar (Yaz, Ara Sezon, Kış)
7. Form alanları otomatik doldurulur
8. Flash effect: Input'lar yeşil yanıp söner

### Konut Validasyon Akışı

1. Kullanıcı Konut kategorisini seçer
2. Oda Sayısı: 3+1 seçildiğinde select turuncu ton alır
3. Brüt m²: 100 girilir
4. Net m²: 110 girilmeye çalışıldığında:
   - Input çerçevesi kırmızı olur
   - "Net metrekare, Brüt metrekareden büyük olamaz!" uyarısı gösterilir
   - Form kaydedilemez
5. Satış Fiyatı: 3.500.000 TL girilir
6. Birim fiyat badge: "Birim: 35.000 TL/m²" otomatik gösterilir

---

## 📊 KULLANIM ÖRNEKLERİ

### Yazlık Fiyatlandırma

```javascript
fetch('/api/ai/calculate-seasonal-price', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        gunluk_fiyat: 10000
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Haftalık:', data.data.haftalik_fiyat); // 66500
        console.log('Aylık:', data.data.aylik_fiyat); // 255000
        console.log('Kış Günlük:', data.data.sezonluk_fiyatlar.kis.gunluk); // 5000
    }
});
```

### Konut Metrikleri

```javascript
fetch('/api/ai/calculate-konut-metrics', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        satis_fiyati: 3500000,
        brut_m2: 100
    })
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('m² Birim Fiyat:', data.data.m2_birim_fiyat); // 35000
        console.log('Formatted:', data.data.formatted); // "35.000 TL/m²"
        console.log('Piyasa Durumu:', data.data.piyasa_analizi.durum); // "ortalamada"
    }
});
```

---

## ✅ CONTEXT7 UYUMLULUK

- ✅ `declare(strict_types=1);` standardı
- ✅ ResponseService kullanımı
- ✅ Config-based rules (yali_options.php)
- ✅ Tailwind CSS (UI)
- ✅ Alpine.js (interaktivite)
- ✅ İngilizce field isimleri
- ✅ Comprehensive error handling
- ✅ Logging sistemi

---

## 🔗 İLGİLİ DOSYALAR

### Yazlık Fiyatlandırma

1. **Config:** `config/yali_options.php` (pricing_rules, sezon_tipleri)
2. **Controller:** `app/Http/Controllers/Api/IlanAIController.php` (calculateSeasonalPrice)
3. **Route:** `routes/api/v1/ai.php` (calculate-seasonal-price)
4. **UI:** `resources/views/admin/ilanlar/components/category-fields/kiralik-fields.blade.php`

### Konut Validasyon

1. **Config:** `config/yali_options.php` (oda_sayisi_options)
2. **Validator:** `app/Services/CategoryFieldValidator.php` (getKonutRules, validateKonut)
3. **Controller:** `app/Http/Controllers/Api/IlanAIController.php` (calculateKonutMetrics)
4. **Route:** `routes/api/v1/ai.php` (calculate-konut-metrics)
5. **UI:** `resources/views/admin/ilanlar/components/category-fields/konut-fields.blade.php`
6. **Dynamic Fields:** `resources/views/admin/ilanlar/components/field-dependencies-dynamic.blade.php` (createSelect - oda_sayisi renklendirme)

---

## 📚 REFERANSLAR

- Context7 Standards: `.context7/authority.json`
- Tailwind CSS: https://tailwindcss.com
- Alpine.js: https://alpinejs.dev
- Laravel Validation: https://laravel.com/docs/validation

---

**Son Güncelleme:** 2025-11-30  
**Durum:** ✅ Production Ready



