# 📝 İLAN EKLEME SÜRECİ - DETAYLI REHBER

**Tarih:** 2025-11-27  
**Sistem:** Yalıhan Emlak - Laravel 10  
**Durum:** ✅ Aktif Production

---

## 📋 İÇİNDEKİLER

1. [Genel Bakış](#genel-bakış)
2. [Form Yapısı ve Bölümler](#form-yapısı-ve-bölümler)
3. [Kategori Seçim Sistemi](#kategori-seçim-sistemi)
4. [Dinamik Alan Sistemi](#dinamik-alan-sistemi)
5. [İlan Özellikleri (Features)](#ilan-özellikleri-features)
6. [Yayın Tipleri](#yayın-tipleri)
7. [Validasyon ve Kayıt](#validasyon-ve-kayıt)
8. [Akış Diyagramı](#akış-diyagramı)

---

## 🎯 GENEL BAKIŞ

### İlan Ekleme Formu Yapısı

İlan ekleme formu **10 ana bölümden** oluşur:

```
1. Kategori Sistemi (Ana Kategori → Alt Kategori → Yayın Tipi)
2. Lokasyon ve Harita (İl → İlçe → Mahalle + Koordinat)
3. Fiyat Yönetimi (Fiyat + Para Birimi)
4. Temel Bilgiler + AI Yardımcısı (Başlık, Açıklama)
5. Fotoğraflar (Çoklu fotoğraf yükleme)
6. İlan Özellikleri (Dinamik field dependencies)
7. Kişi (İlan Sahibi Seçimi)
8. Site/Apartman (Proje bilgileri)
9. Anahtar (Anahtar yönetimi)
10. Yayın Durumu (Status)
```

### Form İlerlemesi

- **Progress Bar:** Form dolduruldukça %0 → %100
- **Otomatik Kayıt:** Her 30 saniyede taslak olarak kaydedilir
- **Sticky Navigation:** Üstte sabit navigasyon menüsü
- **Section Navigation:** Bölümler arası hızlı geçiş

---

## 📐 FORM YAPISI VE BÖLÜMLER

### Bölüm 1: Kategori Sistemi

**Dosya:** `resources/views/admin/ilanlar/components/category-system.blade.php`

**3 Adımlı Seçim:**

1. **Ana Kategori** (Seviye 0)
   - Dropdown: Konut, İşyeri, Arsa, Yazlık Kiralama, Turistik Tesisler
   - JavaScript: `loadAltKategoriler(anaKategoriId)` fonksiyonu çağrılır
   - API: `/api/categories/sub/${anaKategoriId}`

2. **Alt Kategori** (Seviye 1)
   - Dropdown: Ana kategoriye göre dinamik yüklenir
   - Örnek: Konut → Daire, Villa, Müstakil Ev, Dubleks
   - JavaScript: `loadYayinTipleri(altKategoriId)` fonksiyonu çağrılır
   - API: `/api/categories/sub/${altKategoriId}` (veya yayın tipleri için özel endpoint)

3. **Yayın Tipi** (ilan_kategori_yayin_tipleri tablosu)
   - Dropdown: Alt kategoriye göre dinamik yüklenir
   - Örnek: Daire → Satılık, Kiralık
   - JavaScript: `category-changed` event'i dispatch edilir
   - Bu event, dinamik field dependencies sistemini tetikler

**Event Sistemi:**

```javascript
// Kategori değiştiğinde event dispatch edilir
window.dispatchEvent(new CustomEvent('category-changed', {
    detail: {
        category: {
            id: anaKategoriId,
            slug: kategoriSlug,  // 'konut', 'arsa', 'yazlik-kiralama'
            parent_slug: kategoriSlug
        },
        yayinTipi: yayinTipiText,  // 'Satılık', 'Kiralık', 'Günlük'
        yayinTipiId: yayinTipiId
    }
}));
```

---

### Bölüm 2: Lokasyon ve Harita

**Dosya:** `resources/views/admin/ilanlar/components/location-map.blade.php`

**3 Adımlı Lokasyon Seçimi:**

1. **İl (Province)**
   - Dropdown: Tüm Türkiye illeri
   - API: `/api/location/iller`
   - JavaScript: İl seçildiğinde ilçeler yüklenir

2. **İlçe (District)**
   - Dropdown: Seçilen ile göre dinamik yüklenir
   - API: `/api/location/ilce/${ilId}`
   - JavaScript: İlçe seçildiğinde mahalleler yüklenir

3. **Mahalle (Neighborhood)**
   - Dropdown: Seçilen ilçeye göre dinamik yüklenir
   - API: `/api/location/mahalle/${ilceId}`

**Harita Entegrasyonu:**

- **Leaflet.js** ile interaktif harita
- **Marker Placement:** Haritada tıklanarak koordinat seçimi
- **Reverse Geocoding:** Koordinattan adres bilgisi çekme (Nominatim API)
- **İki Yönlü Senkronizasyon:**
  - Dropdown'larda seçim yapınca → Harita güncellenir
  - Haritada marker yerleştirince → Dropdown'lar otomatik doldurulur

**Koordinat Yönetimi:**

```php
// Form field'ları
latitude (decimal) - Enlem
longitude (decimal) - Boylam
enlem (decimal) - Alias (backward compatibility)
boylam (decimal) - Alias (backward compatibility)
```

---

### Bölüm 3: Fiyat Yönetimi

**Dosya:** `resources/views/admin/ilanlar/components/price-management.blade.php`

**Fiyat Alanları:**

```php
fiyat (decimal) - Ana fiyat (ZORUNLU)
para_birimi (varchar) - TRY, USD, EUR, GBP (ZORUNLU)
fiyat_orijinal (decimal) - Orijinal fiyat (çoklu para birimi)
fiyat_try_cached (decimal) - TRY cache fiyatı
kur_orani (decimal) - Kur oranı
kur_tarihi (date) - Kur tarihi
```

**Çoklu Para Birimi Desteği:**

- Kullanıcı USD/EUR/GBP seçerse
- Sistem otomatik TRY'ye çevirir (TCMB API)
- Cache'lenmiş değer `fiyat_try_cached`'e kaydedilir

---

### Bölüm 4: Temel Bilgiler + AI Yardımcısı

**Dosya:** `resources/views/admin/ilanlar/components/basic-info.blade.php`

**Temel Alanlar:**

```php
baslik (varchar) - İlan başlığı (ZORUNLU, max 255)
aciklama (text) - İlan açıklaması (Opsiyonel)
```

**AI Yardımcısı (Dijital Danışman):**

AI yardımcısı 4 özellik sunar:

1. **Başlık Öner** (`ai-generate-title`)
   - Context: Kategori, lokasyon, fiyat
   - AI Provider: YalihanCortex → AIService
   - Örnek: "Muğla Bodrum Yalıkavak'ta Denize Sıfır 4+1 Villa"

2. **Açıklama Öner** (`ai-generate-description`)
   - Context: Kategori, lokasyon, fiyat, özellikler
   - AI Provider: YalihanCortex → AIService
   - Örnek: SEO uyumlu, detaylı açıklama metni

3. **Fiyat Öner** (`ai-price-suggestion`)
   - Context: Lokasyon, benzer ilanlar, piyasa analizi
   - AI Provider: PropertyValuationService + AI analiz
   - Örnek: "Piyasa analizine göre önerilen fiyat: 10.500.000 TL"

4. **Alan Önerileri** (`ai-field-suggestion`)
   - Context: Kategori, yayın tipi, lokasyon
   - AI Provider: YalihanCortex → Field Suggestion
   - Örnek: Arsa için KAKS/TAKS önerileri, Yazlık için sezon fiyatları

**AI Context Hazırlık Sistemi:**

Form dolduruldukça AI context'i hazırlanır:

```
%0 → Kategori seçilmedi
%25 → Kategori seçildi
%50 → Lokasyon seçildi
%75 → Fiyat girildi
%100 → Tüm bilgiler hazır (AI önerileri en iyi şekilde çalışır)
```

---

### Bölüm 5: Fotoğraflar

**Dosya:** `resources/views/admin/ilanlar/components/photos.blade.php`

**Fotoğraf Yönetimi:**

- **Drag & Drop:** Fotoğrafları sürükle-bırak ile yükleme
- **Çoklu Seçim:** Birden fazla fotoğraf aynı anda
- **Kapak Fotoğrafı:** Bir fotoğraf kapak olarak işaretlenebilir
- **Sıralama:** Fotoğrafları sürükleyerek sıralama
- **Önizleme:** Yüklenmeden önce önizleme
- **API:** `/api/admin/ilanlar/${ilanId}/fotograflar` (AJAX)

---

### Bölüm 6: İlan Özellikleri (Features)

**Dosya:** `resources/views/admin/ilanlar/components/field-dependencies-dynamic.blade.php`

**En Karmaşık ve Dinamik Bölüm!**

#### 6.1. Dinamik Field Dependencies Sistemi

**Nasıl Çalışır:**

1. **Kategori ve Yayın Tipi Seçildiğinde:**
   - `category-changed` event'i fırlatılır
   - Event'i dinleyen `FieldDependenciesManager` devreye girer

2. **API Çağrısı:**
   ```javascript
   GET /api/admin/field-dependencies?kategori_slug=arsa&yayin_tipi=Satılık
   ```

3. **Field Dependencies Yüklenir:**
   - `kategori_yayin_tipi_field_dependencies` tablosundan
   - Filtre: `kategori_slug` + `yayin_tipi` + `status = true`
   - Sıralama: `display_order ASC`

4. **Field'lar Gruplara Ayrılır:**
   - `field_category` kolonuna göre gruplama
   - Örnek gruplar:
     - `fiyatlandirma` → 💰 Fiyatlandırma
     - `fiziksel_ozellikler` → 📐 Fiziksel Özellikler
     - `arsa` → 🗺️ Arsa Özellikleri
     - `yazlik` → 🏖️ Yazlık Özellikleri

#### 6.2. Field Tipleri

**7 Farklı Field Tipi:**

1. **text** - Metin input
   - Örnek: `ada_no`, `parsel_no`

2. **number** - Sayısal input
   - Örnek: `oda_sayisi`, `banyo_sayisi`, `alan_m2`

3. **boolean** - Checkbox (True/False)
   - Örnek: `havuz`, `esyali`, `yola_cephe`

4. **select** - Dropdown (Seçenekli)
   - Örnek: `imar_statusu` → ["İmarlı", "İmarsız", "Villa İmarlı"]
   - Options: `field_options` (JSON) kolonundan gelir

5. **textarea** - Çok satırlı metin
   - Örnek: `ozel_notlar`

6. **date** - Tarih seçici
   - Örnek: `sezon_baslangic`, `sezon_bitis`

7. **price** - Fiyat inputu
   - Örnek: `gunluk_fiyat`, `haftalik_fiyat`

#### 6.3. Field Kategorileri (Gruplar)

**Ana Kategoriler:**

```php
💰 fiyatlandirma - Fiyat Bilgileri
📐 fiziksel_ozellikler - Fiziksel Özellikler (m², oda sayısı, vb.)
🔌 donanim_tesisat - Donanım & Tesisat (Isıtma, Elektrik, vb.)
🏖️ dismekan_olanaklar - Dış Mekan & Olanaklar (Bahçe, Havuz, vb.)
🛏️ yatak_odasi_konfor - Yatak Odası & Konfor
➕ ek_hizmetler - Ek Hizmetler
🗺️ arsa - Arsa Özellikleri (Ada, Parsel, KAKS, TAKS, vb.)
🏖️ yazlik - Yazlık Özellikleri (Günlük fiyat, Havuz, vb.)
🏢 isyeri - İşyeri Özellikleri
⭐ ozellik - Genel Özellikler
```

#### 6.4. Accordion Yapısı

Her field kategorisi bir **accordion** (açılır/kapanır) kartıdır:

```
┌─────────────────────────────────────────┐
│ 💰 Fiyatlandırma                        │
│ 5 alan • 2 dolu                          │
│ ─────────────────────────────────────── │
│ [Açılıp kapanabilir içerik]            │
│  - Günlük Fiyat: [_____] TL/Gün        │
│  - Haftalık Fiyat: [_____] TL/Hafta    │
│  - Aylık Fiyat: [_____] TL/Ay          │
└─────────────────────────────────────────┘
```

**Varsayılan Açık Kategoriler:**
- `fiyatlandirma` - Fiyatlandırma (her zaman açık)
- `fiziksel_ozellikler` - Fiziksel Özellikler (her zaman açık)
- Diğerleri: Kapalı (kullanıcı tıklayarak açar)

#### 6.5. Field Doldurulma Takibi

Her field için **doldurulma durumu** takip edilir:

```
✅ Dolu - Yeşil badge (değer var)
⚪ Boş - Gri badge (değer yok)
```

**Progress Bar:**
- Her kategori için %0-100 doldurulma oranı
- Örnek: "💰 Fiyatlandırma: 40% (2/5)"

---

### Bölüm 7: Kişi (İlan Sahibi)

**Dosya:** `resources/views/admin/ilanlar/components/person-selector.blade.php`

**Unified Person Selector:**

- **Live Search:** Kişi araması (AJAX)
- **Hızlı Ekleme:** Yeni kişi ekleme modalı
- **İlişki Türleri:**
  - `ilan_sahibi_id` - İlan sahibi (ZORUNLU)
  - `ilgili_kisi_id` - İlgili kişi (Opsiyonel)

---

### Bölüm 8: Site/Apartman

**Dosya:** `resources/views/admin/ilanlar/components/site-selector.blade.php`

**Site/Apartman Seçimi:**

- Live search ile site/apartman seçimi
- Proje bilgileri otomatik doldurulur

---

### Bölüm 9: Anahtar

**Dosya:** `resources/views/admin/ilanlar/components/key-management.blade.php`

**Anahtar Yönetimi:**

```php
anahtar_kimde (varchar) - Anahtar kimde?
anahtar_turu (enum) - mal_sahibi, danisman, kapici, emlakci, yonetici, diger
anahtar_notlari (text) - Anahtar notları
anahtar_ulasilabilirlik (varchar) - Anahtar ulaşılabilirlik
```

---

### Bölüm 10: Yayın Durumu

**Dosya:** `resources/views/admin/ilanlar/components/status-selector.blade.php`

**Status Seçenekleri:**

```php
status (varchar) - 'Taslak', 'Aktif', 'Pasif', 'Beklemede', 'Yayında', 'Satıldı', 'Kiralandı'
crm_only (boolean) - Sadece CRM'de görünsün (public'e çıkmayacak)
```

---

## 🔄 KATEGORİ SEÇİM SİSTEMİ

### JavaScript Modülü

**Dosya:** `resources/js/admin/ilan-create/categories.js`

**Ana Fonksiyonlar:**

```javascript
// 1. Alt kategorileri yükle
function loadAltKategoriler(anaKategoriId) {
    // API: GET /api/categories/sub/${anaKategoriId}
    // Response: { data: [{ id, name, slug }] }
    // Dropdown'u doldur
}

// 2. Yayın tiplerini yükle
function loadYayinTipleri(altKategoriId) {
    // API: GET /api/categories/sub/${altKategoriId}
    // Response: Yayın tipleri (Satılık, Kiralık, vb.)
    // Dropdown'u doldur
    // category-changed event'i dispatch et
}

// 3. Tip bazlı alanları yükle
function loadTypeBasedFields() {
    // Kategoriye özel sabit alanları göster/gizle
    // Örnek: Arsa için ada_no, parsel_no alanlarını göster
}
```

**Event Listener Zinciri:**

```
1. Ana Kategori Change
   ↓
2. loadAltKategoriler() çağrılır
   ↓
3. Alt Kategori Dropdown doldurulur
   ↓
4. Alt Kategori Change
   ↓
5. loadYayinTipleri() çağrılır
   ↓
6. Yayın Tipi Dropdown doldurulur
   ↓
7. Yayın Tipi Change
   ↓
8. category-changed event dispatch edilir
   ↓
9. Field Dependencies sistemi tetiklenir
```

---

## 🎨 DİNAMIK ALAN SİSTEMİ

### Field Dependencies Tablosu

**Tablo:** `kategori_yayin_tipi_field_dependencies`

**2D Matrix Yapısı:**

```
Kategori × Yayın Tipi → Field'lar

Örnek:
arsa × Satılık → [ada_no, parsel_no, imar_statusu, kaks, taks, ...]
yazlik × Günlük → [gunluk_fiyat, haftalik_fiyat, havuz, min_konaklama, ...]
```

**Örnek Kayıt:**

```php
[
    'kategori_slug' => 'arsa',
    'yayin_tipi' => 'Satılık',
    'field_slug' => 'ada_no',
    'field_name' => 'Ada Numarası',
    'field_type' => 'text',
    'field_category' => 'arsa',
    'required' => true,
    'ai_auto_fill' => true,  // TKGM'den otomatik çekilebilir
    'display_order' => 1,
]
```

### Field Dependencies API

**Endpoint:** `GET /api/admin/field-dependencies`

**Parametreler:**

```javascript
{
    kategori_slug: 'arsa',  // veya kategori_id: 3
    yayin_tipi: 'Satılık'  // veya yayin_tipi_id: 1
}
```

**Response Format:**

```json
{
  "success": true,
  "data": [
    {
      "category": "arsa",
      "name": "Arsa Özellikleri",
      "icon": "🗺️",
      "fields": [
        {
          "id": 1,
          "slug": "ada_no",
          "name": "Ada Numarası",
          "type": "text",
          "required": true,
          "ai_auto_fill": true
        }
      ]
    }
  ]
}
```

### Frontend Rendering

**Alpine.js Component:**

```javascript
window.FieldDependenciesManager = {
    selectedKategoriSlug: null,
    selectedYayinTipi: null,
    fieldCategories: [],
    
    async loadFields() {
        // API çağrısı
        // Response'u parse et
        // fieldCategories'i güncelle
        // renderFields() çağrılır
    },
    
    renderFields() {
        // Her kategori için accordion kartı oluştur
        // Her field için input elementi oluştur
        // Event listener'lar ekle
    }
}
```

**Field Element Oluşturma:**

```javascript
createFieldElement(field, groupName) {
    switch(field.type) {
        case 'text':
            // <input type="text" name="features[${field.slug}]" />
            break;
        case 'boolean':
            // <input type="checkbox" name="features[${field.slug}]" />
            break;
        case 'select':
            // <select name="features[${field.slug}]">
            //   {field.options.map(opt => <option>{opt}</option>)}
            // </select>
            break;
    }
}
```

---

## 🔗 İLAN ÖZELLİKLERİ (FEATURES)

### Feature Sistemi

**İki Sistem Var:**

1. **Eski Sistem:** `ilan_feature` pivot tablosu
   - `Feature` modeli ile many-to-many ilişki
   - `value` kolonu ile değer saklama

2. **Yeni Sistem:** Field Dependencies → İlan modeline direkt kayıt
   - `ada_no`, `parsel_no`, `gunluk_fiyat` gibi kolonlar direkt `ilanlar` tablosunda

### Feature Kayıt Süreci

**Form Submission:**

```php
// Form'dan gelen data:
[
    'features' => [
        'ada_no' => '123',
        'parsel_no' => '456',
        'havuz' => '1',  // checkbox için '1' veya '0'
        'gunluk_fiyat' => '5000',
    ]
]
```

**Controller'da İşleme:**

```php
// 1. İlan kaydedilir
$ilan = Ilan::create([...]);

// 2. Features kaydedilir
if ($request->has('features')) {
    $featuresToAttach = [];
    
    foreach ($request->features as $featureId => $featureValue) {
        if ($featureValue && $featureValue !== '' && $featureValue !== '0') {
            $featuresToAttach[$featureId] = [
                'value' => $featureValue,
            ];
        }
    }
    
    // Pivot tabloya kaydet
    $ilan->features()->attach($featuresToAttach);
}
```

### Feature Modelleri

**Tablo Yapısı:**

```
features (özellikler tablosu)
├── id
├── name (Özellik adı)
├── slug (URL-friendly slug)
├── tip (text, number, boolean, select)
├── kategori_id (Özellik kategorisi)
└── status

ilan_feature (pivot tablo)
├── ilan_id
├── feature_id
└── value (JSON veya string)
```

---

## 📊 YAYIN TİPLERİ

### Yayın Tipi Yapısı

**Tablo:** `ilan_kategori_yayin_tipleri`

**Örnek Kayıtlar:**

```php
// Konut → Daire → Satılık
[
    'kategori_id' => 4,  // Daire kategorisi ID
    'yayin_tipi' => 'Satılık',
    'status' => true,
]

// Arsa → İmar Arsaları → Satılık
[
    'kategori_id' => 7,  // İmar Arsaları kategorisi ID
    'yayin_tipi' => 'Satılık',
    'status' => true,
]

// Yazlık → Günlük Kiralama → Günlük
[
    'kategori_id' => 12,  // Günlük Kiralama kategorisi ID
    'yayin_tipi' => 'Günlük',
    'status' => true,
]
```

**Yayın Tipi Seçenekleri:**

```
Satılık
Kiralık
Günlük (Yazlık için)
Haftalık (Yazlık için)
Aylık (Yazlık için)
Devren Satış
İnşaat Halinde
Ön Satış
```

---

## ✅ VALİDASYON VE KAYIT

### Validasyon Sistemi

**1. Temel Validasyon (Her Zaman):**

```php
[
    'baslik' => 'required|string|max:255',
    'fiyat' => 'required|numeric|min:0',
    'para_birimi' => 'required|in:TRY,USD,EUR,GBP',
    'ana_kategori_id' => 'required|exists:ilan_kategorileri,id',
    'alt_kategori_id' => 'required|exists:ilan_kategorileri,id',
    'yayin_tipi_id' => 'required|exists:ilan_kategori_yayin_tipleri,id',
    'ilan_sahibi_id' => 'required|exists:kisiler,id',
    'status' => 'required|string|in:Taslak,Aktif,Pasif,Beklemede',
]
```

**2. Kategori Bazlı Validasyon:**

**CategoryFieldValidator Service:**

```php
$validator = new CategoryFieldValidator();
$categoryRules = $validator->getRules($kategoriSlug, $yayinTipiSlug);

// Örnek: Arsa için
[
    'ada_no' => 'required|string|max:50',
    'parsel_no' => 'required|string|max:50',
    'imar_statusu' => 'required|string',
    'kaks' => 'nullable|numeric|min:0|max:10',
    'taks' => 'nullable|numeric|min:0|max:1',
]

// Örnek: Yazlık için
[
    'gunluk_fiyat' => 'required|numeric|min:0',
    'min_konaklama' => 'required|integer|min:1',
    'max_misafir' => 'nullable|integer|min:1',
]
```

### Kayıt Süreci

**Controller:** `IlanController::store()`

**Adımlar:**

```
1. Validasyon
   ↓
2. Database Transaction Başlat
   ↓
3. İlan Oluştur
   Ilan::create([...])
   ↓
4. Fiyat Geçmişi Kaydet
   IlanPriceHistory::create([...])
   ↓
5. Referans Numarası Oluştur
   IlanReferansService::generateReferansNo()
   ↓
6. Features Kaydet
   $ilan->features()->attach([...])
   ↓
7. Yazlık Detayları Kaydet (Eğer yazlık ise)
   YazlikDetail::create([...])
   ↓
8. Fotoğrafları Kaydet (Eğer varsa)
   IlanFotografi::create([...])
   ↓
9. Transaction Commit
   ↓
10. Redirect veya JSON Response
```

---

## 🔄 AKIŞ DİYAGRAMI

### Tam İlan Ekleme Akışı

```
┌─────────────────────────────────────────────────────────┐
│ 1. KULLANICI FORM SAYFASINI AÇAR                        │
│    /admin/ilanlar/create                                │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 2. FORM YÜKLENİR                                        │
│    - Ana kategoriler dropdown doldurulur                │
│    - Lokasyon dropdown'ları boş hazırlanır              │
│    - Harita başlatılır                                  │
│    - Field dependencies container boş                   │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 3. KULLANICI ANA KATEGORİ SEÇER                         │
│    Örnek: "Konut"                                       │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 4. JAVASCRIPT: loadAltKategoriler(anaKategoriId)        │
│    API: GET /api/categories/sub/${anaKategoriId}        │
│    Response: [Daire, Villa, Müstakil Ev, Dubleks]      │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 5. ALT KATEGORİ DROPDOWN DOLDURULUR                     │
│    Kullanıcı "Daire" seçer                              │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 6. JAVASCRIPT: loadYayinTipleri(altKategoriId)          │
│    API: GET /api/categories/sub/${altKategoriId}        │
│    Response: [Satılık, Kiralık]                         │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 7. YAYIN TİPİ DROPDOWN DOLDURULUR                       │
│    Kullanıcı "Satılık" seçer                            │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 8. category-changed EVENT DİSPATCH EDİLİR               │
│    { category: { id, slug: 'konut' },                  │
│      yayinTipi: 'Satılık', yayinTipiId: 1 }            │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 9. FIELD DEPENDENCIES SİSTEMİ TETİKLENİR                │
│    FieldDependenciesManager.loadFields()                │
│    API: GET /api/admin/field-dependencies?              │
│         kategori_slug=konut&yayin_tipi=Satılık          │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 10. FIELD'LAR YÜKLENİR VE GÖSTERİLİR                    │
│     - Fiyatlandırma grubu (açık)                        │
│     - Fiziksel Özellikler grubu (açık)                  │
│     - Donanım & Tesisat grubu (kapalı)                  │
│     - Dış Mekan & Olanaklar grubu (kapalı)              │
│     - ...                                                │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 11. KULLANICI LOKASYON SEÇER                            │
│     İl: Muğla → İlçe: Bodrum → Mahalle: Yalıkavak      │
│     Haritada marker yerleştirir                         │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 12. KULLANICI FİYAT GİRER                               │
│     Fiyat: 10.000.000 TL                                │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 13. KULLANICI AI YARDIMCISI KULLANIR                    │
│     "Başlık Öner" butonuna tıklar                       │
│     AI Context: %100 hazır (kategori + lokasyon + fiyat)│
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 14. AI BAŞLIK ÜRETİR                                    │
│     "Muğla Bodrum Yalıkavak'ta Denize Sıfır 4+1 Villa" │
│     Başlık alanına otomatik doldurulur                  │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 15. KULLANICI FIELD'LARI DOLDURUR                       │
│     - Oda Sayısı: 4+1                                   │
│     - Net m²: 180                                       │
│     - Havuz: ✅                                         │
│     - ...                                               │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 16. KULLANICI FOTOĞRAFLAR YÜKLER                        │
│     Drag & Drop ile 5 fotoğraf                          │
│     İlk fotoğraf kapak olarak işaretlenir               │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 17. KULLANICI KİŞİ SEÇER                                │
│     İlan Sahibi: Ahmet Yılmaz (Live search ile)        │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 18. KULLANICI STATUS SEÇER                              │
│     Status: "Aktif"                                     │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 19. FORM SUBMİT                                         │
│     POST /admin/ilanlar                                 │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 20. BACKEND VALİDASYON                                  │
│     - Temel validasyon                                  │
│     - Kategori bazlı validasyon (CategoryFieldValidator)│
│     - Feature validasyonu                               │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 21. DATABASE TRANSACTION BAŞLAT                         │
│     DB::beginTransaction()                              │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 22. İLAN KAYDET                                         │
│     Ilan::create([...])                                 │
│     - Basit alanlar (baslik, fiyat, kategori_id, ...)   │
│     - Lokasyon (il_id, ilce_id, mahalle_id, lat, lng)   │
│     - Koordinatlar (latitude, longitude)                │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 23. FİYAT GEÇMİŞİ KAYDET                                │
│     IlanPriceHistory::create([...])                     │
│     - old_price: 0                                      │
│     - new_price: 10000000                               │
│     - change_reason: 'İlk ilan oluşturma'               │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 24. REFERANS NUMARASI OLUŞTUR                           │
│     IlanReferansService::generateReferansNo()           │
│     Örnek: "REF-2025-001234"                            │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 25. FEATURES KAYDET                                     │
│     $ilan->features()->attach([...])                    │
│     Pivot tablo: ilan_feature                           │
│     { feature_id: 1, value: '123' },                    │
│     { feature_id: 2, value: '456' },                    │
│     { feature_id: 3, value: '1' }                       │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 26. YAZLIK DETAYLARI KAYDET (Eğer yazlık ise)           │
│     YazlikDetail::create([...])                         │
│     - gunluk_fiyat, haftalik_fiyat, aylik_fiyat         │
│     - havuz, min_konaklama, max_misafir                 │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 27. FOTOĞRAFLARI KAYDET (Eğer varsa)                    │
│     IlanFotografi::create([...])                        │
│     - Dosya yükleme (Storage)                           │
│     - İlan ile ilişkilendirme                           │
│     - Kapak fotoğrafı işaretleme                        │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 28. TRANSACTION COMMIT                                  │
│     DB::commit()                                        │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 29. EVENT FIRLAT (OBSERVER)                             │
│     IlanObserver::created($ilan)                        │
│     → IlanCreated event                                 │
│     → FindMatchingDemands listener                      │
│     → Smart Property Matching (AI)                      │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│ 30. REDİRECT                                            │
│     → /admin/ilanlar/${ilan->id}                        │
│     → Success mesajı gösterilir                         │
└─────────────────────────────────────────────────────────┘
```

---

## 📊 VERİTABANI İLİŞKİLERİ

### İlan Kaydı ile İlgili Tablolar

```
ilanlar (ANA TABLO)
├── id (PK)
├── baslik, aciklama, fiyat, para_birimi
├── ana_kategori_id → ilan_kategorileri.id
├── alt_kategori_id → ilan_kategorileri.id
├── yayin_tipi_id → ilan_kategori_yayin_tipleri.id
├── il_id → iller.id
├── ilce_id → ilceler.id
├── mahalle_id → mahalleler.id
├── ilan_sahibi_id → kisiler.id
├── danisman_id → users.id
└── ... (100+ kolon)

ilan_kategorileri
├── id (PK)
├── name, slug, parent_id, seviye
└── ...

ilan_kategori_yayin_tipleri
├── id (PK)
├── kategori_id → ilan_kategorileri.id
├── yayin_tipi (varchar)
└── ...

ilan_feature (Pivot)
├── ilan_id → ilanlar.id
├── feature_id → features.id
└── value (string/JSON)

features
├── id (PK)
├── name, slug, tip, kategori_id
└── ...

yazlik_fiyatlandirma (Yazlık için)
├── id (PK)
├── ilan_id → ilanlar.id
├── sezon_tipi (yaz, ara_sezon, kis)
├── gunluk_fiyat, haftalik_fiyat, aylik_fiyat
└── ...

yazlik_rezervasyonlar (Yazlık için)
├── id (PK)
├── ilan_id → ilanlar.id
├── check_in, check_out
├── misafir_sayisi
└── ...
```

---

## 🔍 ÖNEMLİ DOSYALAR

### Frontend (JavaScript)

- `resources/js/admin/ilan-create/categories.js` - Kategori seçim sistemi
- `resources/js/admin/ilan-create/field-dependencies.js` - Field dependencies manager
- `resources/views/admin/ilanlar/create.blade.php` - Ana form sayfası

### Backend (PHP)

- `app/Http/Controllers/Admin/IlanController.php` - Controller (create, store)
- `app/Services/CategoryFieldValidator.php` - Kategori bazlı validasyon
- `app/Models/Ilan.php` - İlan modeli
- `app/Models/KategoriYayinTipiFieldDependency.php` - Field dependencies modeli

### API Endpoints

- `GET /api/categories/sub/{id}` - Alt kategoriler veya yayın tipleri
- `GET /api/admin/field-dependencies` - Field dependencies listesi
- `GET /api/admin/features/category/{slug}` - Özellikler (features)

---

## 💡 ÖZET

### İlan Ekleme Süreci Özeti

```
1. KATEGORİ SEÇİMİ (3 adım)
   Ana Kategori → Alt Kategori → Yayın Tipi
   
2. DİNAMİK ALANLAR YÜKLENİR
   Kategori + Yayın Tipi → Field Dependencies
   
3. FORM DOLDURULUR
   Lokasyon, Fiyat, Temel Bilgiler, Features, Fotoğraflar
   
4. VALİDASYON
   Temel + Kategori bazlı validasyon
   
5. KAYIT
   İlan + Features + Yazlık Detayları (eğer varsa)
   
6. EVENT SİSTEMİ
   IlanCreated event → AI eşleştirme
```

**En Kritik Özellik:**
- **Dinamik Field Dependencies Sistemi** - Kategori ve yayın tipine göre form alanları otomatik yüklenir
- **AI Yardımcısı** - Bağlama göre başlık, açıklama, fiyat önerileri
- **Otomatik Kayıt** - Her 30 saniyede taslak olarak kaydedilir
- **Progress Tracking** - Form doldurulma oranı takip edilir

---

**Hazırlayan:** Yalıhan Emlak AI System  
**Tarih:** 2025-11-27  
**Versiyon:** 1.0.0



