# 🌍 API Entegrasyon Planı - 2025-11-04

**Tarih:** 2025-11-04 03:00  
**Yeni API'ler:** TurkiyeAPI, WikiMapia İyileştirmeleri, TCMB Kur API  
**Toplam Süre:** ~12 saat (3 gün)

---

## 📊 ÖZET

| API | Durum | Süre | Öncelik | ROI |
|-----|-------|------|---------|-----|
| **TurkiyeAPI** | Yeni | 2.5h | YÜKSEK | ⭐⭐⭐⭐⭐ |
| **WikiMapia** | İyileştirme | 5h | ORTA | ⭐⭐⭐⭐ |
| **TCMB Kur API** | Yeni | 4.5h | YÜKSEK | ⭐⭐⭐⭐⭐ |
| **Etiket-İlan** | Yeni | 2h | ORTA | ⭐⭐⭐ |

**Toplam:** 14 saat (3 iş günü)

---

## 1️⃣ TURKİYEAPI ENTEGRASYONU 🇹🇷

### API Bilgileri

```yaml
URL: https://api.turkiyeapi.dev/v1
Dokümantasyon: https://api.turkiyeapi.dev/docs
Tip: REST API (JSON)
Lisans: Open Source
Maliyet: Ücretsiz ✅

Endpoints:
  GET /v1/provinces (İller + filtreleme)
  GET /v1/provinces/:id
  GET /v1/districts (İlçeler)
  GET /v1/districts/:id
  GET /v1/neighborhoods (Mahalleler)
  GET /v1/neighborhoods/:id
  GET /v1/villages (Köyler) 🆕
  GET /v1/villages/:id 🆕
  GET /v1/towns (Beldeler) 🆕 CRITICAL!
  GET /v1/towns/:id 🆕
```

### Neden Kritik?

```yaml
Problem:
  Bodrum Gümüşlük = BELDE (town)
  Bodrum Yalıkavak = BELDE
  
Mevcut sistemde:
  ❌ Mahalle olarak yok
  ❌ İlçe değil
  ❌ Bulunamıyor!

TurkiyeAPI ile:
  ✅ GET /v1/towns?name=Gümüşlük&province=Muğla
  ✅ Bulunur!
  ✅ Nüfus, koordinat, posta kodu gelir

Fayda: Tatil bölgeleri için ZORUNLU! 🏖️
```

### Entegrasyon Adımları (2.5 saat)

#### 1. Service Oluştur (1 saat)

```php
// app/Services/TurkiyeAPIService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TurkiyeAPIService
{
    protected $baseUrl = 'https://api.turkiyeapi.dev/v1';
    protected $timeout = 10;
    protected $cacheTtl = 86400; // 24 saat
    
    public function getProvinces(array $filters = [])
    {
        $cacheKey = 'turkiyeapi.provinces.' . md5(json_encode($filters));
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($filters) {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/provinces", $filters);
            return $response->successful() ? $response->json() : null;
        });
    }
    
    public function getDistricts(array $filters = []) { ... }
    public function getNeighborhoods(array $filters = []) { ... }
    
    // 🆕 Yeni metodlar
    public function getVillages(array $filters = []) { ... }
    public function getTowns(array $filters = []) { ... }
    
    // 🆕 Helper metodlar
    public function getCoastalProvinces() {
        return $this->getProvinces(['isCoastal' => true]);
    }
    
    public function getMetropolitanProvinces() {
        return $this->getProvinces(['isMetropolitan' => true]);
    }
    
    public function searchLocation($name, $type = 'all') {
        // İl, ilçe, mahalle, köy, belde'de ara
    }
}
```

#### 2. Config (15dk)

```php
// config/services.php
'turkiyeapi' => [
    'base_url' => env('TURKIYE_API_URL', 'https://api.turkiyeapi.dev/v1'),
    'timeout' => env('TURKIYE_API_TIMEOUT', 10),
    'cache_ttl' => env('TURKIYE_API_CACHE_TTL', 86400),
],
```

#### 3. LocationController Entegrasyon (30dk)

