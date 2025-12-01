# 📊 İlan Yaşı Analizi - Yorgun İlan Tespiti

**Tarih:** 2025-11-29  
**Versiyon:** 1.0.0  
**Durum:** ✅ Aktif  
**Context7 Standardı:** C7-MARKET-INTELLIGENCE-AGE-2025-11-29

---

## 🎯 AMAÇ

İlan kayıt tarihi (`listing_date`) kullanılarak, ilanın ne kadar zamandır pazarda olduğunu tespit etmek ve "yorgun ilan" analizi yapmak. Bu analiz, fiyat karşılaştırması ve pazarlık stratejisi için kritik öneme sahiptir.

---

## 📊 İLAN YAŞ KATEGORİLERİ

### Kategori Tanımları

| Kategori | Yaş Aralığı | Açıklama | Pazarlık Potansiyeli |
|----------|-------------|----------|---------------------|
| **Yeni** | 0-7 gün | Yeni yayınlanmış ilanlar | Düşük (henüz fiyat düşüşü beklenmez) |
| **Taze** | 8-30 gün | Normal süreçteki ilanlar | Orta (standart pazarlık) |
| **Yorgun** | 31-90 gün | Uzun süredir pazarda | Yüksek (fiyat düşüşüne yatkın) |
| **Çok Yorgun** | 90+ gün | Çok uzun süredir pazarda | Çok Yüksek (agresif indirim beklenir) |

---

## 🔧 TEKNİK DETAYLAR

### Model Metodları

**Dosya:** `app/Models/MarketListing.php`

#### 1. İlan Yaşını Hesapla

```php
/**
 * İlan yaşını gün cinsinden hesapla
 * Context7: İlan ne kadar zamandır pazarda?
 */
public function getAgeInDays(): ?int
{
    if (!$this->listing_date) {
        return null;
    }

    return now()->diffInDays($this->listing_date);
}
```

**Kullanım:**
```php
$listing = MarketListing::find(1);
$age = $listing->getAgeInDays(); // 45 (gün)
```

#### 2. Yorgun İlan Kontrolü

```php
/**
 * İlan "yorgun" mu? (30 günden fazla pazarda)
 * Context7: Yorgun ilanlar fiyat düşüşüne daha yatkındır
 */
public function isTired(): bool
{
    $age = $this->getAgeInDays();
    return $age !== null && $age > 30;
}
```

**Kullanım:**
```php
if ($listing->isTired()) {
    // Yorgun ilan - fiyat düşüşü beklenir
    $suggestedDiscount = 5; // %5 indirim öner
}
```

#### 3. Yaş Kategorisi

```php
/**
 * İlan yaş kategorisi
 * Context7: Yeni, Taze, Yorgun, Çok Yorgun
 */
public function getAgeCategory(): string
{
    $age = $this->getAgeInDays();
    
    if ($age === null) {
        return 'bilinmiyor';
    }

    if ($age <= 7) {
        return 'yeni'; // 0-7 gün
    } elseif ($age <= 30) {
        return 'taze'; // 8-30 gün
    } elseif ($age <= 90) {
        return 'yorgun'; // 31-90 gün
    } else {
        return 'cok_yorgun'; // 90+ gün
    }
}
```

**Kullanım:**
```php
$category = $listing->getAgeCategory(); // 'yorgun'
```

---

## 🔍 QUERY SCOPES

### 1. Yorgun İlanlar

```php
// 30+ günlük ilanlar
$tiredListings = MarketListing::tired()->get();
```

### 2. Yeni İlanlar

```php
// 7 gün içinde yayınlanan ilanlar
$newListings = MarketListing::new()->get();
```

### 3. Yaş Aralığı

```php
// 15-45 gün arası ilanlar
$listings = MarketListing::ageBetween(15, 45)->get();
```

---

## 📈 KULLANIM SENARYOLARI

### Senaryo 1: Fiyat Karşılaştırması

```php
// Bir ilanın piyasa fiyatını karşılaştırırken
$ourListing = Ilan::find(123);
$marketListings = MarketListing::where('location_il', $ourListing->il->il_adi)
    ->where('m2_brut', '>=', $ourListing->brut_m2 * 0.9)
    ->where('m2_brut', '<=', $ourListing->brut_m2 * 1.1)
    ->get();

foreach ($marketListings as $marketListing) {
    $age = $marketListings->getAgeInDays();
    $category = $marketListings->getAgeCategory();
    
    // Yorgun ilanlar daha düşük fiyata satılabilir
    if ($category === 'yorgun' || $category === 'cok_yorgun') {
        $adjustedPrice = $marketListing->price * 0.95; // %5 indirimli fiyat
    } else {
        $adjustedPrice = $marketListing->price;
    }
    
    // Karşılaştırma yap
    if ($ourListing->fiyat > $adjustedPrice) {
        // Bizim ilanımız pahalı - indirim öner
    }
}
```

### Senaryo 2: Pazarlık Stratejisi

