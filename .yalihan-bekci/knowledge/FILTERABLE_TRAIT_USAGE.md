# Filterable Trait Kullanım Kılavuzu

**Tarih:** 2025-11-11 20:15  
**Durum:** ✅ OLUŞTURULDU

---

## 📚 OVERVIEW

`Filterable` trait, standart filtreleme, arama, sıralama ve tarih aralığı işlemleri için kapsamlı bir çözüm sunar. Code duplication'ı azaltmak ve tutarlı filter logic sağlamak için oluşturuldu.

---

## 🚀 KULLANIM ÖRNEKLERİ

### 1. Model'e Trait Ekleme

```php
use App\Traits\Filterable;

class Ilan extends Model
{
    use Filterable;
    
    // Opsiyonel: Aranabilir alanları tanımla
    protected $searchable = ['baslik', 'aciklama'];
}
```

### 2. Basit Kullanım - Request'ten Filtreleme

```php
// Controller'da
public function index(Request $request)
{
    $ilanlar = Ilan::filterFromRequest($request, [
        'search_fields' => ['baslik', 'aciklama'],
        'allowed_filters' => ['status', 'kategori_id', 'il_id'],
        'date_column' => 'created_at',
        'price_column' => 'fiyat',
        'default_sort' => 'created_at',
    ])->paginate(20);
    
    return view('admin.ilanlar.index', compact('ilanlar'));
}
```

### 3. Tekil Scope Kullanımı

```php
// Search
$query = Ilan::search($request->search, ['baslik', 'aciklama']);

// Filter
$query = Ilan::applyFilters($request, ['status', 'kategori_id']);

// Date Range
$query = Ilan::dateRange($request->start_date, $request->end_date);

// Price Range
$query = Ilan::priceRange($request->min_price, $request->max_price);

// Sort
$query = Ilan::sort($request->sort_by, $request->sort_order);

// Status
$query = Ilan::byStatus($request->status);
```

### 4. İlişki Üzerinden Arama

```php
// İlan sahibi adına göre arama
$query = Ilan::searchRelation('ilanSahibi', $request->search, ['ad', 'soyad']);
```

### 5. Tümünü Birleştirme

```php
$ilanlar = Ilan::query()
    ->search($request->search, ['baslik', 'aciklama'])
    ->applyFilters($request, ['status', 'kategori_id', 'il_id'])
    ->dateRange($request->start_date, $request->end_date)
    ->priceRange($request->min_price, $request->max_price)
    ->sort($request->sort_by, $request->sort_order)
    ->with(['ilanSahibi', 'kategori', 'il'])
    ->paginate(20);
```

### 6. Static Method ile Tek Satır

```php
$ilanlar = Ilan::filterAndPaginate($request, [
    'search_fields' => ['baslik', 'aciklama'],
    'allowed_filters' => ['status', 'kategori_id'],
], 20);
```

---

## 📋 SCOPE METODLARI

### `scopeApplyFilters()`
Genel filtreleme için kullanılır. Request object veya array kabul eder.

**Parametreler:**
- `$filters`: Request object veya filter array
- `$allowedFilters`: İzin verilen filter alanları (güvenlik için)

**Örnek:**
```php
$query->applyFilters($request, ['status', 'kategori_id', 'il_id']);
```

---

### `scopeSearch()`
Genel arama için kullanılır.

**Parametreler:**
- `$search`: Arama terimi
- `$fields`: Aranacak alanlar (boşsa searchable property kullanılır)

**Örnek:**
```php
$query->search('villa', ['baslik', 'aciklama']);
```

---

### `scopeSearchRelation()`
İlişki üzerinden arama için kullanılır.

**Parametreler:**
- `$relation`: İlişki adı
- `$search`: Arama terimi
- `$fields`: Aranacak alanlar

**Örnek:**
```php
$query->searchRelation('ilanSahibi', 'Ahmet', ['ad', 'soyad']);
```

---

### `scopeSort()`
Sıralama için kullanılır.

**Parametreler:**
- `$sortBy`: Sıralama alanı (null ise default kullanılır)
- `$sortDirection`: Sıralama yönü (asc/desc)
- `$defaultSort`: Varsayılan sıralama alanı

**Örnek:**
```php
$query->sort('fiyat', 'asc', 'created_at');
```

---

### `scopeDateRange()`
Tarih aralığı filtreleme için kullanılır.

**Parametreler:**
- `$startDate`: Başlangıç tarihi
- `$endDate`: Bitiş tarihi
- `$column`: Tarih kolonu (varsayılan: created_at)

**Örnek:**
```php
$query->dateRange('2025-01-01', '2025-12-31', 'created_at');
```

---