```php
// app/Http/Controllers/Api/LocationController.php

use App\Services\TurkiyeAPIService;

protected $turkiyeAPI;

public function __construct(TurkiyeAPIService $turkiyeAPI)
{
    $this->turkiyeAPI = $turkiyeAPI;
}

// 🆕 Köy listesi
public function getVillages(Request $request)
{
    $districtId = $request->district_id;
    return $this->turkiyeAPI->getVillages([
        'districtId' => $districtId,
        'limit' => 100
    ]);
}

// 🆕 Belde listesi (CRITICAL!)
public function getTowns(Request $request)
{
    $districtId = $request->district_id;
    return $this->turkiyeAPI->getTowns([
        'districtId' => $districtId,
        'limit' => 100
    ]);
}
```

#### 4. Frontend Dropdown (1 saat)

```blade
{{-- components/unified-location-selector.blade.php --}}

<!-- Mahalle/Köy/Belde Seçici -->
<select name="mahalle_id" id="mahalle_id">
    <optgroup label="🏘️ Mahalleler">
        @foreach($mahalleler as $mahalle)
            <option value="mahalle_{{ $mahalle->id }}">
                {{ $mahalle->mahalle_adi }}
                ({{ number_format($mahalle->population ?? 0) }} kişi)
            </option>
        @endforeach
    </optgroup>
    
    <optgroup label="🌾 Köyler">
        <template x-for="village in villages">
            <option :value="'village_' + village.id" 
                    x-text="`${village.name} (${village.population} kişi)`">
            </option>
        </template>
    </optgroup>
    
    <optgroup label="🏖️ Beldeler (Tatil Bölgeleri)">
        <template x-for="town in towns">
            <option :value="'town_' + town.id" 
                    x-text="`${town.name} (${town.population} kişi)`">
            </option>
        </template>
    </optgroup>
</select>
```

```javascript
// Alpine.js
loadLocationOptions(districtId) {
    // Mahalleler (mevcut database)
    fetch(`/api/location/neighborhoods/${districtId}`)
        .then(r => r.json())
        .then(data => this.neighborhoods = data);
    
    // Köyler (TurkiyeAPI) 🆕
    fetch(`/api/turkiye/villages?districtId=${districtId}`)
        .then(r => r.json())
        .then(data => this.villages = data.data);
    
    // Beldeler (TurkiyeAPI) 🆕 CRITICAL!
    fetch(`/api/turkiye/towns?districtId=${districtId}`)
        .then(r => r.json())
        .then(data => this.towns = data.data);
}
```

### Kullanım Senaryoları

```yaml
1. Tatil Bölgeleri:
   Kullanıcı: "Bodrum Gümüşlük'te villa"
   Sistem:
     - İl: Muğla
     - İlçe: Bodrum
     - Belde: Gümüşlük 🏖️ (TurkiyeAPI'den gelir)
   Sonuç: Doğru lokasyon! ✅

2. Yatırım Analizi:
   GET /v1/provinces?isCoastal=true&minPopulation=100000
   → Kıyı şehirler, nüfus 100k+
   → Yatırım için hedef bölgeler
   → Dashboard widget

3. Posta Kodu:
   İlan: Kadıköy, İstanbul
   TurkiyeAPI: Posta kodu: 34XXX
   İlan detayda göster: "Kargo için: 34XXX"
```

---

## 2️⃣ WİKİMAPIA İYİLEŞTİRMELERİ 🗺️

### Mevcut Durum (%95)

```yaml
URL: /admin/wikimapia-search
Controller: WikimapiaSearchController ✅
Service: WikimapiaService ✅
View: wikimapia-search/index.blade.php ✅
API Functions: 7 adet ✅

Güçlü Yönler:
  ✅ Site/apartman arama
  ✅ Leaflet harita
  ✅ Cache (1 saat)
  ✅ Dark mode
  ✅ Toast notifications
  ✅ Custom searchResidentialComplexes()

Zayıf Yönler:
  ⚠️ Neo classes (Tailwind gerekli)
  ❌ Place detay modal yok
  ❌ İlan-Place ilişki yok
```

### İyileştirme Planı (5 saat)

#### 1. Tailwind Migration (1 saat)

