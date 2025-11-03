# İlan Etiket Sistemi

## 📋 Genel Bakış

İlan etiket sistemi, emlak ilanlarına çeşitli özellikler ve promosyonlar eklemek için kullanılan esnek bir yapıdır.

## 🏗️ Mimari

### Database Tabloları

#### `etiketler` Tablosu
```php
- id
- name
- slug (unique)
- type (promo, location, investment, feature)
- icon (FontAwesome)
- color (text rengi)
- bg_color (arka plan rengi)
- badge_text (opsiyonel kısa metin)
- is_badge (boolean - resim üstünde gösterilsin mi?)
- target_url (opsiyonel - SEO friendly link)
- description
- status (boolean)
- order
- timestamps
```

#### `ilan_etiketler` Pivot Tablosu
```php
- id
- ilan_id (FK -> ilanlar)
- etiket_id (FK -> etiketler)
- display_order (görüntülenme sırası)
- is_featured (öne çıkan mı?)
- timestamps
```

### Model İlişkileri

#### `app/Models/Ilan.php`
```php
public function etiketler(): BelongsToMany
{
    return $this->belongsToMany(Etiket::class, 'ilan_etiketler')
                ->withPivot(['display_order', 'is_featured'])
                ->orderByPivot('display_order')
                ->withTimestamps();
}
```

#### `app/Models/Etiket.php`
```php
public function ilanlar(): BelongsToMany
{
    return $this->belongsToMany(Ilan::class, 'ilan_etiketler')
                ->withPivot(['display_order', 'is_featured'])
                ->withTimestamps();
}

// Scope metodları
public function scopeBadges($query)
{
    return $query->where('is_badge', true)
                 ->where('status', true)
                 ->orderBy('order');
}

public function scopeType($query, $type)
{
    return $query->where('type', $type);
}
```

## 📊 Etiket Tipleri

### 1. `promo` - Promosyon Badge'leri
Örnek: Fırsat, İndirim, Özel Fiyat
- Resim üstünde badge olarak gösterilir
- Dikkat çekici renkler kullanılır
- İlan başlığı/yönlendirme yapabilir

### 2. `location` - Lokasyon Özellikleri
Örnek: Denize Sıfır, Deniz Manzaralı
- İlan detayında özellikler bölümünde gösterilir
- Icon + text formatında
- Filtreleme için kullanılabilir

### 3. `investment` - Yatırım Özellikleri
Örnek: Golden Visa, Vatandaşlık, Pasaport
- Yatırım teşvikleri için özel badge'ler
- SEO-friendly URL'lere bağlanabilir
- Farklı ülke bayrakları için kullanılabilir

### 4. `feature` - Genel Özellikler
Örnek: Müstakil, Havuzlu, Özel Plajlı
- Diğer özelliklerle uyumlu gösterim
- Filtreleme ve arama için optimize

## 🎨 Frontend Gösterimi

### Badge Komponenti
```blade
{{-- resources/views/components/ilan-badge.blade.php --}}
@foreach($ilan->etiketler->where('is_badge', true)->sortBy('pivot.display_order') as $etiket)
    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold"
          style="background: {{ $etiket->bg_color }}; color: {{ $etiket->color }};">
        <i class="{{ $etiket->icon }} mr-1"></i>
        {{ $etiket->badge_text ?? $etiket->name }}
    </span>
@endforeach
```

### Detay Sayfası
```blade
<div class="ilan-etiketler">
    @foreach($ilan->etiketler as $etiket)
        <span class="etiklet-etiketi" 
              style="color: {{ $etiket->color }}; background: {{ $etiket->bg_color }};">
            <i class="{{ $etiket->icon }}"></i>
            {{ $etiket->name }}
        </span>
    @endforeach
</div>
```

## 🔍 Filtreleme Sistemi

### Controller
```php
public function index(Request $request)
{
    $query = Ilan::query();
    
    // Etiket filtresi
    if ($request->has('etiket')) {
        $query->whereHas('etiketler', function($q) use ($request) {
            $q->whereIn('etiketler.slug', $request->etiket);
        });
    }
    
    // Tip filtresi (örn: sadece yatırım özelliği olanlar)
    if ($request->has('etiket_tip')) {
        $query->whereHas('etiketler', function($q) use ($request) {
            $q->where('etiketler.type', $request->etiket_tip);
        });
    }
    
    return $query->paginate(12);
}
```

### Frontend Filter
```html
<div class="etiket-filtreleri">
    <h3>Promosyon</h3>
    @foreach(Etiket::type('promo')->where('is_badge', true)->get() as $etiket)
        <label>
            <input type="checkbox" name="etiket[]" value="{{ $etiket->slug }}">
            {{ $etiket->name }}
        </label>
    @endforeach
</div>
```

## 📊 Örnek Kullanım Senaryoları

### Senaryo 1: Promosyon Badge'i
```php
$firsatEtiketi = Etiket::where('slug', 'firsat')->first();
$ilan = Ilan::find(1);

$ilan->etiketler()->attach($firsatEtiketi->id, [
    'display_order' => 1,
    'is_featured' => true
]);
```

### Senaryo 2: Çoklu Etiket Ekleme
```php
$etiketler = ['denize-sifir', 'havuzlu', 'golden-visa'];
$etiketIds = Etiket::whereIn('slug', $etiketler)->pluck('id');

$ilan->etiketler()->sync($etiketIds);
```

### Senaryo 3: Badge'li Etiketleri Getirme
```php
$ilanlar = Ilan::whereHas('etiketler', function($q) {
    $q->where('is_badge', true);
})->get();
```

## 🎯 SEO Optimizasyonu

### target_url Kullanımı
```php
// Etiket oluştururken
Etiket::create([
    'name' => 'Golden Visa',
    'slug' => 'golden-visa',
    'type' => 'investment',
    'target_url' => '/golden-visa-programi',
]);

// Frontend'de
@if($etiket->target_url)
    <a href="{{ $etiket->target_url }}" class="etiklet-link">
        {{ $etiket->name }}
    </a>
@else
    <span class="etiklet-badge">{{ $etiket->name }}</span>
@endif
```

## 📝 Admin Panel Yönetimi

### Etiket Listesi
```php
Route::get('/admin/ilan-etiketleri', [IlanEtiketController::class, 'index']);
```

### Etiket Oluşturma
```php
Route::post('/admin/ilan-etiketleri', [IlanEtiketController::class, 'store']);
```

### İlan-Etiket Atama
```php
Route::post('/admin/ilanlar/{ilan}/etiketler', [IlanController::class, 'attachEtiketler']);
```

## 🚀 Gelecek Geliştirmeler

1. **Otomatik Etiket Önerisi**: İlan özelliklerine göre otomatik etiket öner
2. **Toplu İşlemler**: Çoklu ilana etiket atama
3. **Analytics**: En çok tıklanan etiketler
4. **Renk Paleti**: Hazır renk şemaları
5. **Çoklu Dil Desteği**: Etiket çevirileri

## ✅ Başarı Ölçütleri

- ✅ Tüm ilan türleri için etiket desteği
- ✅ Frontend'de görsel badge gösterimi
- ✅ Filtreleme ve arama entegrasyonu
- ✅ SEO-friendly URL yönetimi
- ✅ Admin panel'den kolay yönetim
- ✅ Performans odaklı sorgu optimizasyonu

---

**Son Güncelleme**: 27 Ekim 2025  
**Versiyon**: 1.0.0  
**Durum**: ✅ Aktif
