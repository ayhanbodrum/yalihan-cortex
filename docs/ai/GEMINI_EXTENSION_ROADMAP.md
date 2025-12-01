# 🚀 Yapılan Değişikliklerin Genişletme Yol Haritası

**Tarih:** 2025-11-30  
**Versiyon:** 2.0.0  
**Hedef:** Arsa kategorisinde yapılan değişiklikleri diğer kategorilere uygulama planı

---

## ✅ YAPILAN DEĞİŞİKLİKLER (Arsa Kategorisi)

### 1. CategoryFieldValidator ✅
- JSON-based validation rules
- Config entegrasyonu (`yali_options.php`)
- `satis_fiyati` zorunlu validasyon
- `imar_statusu` config'den seçeneklerle validasyon

### 2. AI-Powered Endpoints ✅
- `POST /api/ai/fetch-tkgm` - TKGM sorgulama
- `POST /api/ai/calculate-m2-price` - m² fiyat hesaplama

### 3. Frontend AI Integration ✅
- TKGM sorgulama butonu (ada_no, parsel_no yanında)
- Renkli imar durumu seçenekleri
- Otomatik form doldurma
- m² fiyatı otomatik hesaplama

---

## 🔄 GENİŞLETİLEBİLİR ALANLAR

### 1. YAZLIK KATEGORİSİ (Yüksek Öncelik) 🔥

#### A. Validasyon Kuralları

**Mevcut Durum:**
- `getKiralikRules()` var ama basit
- Yazlık özel validasyon kuralları yok

**Yapılacaklar:**
```php
// app/Services/CategoryFieldValidator.php

protected function getYazlikRules(): array
{
    $sezonTipleri = config('yali_options.sezon_tipleri', []);
    $sezonValues = array_keys($sezonTipleri);
    
    return [
        'gunluk_fiyat' => 'required|numeric|min:0',
        'sezon_tipi' => 'nullable|string|in:'.implode(',', $sezonValues),
        'minimum_konaklama' => 'nullable|integer|min:1',
        'maksimum_konaklama' => 'nullable|integer|min:1',
        'havuz' => 'nullable|boolean',
        'denize_uzaklik' => 'nullable|numeric|min:0',
    ];
}
```

**Config Eklenecek:**
```php
// config/yali_options.php

'sezon_tipleri' => [
    'yaz' => [
        'label' => 'Yaz Sezonu',
        'color' => 'yellow',
        'icon' => '☀️',
    ],
    'ara_sezon' => [
        'label' => 'Ara Sezon',
        'color' => 'orange',
        'icon' => '🍂',
    ],
    'kis' => [
        'label' => 'Kış Sezonu',
        'color' => 'blue',
        'icon' => '❄️',
    ],
],
```

#### B. AI Endpoints

**1. Sezonluk Fiyat Hesaplama:**
```php
// POST /api/ai/calculate-seasonal-price
{
  "gunluk_fiyat": 5000,
  "sezon_tipi": "yaz",
  "minimum_konaklama": 3
}

Response:
{
  "success": true,
  "data": {
    "haftalik_fiyat": 35000,  // gunluk_fiyat × 7 × discount
    "aylik_fiyat": 150000,    // gunluk_fiyat × 30 × discount
    "yaz_sezonu_fiyat": 5000,
    "ara_sezon_fiyat": 3500,  // -30%
    "kis_sezonu_fiyat": 2500  // -50%
  }
}
```

**2. Haftalık/Aylık Fiyat Hesaplama:**
```php
// POST /api/ai/calculate-rental-price
{
  "gunluk_fiyat": 5000,
  "nights": 10
}

Response:
{
  "success": true,
  "data": {
    "total_daily": 50000,
    "total_weekly": 35000,  // 7 gece için haftalık indirim
    "total_monthly": 150000, // 30 gece için aylık indirim
    "recommended_price": 35000
  }
}
```

#### C. Frontend Integration

**1. Sezon Tipi Renkli Select:**
- Yaz → Yellow (☀️)
- Ara Sezon → Orange (🍂)
- Kış → Blue (❄️)