```blade
{{-- wikimapia-search/index.blade.php --}}

<!-- Önce (Neo): -->
<button class="neo-btn neo-btn-primary">
    <i class="fas fa-search mr-2"></i>
    Site/Apartman Ara
</button>

<!-- Sonra (Tailwind): -->
<button class="px-6 py-3 bg-gradient-to-br from-blue-600 to-purple-600 
               text-white font-semibold rounded-lg shadow-lg 
               hover:shadow-xl hover:scale-105 
               active:scale-95 transition-all duration-200
               focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
    <svg class="w-5 h-5 inline mr-2">...</svg>
    Site/Apartman Ara
</button>
```

#### 2. Place Detay Modal (1 saat)

```blade
{{-- Modal component kullan --}}
<x-modal id="placeDetailModal" size="xl">
    <div x-data="placeDetail">
        <h2 x-text="place.title" class="text-2xl font-bold"></h2>
        
        <!-- Fotoğraf Galeri -->
        <div class="grid grid-cols-3 gap-2" x-show="place.photos">
            <template x-for="photo in place.photos">
                <img :src="photo.thumb_url" class="rounded-lg">
            </template>
        </div>
        
        <!-- Açıklama -->
        <p x-text="place.description"></p>
        
        <!-- Koordinatlar -->
        <div class="mt-4">
            <span>📍 {{ place.location.lat }}, {{ place.location.lon }}</span>
        </div>
        
        <!-- İlana Ekle Butonu -->
        <button @click="addToIlan(place)" 
                class="mt-4 px-6 py-3 bg-green-600 text-white rounded-lg">
            ✅ İlana Ekle
        </button>
    </div>
</x-modal>
```

#### 3. İlan-Place İlişkilendirme (2 saat)

```php
// Migration
Schema::table('ilanlar', function (Blueprint $table) {
    $table->unsignedBigInteger('wikimapia_place_id')->nullable();
    $table->string('wikimapia_place_title')->nullable();
    $table->text('wikimapia_place_url')->nullable();
    $table->json('wikimapia_data')->nullable();
});

// Ilan Model
public function wikimapiaPlace()
{
    return $this->hasOne(WikimapiaPlace::class, 'wikimapia_id', 'wikimapia_place_id');
}

// İlan create sayfasında
<button @click="searchWikimapiaPlace()" 
        class="px-4 py-2 bg-blue-600 text-white rounded-lg">
    🗺️ Site/Apartman Bul
</button>

<div x-show="selectedPlace" class="mt-2 p-4 bg-green-50 rounded-lg">
    <p>✅ Seçilen: <span x-text="selectedPlace.title"></span></p>
    <a :href="selectedPlace.url" target="_blank" class="text-blue-600">
        WikiMapia'da Gör →
    </a>
</div>
```

#### 4. Otomatik Site Adı (1 saat)

```javascript
// İlan create - koordinat girilince
async function onCoordinateChange(lat, lng) {
    // WikiMapia'dan otomatik site bul
    const places = await fetch('/admin/wikimapia-search/nearby', {
        method: 'POST',
        body: JSON.stringify({ lat, lon: lng, radius: 0.01 })
    }).then(r => r.json());
    
    if (places.data && places.data.length > 0) {
        const nearest = places.data[0];
        
        // Otomatik doldur
        document.getElementById('site_apartman_adi').value = nearest.title;
        document.getElementById('wikimapia_place_id').value = nearest.id;
        
        // Kullanıcıya göster
        toast.success(`✅ Site bulundu: ${nearest.title}`);
    }
}
```

---

## 2️⃣ TCMB KUR API ENTEGRASYONU 💱

### API Bilgileri

```yaml
URL: https://www.tcmb.gov.tr/kurlar/today.xml
Tip: XML API (Official T.C. Merkez Bankası)
Maliyet: Ücretsiz ✅
Güncelleme: Günlük (hafta içi 15:30)

Alternatif:
  - ECB API: https://data.ecb.europa.eu/api
  - exchangerate-api.com: https://api.exchangerate-api.com
```

### Entegrasyon Adımları (4.5 saat)