```php
// Müşteriye pazarlık önerisi
$marketListing = MarketListing::find(456);

if ($marketListing->isTired()) {
    $strategy = [
        'message' => 'Bu ilan 45 gündür pazarda. Yorgun ilan - fiyat düşüşüne yatkın.',
        'suggested_discount' => 5, // %5 indirim öner
        'confidence' => 0.85,
    ];
} else {
    $strategy = [
        'message' => 'Bu ilan yeni (7 gün). Fiyat düşüşü beklenmez.',
        'suggested_discount' => 0,
        'confidence' => 0.90,
    ];
}
```

### Senaryo 3: Dashboard İstatistikleri

```php
// Dashboard'da gösterilecek istatistikler
$stats = [
    'new_listings' => MarketListing::new()->count(), // 0-7 gün
    'fresh_listings' => MarketListing::ageBetween(8, 30)->count(), // 8-30 gün
    'tired_listings' => MarketListing::tired()->count(), // 30+ gün
    'very_tired_listings' => MarketListing::ageBetween(91, 365)->count(), // 90+ gün
];
```

---

## 🎨 UI GÖSTERİMİ

### Badge Renkleri

```html
<!-- Yeni İlan -->
<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
    Yeni ({{ $listing->getAgeInDays() }} gün)
</span>

<!-- Taze İlan -->
<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
    Taze ({{ $listing->getAgeInDays() }} gün)
</span>

<!-- Yorgun İlan -->
<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
    Yorgun ({{ $listing->getAgeInDays() }} gün)
</span>

<!-- Çok Yorgun İlan -->
<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
    Çok Yorgun ({{ $listing->getAgeInDays() }} gün)
</span>
```

---

## 📊 VERİ YAPISI

### listing_date Alanı

**Tip:** `date`  
**Format:** `YYYY-MM-DD`  
**Örnek:** `2025-11-20`

**Önemli Notlar:**
- İlanın ilk yayınlandığı tarih
- Dış kaynaklardan (Sahibinden, Hepsiemlak) çekilir
- `null` olabilir (eski veriler için)

### İlan Yaşı Hesaplama

```php
// Bugün: 2025-11-29
// listing_date: 2025-11-20
// Yaş: 9 gün (taze kategori)
```

---

## 🔄 VERİ SENKRONİZASYONU

### n8n Bot'tan Gelen Veri

```json
{
    "source": "sahibinden",
    "listings": [
        {
            "external_id": "123456",
            "listing_date": "2025-11-20",  // İlan tarihi
            "price": 1500000,
            ...
        }
    ]
}
```

### Sync Endpoint

**POST** `/api/admin/market-intelligence/sync`

**İşlem:**
1. Yeni ilanlar için `listing_date` kaydedilir
2. Mevcut ilanlar için `listing_date` güncellenmez (ilk tarih korunur)
3. İlan yaşı otomatik hesaplanır

---

## ✅ CONTEXT7 UYUMLULUK

### Standartlar

- ✅ **Model Metodları:** İlan yaşı hesaplama metodları
- ✅ **Query Scopes:** Yorgun/yeni ilan filtreleme
- ✅ **Type Safety:** Null kontrolü ve type hints
- ✅ **Documentation:** Detaylı metod dokümantasyonu

### Yasaklı Patterns

- ❌ `enabled` field → ✅ `status` field
- ❌ Raw SQL → ✅ Eloquent scopes
- ❌ Inline calculations → ✅ Model metodları

---

## 📚 İLGİLİ DOSYALAR

### Backend

- `app/Models/MarketListing.php` - Model metodları
- `app/Http/Controllers/Admin/MarketIntelligenceController.php` - Sync endpoint
- `routes/admin.php` - API routes

### Dokümantasyon

- `docs/market-intelligence/ILAN_YASI_ANALIZI.md` - Bu dosya
- `docs/market-intelligence/PAZAR_ISTIHBARATI_SISTEMI.md` - Genel sistem
- `docs/market-intelligence/VERI_CEKME_STRATEJISI.md` - Veri çekme stratejisi

---

## 🎯 ÖZET

**Ne Yapar?**
- İlan yaşını hesaplar (gün cinsinden)
- Yorgun ilanları tespit eder (30+ gün)
- Yaş kategorisi belirler (Yeni, Taze, Yorgun, Çok Yorgun)

**Neden Önemli?**
- Fiyat karşılaştırması için kritik
- Pazarlık stratejisi belirleme
- Piyasa analizi ve trend takibi

**Nasıl Kullanılır?**
- `$listing->getAgeInDays()` - Yaş hesapla
- `$listing->isTired()` - Yorgun mu?
- `$listing->getAgeCategory()` - Kategori al
- `MarketListing::tired()->get()` - Yorgun ilanları filtrele

---

**Son Güncelleme:** 2025-11-29  
**Versiyon:** 1.0.0  
**Durum:** ✅ Production Ready






