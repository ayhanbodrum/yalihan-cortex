# 🚀 Yalıhan Emlak - Yeni Mimari v2.1 (Gemini İçin Güncellenmiş Rehber)

**Tarih:** 2025-11-30  
**Versiyon:** 2.1.0  
**Hedef:** Google Gemini'ye yeni AI entegrasyonu pattern'lerini ve validasyon sistemini öğretmek

---

## 📋 DEĞİŞİKLİK ÖZETİ

### ✅ Yeni Eklenen Özellikler (v2.1.0)

1. **CategoryFieldValidator Sistemi** - JSON-based validation rules
2. **AI-Powered Endpoints** - TKGM sorgulama ve m² fiyat hesaplama
3. **Frontend AI Integration** - TKGM butonu ve renkli seçenekler
4. **Config-Based Options** - `yali_options.php` ile merkezi yönetim
5. **Yazlık Otomatik Fiyatlandırma** - Sezonluk fiyat hesaplama (2025-11-30)
6. **Konut Akıllı Validasyon** - Net/Brüt m² tutarlılık kontrolü (2025-11-30)

---

## 🏖️ YAZLIK OTOMATIK FİYATLANDIRMA (v2.1.0)

### Mimari

**Config:** `config/yali_options.php`

```php
'pricing_rules' => [
    'discounts' => [
        'weekly' => 0.05,  // %5 İndirim
        'monthly' => 0.15, // %15 İndirim
    ],
    'seasonal_multipliers' => [
        'yaz' => 1.00,
        'ara_sezon' => 0.70,
        'kis' => 0.50,
    ],
],
```

### API Endpoint

**Route:** `POST /api/ai/calculate-seasonal-price`

**Controller:** `App\Http\Controllers\Api\IlanAIController@calculateSeasonalPrice`

**Kullanım:**
```php
$result = $controller->calculateSeasonalPrice($request);
// Returns: haftalik_fiyat, aylik_fiyat, sezonluk_fiyatlar
```

### UI Entegrasyonu

**Dosya:** `resources/views/admin/ilanlar/components/category-fields/kiralik-fields.blade.php`

**Özellikler:**
- Sadece Yazlık kategorisi seçildiğinde görünür
- Günlük fiyat input'una "⚡ Otomatik Hesapla" butonu
- Loading state ve flash effect
- Otomatik form doldurma

---

## 🏠 KONUT AKILLI VALİDASYON (v2.1.0)

### Mimari

**Config:** `config/yali_options.php`

```php
'oda_sayisi_options' => [
    ['value' => '1+0', 'label' => '1+0 (Stüdyo)', 'color' => 'text-blue-600 bg-blue-50', 'icon' => '🏠'],
    ['value' => '3+1', 'label' => '3+1', 'color' => 'text-orange-600 bg-orange-50', 'icon' => '👨‍👩‍👧‍👦'],
    // ...
],
```

### Validation Rules

**Dosya:** `app/Services/CategoryFieldValidator.php`

**getKonutRules():**
- `oda_sayisi`: required
- `brut_metrekare`: required|numeric|min:10
- `net_metrekare`: required|numeric|min:10
- Custom: Net m² > Brüt m² kontrolü

**validateKonut():**
- Custom validation metodu
- Net > Brüt kontrolü
- Hata mesajı: "Net metrekare, Brüt metrekareden büyük olamaz!"

### API Endpoint

**Route:** `POST /api/ai/calculate-konut-metrics`

**Controller:** `App\Http\Controllers\Api\IlanAIController@calculateKonutMetrics`

**Kullanım:**
```php
$result = $controller->calculateKonutMetrics($request);
// Returns: m2_birim_fiyat, formatted, piyasa_analizi
```

### UI Entegrasyonu

**Dosya:** `resources/views/admin/ilanlar/components/category-fields/konut-fields.blade.php`

**Özellikler:**
- Renkli oda sayısı seçimi (select input renklenir)
- Canlı Net/Brüt m² validasyonu
- Birim fiyat badge (otomatik hesaplama)
- Kırmızı çerçeve ve uyarı mesajı

---