#### 1. TCMB Service (1.5 saat)

```php
// app/Services/TCMBService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TCMBService
{
    protected $baseUrl = 'https://www.tcmb.gov.tr/kurlar';
    
    /**
     * Güncel kurları çek
     */
    public function getExchangeRates()
    {
        $cacheKey = 'tcmb.rates.' . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 86400, function () {
            try {
                $url = "{$this->baseUrl}/today.xml";
                $response = Http::timeout(10)->get($url);
                
                if (!$response->successful()) {
                    Log::error('TCMB API failed', ['status' => $response->status()]);
                    return $this->getFallbackRates();
                }
                
                $xml = simplexml_load_string($response->body());
                $rates = [];
                
                foreach ($xml->Currency as $currency) {
                    $code = (string) $currency['CurrencyCode'];
                    $rates[$code] = [
                        'code' => $code,
                        'name' => (string) $currency->Isim,
                        'forex_buying' => (float) $currency->ForexBuying,
                        'forex_selling' => (float) $currency->ForexSelling,
                        'banknote_buying' => (float) $currency->BanknoteBuying,
                        'banknote_selling' => (float) $currency->BanknoteSelling,
                    ];
                }
                
                Log::info('TCMB kurları çekildi', ['count' => count($rates)]);
                return $rates;
                
            } catch (\Exception $e) {
                Log::error('TCMB API exception', ['error' => $e->getMessage()]);
                return $this->getFallbackRates();
            }
        });
    }
    
    /**
     * Belirli para biriminin kurubu
     */
    public function getRate($currencyCode)
    {
        $rates = $this->getExchangeRates();
        return $rates[$currencyCode] ?? null;
    }
    
    /**
     * TRY'ye çevir
     */
    public function convertToTRY($amount, $fromCurrency)
    {
        if ($fromCurrency === 'TRY') {
            return $amount;
        }
        
        $rate = $this->getRate($fromCurrency);
        if (!$rate) {
            return null;
        }
        
        return $amount * $rate['forex_selling'];
    }
    
    /**
     * Fallback kurlar (API çalışmazsa)
     */
    protected function getFallbackRates()
    {
        return [
            'USD' => ['code' => 'USD', 'forex_selling' => 34.00],
            'EUR' => ['code' => 'EUR', 'forex_selling' => 37.00],
            'GBP' => ['code' => 'GBP', 'forex_selling' => 43.00],
        ];
    }
}
```

#### 2. Otomatik Güncelleme (1 saat)

```php
// app/Console/Commands/UpdateExchangeRates.php
<?php

namespace App\Console\Commands;

use App\Services\TCMBService;
use App\Models\Ilan;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{
    protected $signature = 'exchange:update';
    protected $description = 'TCMB\'den kurları çek ve ilanları güncelle';
    
    public function handle(TCMBService $tcmb)
    {
        $this->info('🔄 Kurlar güncelleniyor...');
        
        // 1. Kurları çek
        $rates = $tcmb->getExchangeRates();
        
        // 2. Yurt dışı ilanları güncelle
        $ilanlar = Ilan::whereNotNull('para_birimi_orijinal')
            ->where('para_birimi_orijinal', '!=', 'TRY')
            ->get();
        
        $updated = 0;
        foreach ($ilanlar as $ilan) {
            $currency = $ilan->para_birimi_orijinal;
            $rate = $rates[$currency] ?? null;
            
            if ($rate) {
                $ilan->fiyat_try_cached = $ilan->fiyat_orijinal * $rate['forex_selling'];
                $ilan->kur_orani = $rate['forex_selling'];
                $ilan->kur_tarihi = now();
                $ilan->save();
                $updated++;
            }
        }
        
        $this->info("✅ {$updated} ilan güncellendi!");
    }
}

// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Günlük 16:00'da kur güncelle (TCMB 15:30'da yayınlar)
    $schedule->command('exchange:update')
        ->dailyAt('16:00')
        ->timezone('Europe/Istanbul');
}
```

#### 3. Kur Geçmişi (1 saat)