### `scopePriceRange()`
Fiyat aralığı filtreleme için kullanılır.

**Parametreler:**
- `$minPrice`: Minimum fiyat
- `$maxPrice`: Maksimum fiyat
- `$column`: Fiyat kolonu (varsayılan: fiyat)

**Örnek:**
```php
$query->priceRange(100000, 500000, 'fiyat');
```

---

### `scopeByStatus()`
Status filtreleme için kullanılır (Context7 uyumlu).

**Parametreler:**
- `$status`: Status değeri (true/false, 1/0, 'active'/'inactive')
- `$column`: Status kolonu (varsayılan: status)

**Örnek:**
```php
$query->byStatus('active');
$query->byStatus(1);
$query->byStatus(true);
```

---

### `scopeFilterFromRequest()`
Request'ten tüm filtreleri uygular (all-in-one method).

**Parametreler:**
- `$request`: Request object
- `$options`: Seçenekler array'i

**Options:**
- `search_fields`: Arama alanları
- `allowed_filters`: İzin verilen filter'lar
- `date_column`: Tarih kolonu
- `price_column`: Fiyat kolonu
- `default_sort`: Varsayılan sıralama

**Örnek:**
```php
$query->filterFromRequest($request, [
    'search_fields' => ['baslik', 'aciklama'],
    'allowed_filters' => ['status', 'kategori_id'],
    'date_column' => 'created_at',
    'price_column' => 'fiyat',
    'default_sort' => 'created_at',
]);
```

---

### `filterAndPaginate()`
Pagination ile birlikte filtreleme (static method).

**Parametreler:**
- `$request`: Request object
- `$options`: Seçenekler array'i
- `$perPage`: Sayfa başına kayıt sayısı

**Örnek:**
```php
$ilanlar = Ilan::filterAndPaginate($request, [
    'search_fields' => ['baslik', 'aciklama'],
    'allowed_filters' => ['status', 'kategori_id'],
], 20);
```

---

## 🔒 GÜVENLİK

### Allowed Filters
Güvenlik için `allowed_filters` parametresi kullanılmalıdır:

```php
// ✅ GÜVENLİ
$query->applyFilters($request, ['status', 'kategori_id', 'il_id']);

// ❌ GÜVENSİZ (tüm request parametreleri kabul edilir)
$query->applyFilters($request);
```

### Column Validation
Tüm scope metodları otomatik olarak column validation yapar. Geçersiz kolonlar atlanır.

---

## ⚡ PERFORMANS

### Schema Cache
Schema builder cache'lenir, her filter için tekrar kontrol edilmez.

### Eager Loading
Filterable trait eager loading sağlamaz, manuel olarak eklenmelidir:

```php
$ilanlar = Ilan::filterFromRequest($request)
    ->with(['ilanSahibi', 'kategori', 'il'])
    ->paginate(20);
```

---

## 📝 MIGRATION ÖRNEĞİ

### Eski Kod (Code Duplication)

```php
public function index(Request $request)
{
    $query = Ilan::query();
    
    if ($request->has('search') && $request->search) {
        $query->where('baslik', 'like', "%{$request->search}%");
    }
    
    if ($request->has('status') && $request->status) {
        $query->where('status', $request->status);
    }
    
    if ($request->has('min_fiyat') && $request->min_fiyat) {
        $query->where('fiyat', '>=', $request->min_fiyat);
    }
    
    if ($request->has('max_fiyat') && $request->max_fiyat) {
        $query->where('fiyat', '<=', $request->max_fiyat);
    }
    
    $sortBy = $request->sort_by ?? 'created_at';
    $sortOrder = $request->sort_order ?? 'desc';
    $query->orderBy($sortBy, $sortOrder);
    
    $ilanlar = $query->paginate(20);
}
```

### Yeni Kod (Filterable Trait)

```php
public function index(Request $request)
{
    $ilanlar = Ilan::filterFromRequest($request, [
        'search_fields' => ['baslik', 'aciklama'],
        'allowed_filters' => ['status'],
        'price_column' => 'fiyat',
        'default_sort' => 'created_at',
    ])->paginate(20);
}
```

**Kod Azalması:** ~30 satır → ~8 satır (%73 azalma)

---

## 🎯 BEST PRACTICES

1. **Always use allowed_filters** - Güvenlik için zorunlu
2. **Define searchable fields** - Model'de `$searchable` property tanımla
3. **Combine with eager loading** - N+1 query'leri önlemek için
4. **Use filterAndPaginate** - Tek satırda tüm işlemler için

---

**Son Güncelleme:** 2025-11-11 20:15  
**Durum:** ✅ OLUŞTURULDU VE DOKÜMANTE EDİLDİ