## 🎯 1. CATEGORYFIELDVALIDATOR SİSTEMİ

### Mimari

```php
app/Services/CategoryFieldValidator.php
```

**Özellikler:**
- ✅ JSON-based validation rules
- ✅ Config entegrasyonu (`yali_options.php`)
- ✅ Category-specific rules (Arsa, Konut, İşyeri)
- ✅ Publication type-specific rules (Satılık, Kiralık)
- ✅ Custom validation methods (validateKonut)

### Kullanım Örneği

```php
$validator = new CategoryFieldValidator();

// Konut × Satılık validasyon kuralları
$rules = $validator->getRules('konut', 'satilik');
// Returns:
// [
//   'features.oda-sayisi' => 'required|string|in:1+0,1+1,2+1,...',
//   'features.brut-metrekare' => 'required|numeric|min:10|max:10000',
//   'features.net-metrekare' => 'required|numeric|min:10|max:10000',
//   ...
// ]

// Custom validation (Net > Brüt kontrolü)
$validatorInstance = $validator->validateKonut($requestData);
if ($validatorInstance->fails()) {
    // Hata mesajları
}
```

---

## 🎯 2. AI-POWERED ENDPOINTS

### TKGM Sorgulama

**Route:** `POST /api/ai/fetch-tkgm`

**Input:** `il_id`, `ilce_id`, `mahalle_id`, `ada_no`, `parsel_no`

**Output:** `alan_m2`, `lat`, `lng`, `imar_statusu`, `kaks`, `taks`, `gabari`

### m² Fiyat Hesaplama

**Route:** `POST /api/ai/calculate-m2-price`

**Input:** `satis_fiyati`, `alan_m2`

**Output:** `m2_fiyati`, `formula`

### Yazlık Sezonluk Fiyatlandırma

**Route:** `POST /api/ai/calculate-seasonal-price`

**Input:** `gunluk_fiyat`

**Output:** `haftalik_fiyat`, `aylik_fiyat`, `sezonluk_fiyatlar`

### Konut Metrikleri

**Route:** `POST /api/ai/calculate-konut-metrics`

**Input:** `satis_fiyati`, `brut_m2`

**Output:** `m2_birim_fiyat`, `formatted`, `piyasa_analizi`

---

## 🎯 3. FRONTEND AI INTEGRATION

### Renkli Select Seçenekleri

**Pattern:** Config'den renk bilgisi çekme ve select input'a uygulama

**Örnek:**
```javascript
// Oda Sayısı için renkli seçenekler
const odaSayisiConfig = @json(config('yali_options.oda_sayisi_options', []));
odaSayisiConfig.forEach((config) => {
    option.className = config.color; // Tailwind classes
    option.setAttribute('data-color-classes', config.color);
});

// Select değiştiğinde renk uygula
select.addEventListener('change', function() {
    const colorClasses = selectedOption.getAttribute('data-color-classes');
    select.className = 'base-classes ' + colorClasses;
});
```

### Canlı Validasyon

**Pattern:** Alpine.js ile anlık kontrol

**Örnek:**
```javascript
x-data="{
    netM2: null,
    brutM2: null,
    validationError: null,
    validateM2() {
        if (this.netM2 && this.brutM2 && this.netM2 > this.brutM2) {
            this.validationError = 'Net metrekare, Brüt metrekareden büyük olamaz!';
            return false;
        }
        this.validationError = null;
        return true;
    }
}"
```

### Flash Effect

**Pattern:** Değer değiştiğinde input'un arka planını yeşil yapıp geri döndürme

**Örnek:**
```javascript
input.classList.add('bg-green-100', 'dark:bg-green-900/30');
setTimeout(() => {
    input.classList.remove('bg-green-100', 'dark:bg-green-900/30');
}, 1000);
```

---

## 📚 GEMINI İÇİN ÖĞRENME REHBERİ

### Yeni Pattern'leri Anlama

Gemini'ye öğretilmesi gerekenler:

1. **Config-Based Validation:**
   - Config dosyasından seçenekleri çekme
   - Dinamik validasyon kuralları oluşturma
   - Custom validation methods