```php
// Migration: kur_gecmisi tablosu
Schema::create('kur_gecmisi', function (Blueprint $table) {
    $table->id();
    $table->string('para_birimi'); // USD, EUR, GBP
    $table->decimal('alis', 10, 4);
    $table->decimal('satis', 10, 4);
    $table->date('tarih');
    $table->string('kaynak')->default('TCMB'); // TCMB, ECB
    $table->timestamps();
    
    $table->unique(['para_birimi', 'tarih']);
    $table->index('tarih');
});

// Model
class KurGecmisi extends Model {
    protected $table = 'kur_gecmisi';
    protected $fillable = ['para_birimi', 'alis', 'satis', 'tarih', 'kaynak'];
    protected $casts = ['tarih' => 'date'];
}

// Command'a ekle
KurGecmisi::create([
    'para_birimi' => $currency,
    'alis' => $rate['forex_buying'],
    'satis' => $rate['forex_selling'],
    'tarih' => now()->toDateString(),
    'kaynak' => 'TCMB'
]);
```

#### 4. Kur Hesaplayıcı Widget (1 saat)

```blade
{{-- Admin Dashboard Widget --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
    <h3 class="text-lg font-bold mb-4">💱 Güncel Kurlar (TCMB)</h3>
    
    <div class="space-y-3" x-data="exchangeRates">
        <template x-for="rate in rates">
            <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div>
                    <span class="font-semibold" x-text="rate.code"></span>
                    <span class="text-sm text-gray-600 dark:text-gray-400" x-text="rate.name"></span>
                </div>
                <div class="text-right">
                    <p class="font-bold text-green-600" x-text="`₺${rate.forex_selling.toFixed(2)}`"></p>
                    <p class="text-xs text-gray-500">Alış: <span x-text="`₺${rate.forex_buying.toFixed(2)}`"></span></p>
                </div>
            </div>
        </template>
    </div>
    
    <p class="text-xs text-gray-500 mt-4">
        Son güncelleme: <span x-text="lastUpdate"></span>
    </p>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('exchangeRates', () => ({
        rates: [],
        lastUpdate: '',
        
        async init() {
            const response = await fetch('/api/exchange-rates');
            const data = await response.json();
            this.rates = data.rates;
            this.lastUpdate = data.updated_at;
        }
    }));
});
</script>
```

---

## 3️⃣ ETİKET-İLAN ENTEGRASYONU 🏷️

### Yapılacaklar (2 saat)

#### 1. Database (30dk)

```php
// Migration: ilan_etiket pivot tablosu
Schema::create('ilan_etiket', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ilan_id')->constrained('ilanlar')->onDelete('cascade');
    $table->foreignId('etiket_id')->constrained('etiketler')->onDelete('cascade');
    $table->timestamps();
    
    $table->unique(['ilan_id', 'etiket_id']);
});

// Popüler etiketler için seed
DB::table('etiketler')->insert([
    ['name' => 'Fırsat', 'slug' => 'firsat', 'color' => '#10B981', 'is_badge' => true],
    ['name' => 'Acil', 'slug' => 'acil', 'color' => '#EF4444', 'is_badge' => true],
    ['name' => 'VIP', 'slug' => 'vip', 'color' => '#F59E0B', 'is_badge' => true],
    ['name' => 'Yeni', 'slug' => 'yeni', 'color' => '#3B82F6', 'is_badge' => true],
]);
```

#### 2. Model İlişki (15dk)

```php
// app/Models/Ilan.php
public function etiketler()
{
    return $this->belongsToMany(
        Etiket::class,
        'ilan_etiket',
        'ilan_id',
        'etiket_id'
    )->withTimestamps();
}

// app/Models/Etiket.php
public function ilanlar()
{
    return $this->belongsToMany(
        Ilan::class,
        'ilan_etiket',
        'etiket_id',
        'ilan_id'
    )->withTimestamps();
}
```

#### 3. Admin UI (1 saat)