**2. Fiyat Hesaplama Butonu:**
- `gunluk_fiyat` field'ının yanına "💰 Hesapla" butonu
- Butona tıklayınca sezonluk fiyatları otomatik hesapla

**3. Auto-Fill Butonları:**
- `denize_uzaklik` → Harita API'den çek
- `havuz` → İlan açıklamasından AI ile tespit et

---

### 2. KONUT KATEGORİSİ (Orta Öncelik) 🏠

#### A. Validasyon Kuralları

**Mevcut Durum:**
- `getKonutRules()` var ama hardcoded
- Config entegrasyonu yok

**Yapılacaklar:**
```php
protected function getKonutRules(): array
{
    $odaSayisiOptions = config('yali_options.oda_sayisi_options', []);
    $odaValues = array_column($odaSayisiOptions, 'value');
    
    $isitmaTipiOptions = config('yali_options.isitma_tipi_options', []);
    $isitmaValues = array_column($isitmaTipiOptions, 'value');
    
    return [
        'oda_sayisi' => 'required|string|in:'.implode(',', $odaValues),
        'brut_metrekare' => 'required|numeric|min:10|max:10000',
        'net_metrekare' => 'nullable|numeric|min:10|max:10000',
        'banyo_sayisi' => 'nullable|integer|min:0|max:10',
        'isitma_tipi' => 'nullable|string|in:'.implode(',', $isitmaValues),
        // ...
    ];
}
```

#### B. AI Endpoints

**1. Oda Başına Fiyat Hesaplama:**
```php
// POST /api/ai/calculate-price-per-room
{
  "satis_fiyati": 5000000,
  "oda_sayisi": "3+1"
}

Response:
{
  "success": true,
  "data": {
    "price_per_room": 1250000,
    "room_count": 4,  // 3+1 = 4 oda
    "formula": "5000000 / 4 = 1250000"
  }
}
```

**2. Kiralık Fiyat Önerisi:**
```php
// POST /api/ai/suggest-rental-price
{
  "kategori": "konut",
  "oda_sayisi": "3+1",
  "brut_metrekare": 150,
  "il_id": 6,
  "ilce_id": 123
}

Response:
{
  "success": true,
  "data": {
    "suggested_price": 15000,
    "price_range": {
      "min": 12000,
      "max": 18000
    },
    "market_average": 14500,
    "confidence": 85
  }
}
```

#### C. Frontend Integration

**1. Oda Sayısı Renkli Select:**
- 1+1 → Blue
- 2+1 → Green
- 3+1 → Yellow
- 4+1+ → Purple

**2. Isınma Tipi Renkli Select:**
- Kombi → Orange
- Merkezi Sistem → Blue
- Doğalgaz → Green

---

### 3. İŞYERİ KATEGORİSİ (Orta Öncelik) 🏢

#### A. Validasyon Kuralları

**Mevcut Durum:**
- `getIsyeriRules()` var ama basit

**Yapılacaklar:**
```php
protected function getIsyeriRules(): array
{
    return [
        'brut_metrekare' => 'required|numeric|min:10|max:50000',
        'net_metrekare' => 'nullable|numeric|min:10|max:50000',
        'kat_sayisi' => 'nullable|integer|min:1|max:100',
        'isyeri_tipi' => 'nullable|string|in:Ofis,Dükkan,Fabrika,Depo',
        // ...
    ];
}
```

#### B. AI Endpoints

**1. m² Başına Fiyat Hesaplama:**
```php
// POST /api/ai/calculate-price-per-m2
{
  "satis_fiyati": 10000000,
  "brut_metrekare": 500
}

Response:
{
  "success": true,
  "data": {
    "price_per_m2": 20000,
    "formula": "10000000 / 500 = 20000"
  }
}
```

**2. Ticari Fiyat Önerisi:**
```php
// POST /api/ai/suggest-commercial-price
{
  "isyeri_tipi": "Ofis",
  "brut_metrekare": 500,
  "il_id": 6,
  "ilce_id": 123
}

Response:
{
  "success": true,
  "data": {
    "suggested_price": 10000000,
    "price_per_m2": 20000,
    "market_average": 19500,
    "confidence": 80
  }
}
```