2. **AI-Powered Endpoints:**
   - Standardize edilmiş endpoint yapısı
   - ResponseService kullanımı
   - Error handling pattern'i
   - Sezonluk fiyatlandırma hesaplamaları

3. **Frontend AI Integration:**
   - Renkli select seçenekleri (Config'den renk çekme)
   - Canlı validasyon (Alpine.js)
   - Flash effect (görsel geri bildirim)
   - Birim fiyat badge (otomatik hesaplama)

4. **Category-Specific Intelligence:**
   - Yazlık: Sezonluk fiyatlandırma otomasyonu
   - Konut: Net/Brüt m² tutarlılık kontrolü
   - Arsa: İmar plan analizi (Cortex Knowledge Service)

### Örnek Senaryolar

**Senaryo 1: Yazlık Sezonluk Fiyat Hesaplama**

Gemini'den istenen:
- `gunluk_fiyat` girildiğinde
- Otomatik olarak `haftalik_fiyat` ve `aylik_fiyat` hesapla
- Sezon tipine göre farklı indirimler uygula
- Form alanlarını otomatik doldur

**Senaryo 2: Konut Net/Brüt m² Kontrolü**

Gemini'den istenen:
- `net_metrekare` ve `brut_metrekare` girildiğinde
- Anlık kontrol yap (Net > Brüt olamaz)
- Hata varsa input çerçevesi kırmızı olsun
- Uyarı mesajı göster

**Senaryo 3: Konut Birim Fiyat Hesaplama**

Gemini'den istenen:
- `satis_fiyati` ve `brut_m2` girildiğinde
- Otomatik olarak `m2_birim_fiyat` hesapla
- Badge içinde "Birim: 35.000 TL/m²" göster
- Piyasa ortalamasıyla karşılaştır

---

## 🎯 SONRAKI ADIMLAR

1. ✅ **Dokümantasyon:** Bu dokümantasyon oluşturuldu
2. ⏳ **Gemini JSON Güncelleme:** `GEMINI_COMPLETE_SYSTEM_DATA.json` güncellenecek
3. ⏳ **Testing:** Tüm kategoriler için test senaryoları
4. ⏳ **Performance:** Cache mekanizmaları

---

## 📝 CHANGELOG

### v2.1.0 (2025-11-30)

**Yeni Özellikler:**
- ✅ Yazlık Otomatik Fiyatlandırma Sistemi
  - Sezonluk fiyat hesaplama (Yaz, Ara Sezon, Kış)
  - Haftalık ve Aylık indirimli fiyatlar
  - UI entegrasyonu (Otomatik Hesapla butonu)
- ✅ Konut Akıllı Validasyon Sistemi
  - Net/Brüt m² tutarlılık kontrolü
  - Renkli oda sayısı seçimi
  - Birim fiyat badge (otomatik hesaplama)
  - Canlı validasyon (Alpine.js)

**Dosyalar:**
- `config/yali_options.php` (güncellendi - pricing_rules, oda_sayisi_options)
- `app/Services/CategoryFieldValidator.php` (güncellendi - getKonutRules, validateKonut)
- `app/Http/Controllers/Api/IlanAIController.php` (güncellendi - calculateSeasonalPrice, calculateKonutMetrics)
- `routes/api/v1/ai.php` (güncellendi - calculate-seasonal-price, calculate-konut-metrics)
- `resources/views/admin/ilanlar/components/category-fields/kiralik-fields.blade.php` (güncellendi - Yazlık fiyatlandırma UI)
- `resources/views/admin/ilanlar/components/category-fields/konut-fields.blade.php` (güncellendi - Konut validasyon UI)
- `resources/views/admin/ilanlar/components/field-dependencies-dynamic.blade.php` (güncellendi - oda_sayisi renklendirme)

**Dokümantasyon:**
- `.yalihan-bekci/knowledge/yazlik-konut-ai-automation-2025-11-30.md` (YENİ)
- `docs/ai/GEMINI_NEW_ARCHITECTURE_V2.1.md` (YENİ)

---

**Son Güncelleme:** 2025-11-30  
**Versiyon:** 2.1.0  
**Context7 Compliance:** ✅ %100