```blade
{{-- İlan create/edit --}}
<div class="mb-4">
    <label class="block text-sm font-bold mb-2">🏷️ Etiketler</label>
    
    <div class="flex flex-wrap gap-2">
        @foreach($etiketler as $etiket)
            <label class="inline-flex items-center cursor-pointer">
                <input type="checkbox" 
                       name="etiketler[]" 
                       value="{{ $etiket->id }}"
                       {{ in_array($etiket->id, old('etiketler', $ilan->etiketler->pluck('id')->toArray())) ? 'checked' : '' }}
                       class="sr-only peer">
                <span class="px-4 py-2 rounded-lg border-2 transition-all
                             peer-checked:bg-{{ $etiket->color }}-600 
                             peer-checked:text-white 
                             peer-checked:border-{{ $etiket->color }}-600
                             border-gray-300 text-gray-700
                             hover:border-{{ $etiket->color }}-400">
                    {{ $etiket->icon }} {{ $etiket->name }}
                </span>
            </label>
        @endforeach
    </div>
</div>
```

#### 4. İlan Listesi Badge (30dk)

```blade
{{-- İlan kartında --}}
<div class="flex gap-2 mb-2">
    @foreach($ilan->etiketler as $etiket)
        @if($etiket->is_badge)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                  style="background-color: {{ $etiket->bg_color }}; color: white;">
                {{ $etiket->icon }} {{ $etiket->badge_text ?? $etiket->name }}
            </span>
        @endif
    @endforeach
</div>
```

---

## 📊 ENTEGRASYON PLANI ÖZETİ

### Hafta 1: API Entegrasyonları (3 gün)

```yaml
Pazartesi (5 Kasım):
  ✅ Component Library (Modal, Checkbox, Radio) - 3h
  → Zaten tamamlandı!

Salı (6 Kasım):
  🆕 TurkiyeAPI Service - 2.5h
  🆕 Test + Documentation - 30dk

Çarşamba (7 Kasım):
  🆕 TCMB Kur API - 4.5h
  🆕 Kur widget + Test - 1h

Perşembe (8 Kasım):
  🆕 WikiMapia İyileştirmeleri - 5h
  🆕 Etiket-İlan Entegrasyonu - 2h

Cuma (9 Kasım):
  ✅ Testing (all APIs)
  ✅ Documentation
  ✅ Yalıhan Bekçi'ye öğret
```

**Toplam:** 14 saat (3 iş günü)

---

### Hafta 2: UI & Security (5 gün)

```yaml
Component Library kalan + UI Migration
```

---

## 🎯 FAYDA ANALİZİ

| Entegrasyon | Süre | Fayda | ROI |
|-------------|------|-------|-----|
| **TurkiyeAPI** | 2.5h | Tatil bölgeleri ✅ | ⭐⭐⭐⭐⭐ |
| **TCMB Kur** | 4.5h | Otomatik güncel kur ✅ | ⭐⭐⭐⭐⭐ |
| **WikiMapia++** | 5h | Site adı otomatik ✅ | ⭐⭐⭐⭐ |
| **Etiket-İlan** | 2h | Badge/filtreleme ✅ | ⭐⭐⭐ |

**Toplam:** 14h → Büyük kazanç! 🚀

---

## 📋 CHECKLIST

### TurkiyeAPI:
- [ ] TurkiyeAPIService.php oluştur
- [ ] config/services.php → turkiyeapi
- [ ] LocationController entegrasyon
- [ ] Frontend dropdown (köy/belde)
- [ ] Test endpoint'leri
- [ ] Yalıhan Bekçi'ye öğret

### TCMB Kur API:
- [ ] TCMBService.php oluştur
- [ ] UpdateExchangeRates command
- [ ] Kernel.php → schedule
- [ ] kur_gecmisi migration
- [ ] Kur widget (dashboard)
- [ ] İlan otomatik güncelleme
- [ ] Test + fallback

### WikiMapia:
- [ ] Tailwind migration
- [ ] Place detay modal
- [ ] ilan.wikimapia_place_id field
- [ ] Otomatik site adı
- [ ] İlan-Place link

### Etiket-İlan:
- [ ] ilan_etiket pivot migration
- [ ] Ilan::etiketler() ilişki
- [ ] Admin UI (checkbox)
- [ ] İlan listesi badge
- [ ] Etiket filtresi

---

**HEYECANLI! Yarın başlıyoruz! 🚀**