---

## 📊 ÖNCELİK SIRASI

### 🔥 Yüksek Öncelik (1-2 Hafta)

1. **Yazlık Kategorisi**
   - ✅ Validasyon kuralları
   - ⏳ Sezonluk fiyat hesaplama endpoint'i
   - ⏳ Frontend integration (fiyat hesaplama butonu)

**Neden?**
- Yazlık sezonluk fiyat sistemi kompleks
- Müşteri talebi yüksek
- AI ile otomatik hesaplama büyük zaman tasarrufu

### ⭐ Orta Öncelik (2-4 Hafta)

2. **Konut Kategorisi**
   - ⏳ Oda başına fiyat hesaplama
   - ⏳ Kiralık fiyat önerisi

3. **İşyeri Kategorisi**
   - ⏳ m² başına fiyat hesaplama
   - ⏳ Ticari fiyat önerisi

### 📌 Düşük Öncelik (1-2 Ay)

4. **Genel AI Magic Wand**
   - ⏳ Tüm kategoriler için "AI Öner" butonu
   - ⏳ Field-level AI suggestions
   - ⏳ Auto-fill butonları (harita, API'ler)

---

## 🎯 UYGULAMA STRATEJİSİ

### Pattern Replication

**Arsa Pattern'i:**
```
1. CategoryFieldValidator → getArsaRules()
2. AI Controller → fetchTkgm(), calculateM2Price()
3. Frontend → TKGM button, colored select
```

**Yazlık Uygulaması:**
```
1. CategoryFieldValidator → getYazlikRules() ✅ (yapılacak)
2. AI Controller → calculateSeasonalPrice() ✅ (yapılacak)
3. Frontend → Fiyat hesaplama button, renkli sezon select ✅ (yapılacak)
```

### Kod Örneği

**Yazlık Endpoint:**
```php
// app/Http/Controllers/Api/IlanAIController.php

public function calculateSeasonalPrice(Request $request): JsonResponse
{
    $validated = $this->validateRequestWithResponse($request, [
        'gunluk_fiyat' => 'required|numeric|min:0',
        'sezon_tipi' => 'nullable|string|in:yaz,ara_sezon,kis',
    ]);

    $gunlukFiyat = (float) $validated['gunluk_fiyat'];
    $sezonTipi = $validated['sezon_tipi'] ?? 'yaz';

    // Hesaplamalar
    $haftalikFiyat = round($gunlukFiyat * 7 * 0.95, 2); // 5% indirim
    $aylikFiyat = round($gunlukFiyat * 30 * 0.85, 2); // 15% indirim

    $araSezonFiyat = round($gunlukFiyat * 0.70, 2); // -30%
    $kisSezonFiyat = round($gunlukFiyat * 0.50, 2); // -50%

    return ResponseService::success([
        'gunluk_fiyat' => $gunlukFiyat,
        'haftalik_fiyat' => $haftalikFiyat,
        'aylik_fiyat' => $aylikFiyat,
        'yaz_sezonu_fiyat' => $gunlukFiyat,
        'ara_sezon_fiyat' => $araSezonFiyat,
        'kis_sezonu_fiyat' => $kisSezonFiyat,
    ], 'Sezonluk fiyatlar hesaplandı');
}
```

---

## 📚 GEMINI İÇİN ÖĞRENME REHBERİ

Gemini'ye şu pattern'ler öğretilmeli:

1. **Validation Pattern:**
   - Config'den seçenekleri çekme
   - JSON-based validation rules

2. **AI Endpoint Pattern:**
   - Standardize edilmiş endpoint yapısı
   - ResponseService kullanımı
   - Error handling

3. **Frontend Integration Pattern:**
   - AI buton ekleme
   - Renkli select seçenekleri
   - Auto-fill mekanizması

**Öğrenme Dosyası:** `docs/ai/GEMINI_NEW_ARCHITECTURE_V2.0.md`

---

**Son Güncelleme:** 2025-11-30  
**Versiyon:** 1.0.0  
**Durum:** 📋 Planlama Aşaması



