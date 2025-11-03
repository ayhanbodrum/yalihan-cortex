# 🚀 TurkiyeAPI Implementasyon Planı - Detaylı Yol Haritası

**Tarih:** 23 Ekim 2025  
**Durum:** 🔴 BAŞLIYOR  
**Toplam Süre:** 10 İş Günü  
**Öncelik:** YÜKSEK

---

## 📋 **GENEL BAKIŞ**

```yaml
Hedef: TurkiyeAPI'yi Yalıhan Emlak sistemine tam entegre etmek
Kazanım:
  - %100 demografik veri zenginliği
  - +200% AI içerik kalitesi
  - +40% SEO performansı
  - 0 hata ile 30 gün cache
  
6 Faz:
  FAZ 1: Service + Cache (1-2 gün) ⬅️ BAŞLANGIC
  FAZ 2: Location Cascade (2-3 gün)
  FAZ 3: İlan Detay Widget (1 gün)
  FAZ 4: AI Enhancement (1 gün)
  FAZ 5: Dashboard Stats (1-2 gün)
  FAZ 6: Gelişmiş Filtreleme (1 gün)
```

---

## 🎯 **FAZ 1: TEMEL ALTYAPI** (1-2 Gün)

### **Hedef:**
TurkiyeAPI ile iletişim kuracak, cache edecek, fallback yapacak temel servis katmanını oluşturmak.

---

### **1.1: TurkiyeAPIService.php Oluştur** ⭐⭐⭐

**Dosya:** `app/Services/TurkiyeAPIService.php`

**Süre:** 2-3 saat

**İçerik:**
```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * TurkiyeAPI Service
 * Context7: C7-TURKIYE-API-SERVICE-2025-10-23
 * 
 * Demografik veri: Nüfus, yüzölçümü, rakım, bölge
 * Fallback: Local database (iller, ilceler, mahalleler)
 * Cache: 30 days (iller değişmez!)
 */
class TurkiyeAPIService
{
    protected string $baseUrl = 'https://api.turkiyeapi.dev/v1';
    protected int $timeout = 10;
    protected int $cacheTime = 2592000; // 30 gün

    /**
     * Get all provinces (81 il)
     * Cache: 30 days
     * 
     * Filters:
     * - isCoastal: true/false (Kıyı illeri)
     * - isMetropolitan: true/false (Büyükşehirler)
     * - region: 'Aegean', 'Marmara', etc. (Bölge)
     */
    public function getProvinces(array $filters = [])
    {
        $cacheKey = 'turkiye_api_provinces_' . md5(json_encode($filters));
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($filters) {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/provinces", $filters);
                
                if ($response->successful()) {
                    $data = $response->json()['data'] ?? [];
                    
                    Log::info('TurkiyeAPI provinces loaded', [
                        'count' => count($data),
                        'source' => 'turkiye_api'
                    ]);
                    
                    return $data;
                }
                
                return $this->getFallbackProvinces($filters);
                
            } catch (\Exception $e) {
                Log::warning('TurkiyeAPI provinces error', [
                    'error' => $e->getMessage()
                ]);
                return $this->getFallbackProvinces($filters);
            }
        });
    }

    /**
     * Get single province by ID
     * Includes: districts with population & area
     */
    public function getProvince(int $id)
    {
        $cacheKey = "turkiye_api_province_{$id}";
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($id) {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/provinces/{$id}");
                
                if ($response->successful()) {
                    $data = $response->json()['data'] ?? null;
                    
                    if ($data) {
                        Log::info('TurkiyeAPI province loaded', [
                            'id' => $id,
                            'name' => $data['name'],
                            'population' => $data['population'],
                            'source' => 'turkiye_api'
                        ]);
                        
                        return $data;
                    }
                }
                
                return $this->getFallbackProvince($id);
                
            } catch (\Exception $e) {
                Log::warning('TurkiyeAPI province error', [
                    'id' => $id,
                    'error' => $e->getMessage()
                ]);
                return $this->getFallbackProvince($id);
            }
        });
    }

    /**
     * Get districts of a province
     * Returns: Array of districts with population & area
     */
    public function getDistricts(int $provinceId)
    {
        $province = $this->getProvince($provinceId);
        
        if ($province && isset($province['districts'])) {
            return $province['districts'];
        }
        
        return $this->getFallbackDistricts($provinceId);
    }

    /**
     * Get single district by ID
     * Includes: neighborhoods
     */
    public function getDistrict(int $districtId)
    {
        $cacheKey = "turkiye_api_district_{$districtId}";
        
        return Cache::remember($cacheKey, $this->cacheTime, function () use ($districtId) {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("{$this->baseUrl}/districts/{$districtId}");
                
                if ($response->successful()) {
                    return $response->json()['data'] ?? null;
                }
                
                return $this->getFallbackDistrict($districtId);
                
            } catch (\Exception $e) {
                Log::warning('TurkiyeAPI district error', [
                    'id' => $districtId,
                    'error' => $e->getMessage()
                ]);
                return $this->getFallbackDistrict($districtId);
            }
        });
    }

    /**
     * Get neighborhoods of a district
     */
    public function getNeighborhoods(int $districtId)
    {
        $district = $this->getDistrict($districtId);
        
        if ($district && isset($district['neighborhoods'])) {
            return $district['neighborhoods'];
        }
        
        return $this->getFallbackNeighborhoods($districtId);
    }

    /**
     * Get coastal provinces (Kıyı illeri)
     */
    public function getCoastalProvinces()
    {
        return collect($this->getProvinces())
            ->where('isCoastal', true)
            ->values()
            ->toArray();
    }

    /**
     * Get metropolitan provinces (Büyükşehirler - 30 adet)
     */
    public function getMetropolitanProvinces()
    {
        return collect($this->getProvinces())
            ->where('isMetropolitan', true)
            ->values()
            ->toArray();
    }

    /**
     * Calculate population density (Nüfus yoğunluğu)
     * @return float kişi/km²
     */
    public function calculateDensity($population, $area)
    {
        if ($area <= 0) return 0;
        return round($population / $area, 2);
    }

    /**
     * Calculate investment potential score (Yatırım potansiyeli)
     * @return int 0-100
     */
    public function calculateInvestmentScore(array $provinceData, ?array $districtData = null)
    {
        $score = 0;
        
        // Kıyı ili: +30
        if (isset($provinceData['isCoastal']) && $provinceData['isCoastal']) {
            $score += 30;
        }
        
        // Büyükşehir: +25
        if (isset($provinceData['isMetropolitan']) && $provinceData['isMetropolitan']) {
            $score += 25;
        }
        
        // İl nüfusu > 500K: +20
        if (isset($provinceData['population']) && $provinceData['population'] > 500000) {
            $score += 20;
        }
        
        // İlçe nüfusu > 100K: +15
        if ($districtData && isset($districtData['population']) && $districtData['population'] > 100000) {
            $score += 15;
        }
        
        // Ege/Akdeniz bölgesi: +10
        $region = $provinceData['region']['en'] ?? $provinceData['region'] ?? '';
        if (in_array($region, ['Aegean', 'Mediterranean'])) {
            $score += 10;
        }
        
        return min($score, 100);
    }

    // ==========================================
    // FALLBACK METHODS (Local Database)
    // ==========================================

    protected function getFallbackProvinces(array $filters = [])
    {
        $query = \App\Models\Il::query();
        
        // Apply filters if any (limited compared to TurkiyeAPI)
        
        $iller = $query->orderBy('il_adi')->get();
        
        Log::info('Fallback provinces loaded', [
            'count' => $iller->count(),
            'source' => 'local_db'
        ]);
        
        return $iller->map(function ($il) {
            return [
                'id' => $il->id,
                'name' => $il->il_adi,
                'population' => null,
                'area' => null,
                'source' => 'local_db'
            ];
        })->toArray();
    }

    protected function getFallbackProvince(int $id)
    {
        $il = \App\Models\Il::find($id);
        
        if (!$il) {
            Log::error('Province not found in fallback', ['id' => $id]);
            return null;
        }
        
        $districts = \App\Models\Ilce::where('il_id', $id)
            ->orderBy('ilce_adi')
            ->get()
            ->map(fn($ilce) => [
                'id' => $ilce->id,
                'name' => $ilce->ilce_adi,
                'population' => null,
                'area' => null
            ])
            ->toArray();
        
        return [
            'id' => $il->id,
            'name' => $il->il_adi,
            'population' => null,
            'area' => null,
            'districts' => $districts,
            'source' => 'local_db'
        ];
    }

    protected function getFallbackDistricts(int $provinceId)
    {
        return \App\Models\Ilce::where('il_id', $provinceId)
            ->orderBy('ilce_adi')
            ->get()
            ->map(fn($ilce) => [
                'id' => $ilce->id,
                'name' => $ilce->ilce_adi,
                'population' => null,
                'area' => null
            ])
            ->toArray();
    }

    protected function getFallbackDistrict(int $districtId)
    {
        $ilce = \App\Models\Ilce::find($districtId);
        
        if (!$ilce) return null;
        
        return [
            'id' => $ilce->id,
            'name' => $ilce->ilce_adi,
            'population' => null,
            'area' => null,
            'source' => 'local_db'
        ];
    }

    protected function getFallbackNeighborhoods(int $districtId)
    {
        return \App\Models\Mahalle::where('ilce_id', $districtId)
            ->orderBy('mahalle_adi')
            ->get()
            ->map(fn($mahalle) => [
                'id' => $mahalle->id,
                'name' => $mahalle->mahalle_adi
            ])
            ->toArray();
    }

    /**
     * Clear all TurkiyeAPI cache
     */
    public function clearCache()
    {
        Cache::flush();
        Log::info('TurkiyeAPI cache cleared');
    }
}
```

**Test:**
```php
// Tinker test
$api = app(\App\Services\TurkiyeAPIService::class);

// Test 1: Get Muğla
$mugla = $api->getProvince(48);
dd($mugla['population']); // 1,066,736

// Test 2: Get Bodrum
$bodrum = collect($mugla['districts'])->firstWhere('id', 1197);
dd($bodrum['population']); // 198,335

// Test 3: Investment score
$score = $api->calculateInvestmentScore($mugla, $bodrum);
dd($score); // 100
```

---

### **1.2: Cache Mechanism** ⭐⭐

**Süre:** 1 saat

**İçerik:**
- ✅ 30 gün cache (iller değişmez)
- ✅ Cache key: `turkiye_api_*`
- ✅ Cache warming command

---

### **1.3: Fallback Sistemi** ⭐⭐⭐

**Süre:** 1 saat

**Test Scenarios:**
```php
// 1. TurkiyeAPI çalışıyor
$mugla = $api->getProvince(48);
assert($mugla['source'] === 'turkiye_api'); // ✅

// 2. TurkiyeAPI down (simulate)
Http::fake(['*' => Http::response([], 500)]);
$mugla = $api->getProvince(48);
assert($mugla['source'] === 'local_db'); // ✅ Fallback!

// 3. Cache hit
$mugla1 = $api->getProvince(48); // API call
$mugla2 = $api->getProvince(48); // Cache hit (no API call)
```

---

### **1.4: Service Provider Kaydı** ⭐

**Dosya:** `app/Providers/AppServiceProvider.php`

**Süre:** 10 dakika

```php
public function register()
{
    // TurkiyeAPI Service (Singleton)
    $this->app->singleton(\App\Services\TurkiyeAPIService::class, function ($app) {
        return new \App\Services\TurkiyeAPIService();
    });
}
```

---

### **1.5: Artisan Command (Cache Warming)** ⭐⭐

**Dosya:** `app/Console/Commands/TurkiyeAPICacheWarm.php`

**Süre:** 30 dakika

```php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TurkiyeAPIService;

class TurkiyeAPICacheWarm extends Command
{
    protected $signature = 'turkiye-api:cache-warm';
    protected $description = 'Warm up TurkiyeAPI cache (81 provinces + 973 districts)';

    public function handle(TurkiyeAPIService $api)
    {
        $this->info('🔥 Warming TurkiyeAPI cache...');
        
        // 1. Load all provinces
        $this->info('📍 Loading 81 provinces...');
        $provinces = $api->getProvinces();
        $this->info("✅ Loaded {count($provinces)} provinces");
        
        // 2. Load districts for each province
        $this->info('📍 Loading 973 districts...');
        $bar = $this->output->createProgressBar(count($provinces));
        
        $totalDistricts = 0;
        foreach ($provinces as $province) {
            $districts = $api->getDistricts($province['id']);
            $totalDistricts += count($districts);
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        $this->info("✅ Loaded {$totalDistricts} districts");
        $this->info('🎉 Cache warming completed!');
        
        // Stats
        $this->table(['Metric', 'Value'], [
            ['Provinces', count($provinces)],
            ['Districts', $totalDistricts],
            ['Cache Duration', '30 days'],
            ['Next Refresh', now()->addDays(30)->format('Y-m-d')]
        ]);
    }
}
```

**Scheduler:** `app/Console/Kernel.php`

```php
protected function schedule(Schedule $schedule)
{
    // Cache'i her ay yenile
    $schedule->command('turkiye-api:cache-warm')
        ->monthly()
        ->timezone('Europe/Istanbul');
}
```

**Manual Run:**
```bash
php artisan turkiye-api:cache-warm
```

---

### **1.6: Test API Endpoints** ⭐⭐

**Dosya:** `routes/api.php`

**Süre:** 30 dakika

```php
// TurkiyeAPI Test & Public Endpoints
// Context7: TURKIYE-API-PUBLIC-ENDPOINTS-2025-10-23
Route::prefix('turkiye-api')->name('turkiye-api.')->group(function () {
    
    // Get all provinces
    Route::get('/provinces', function (TurkiyeAPIService $api) {
        return response()->json([
            'success' => true,
            'data' => $api->getProvinces(),
            'count' => count($api->getProvinces())
        ]);
    });
    
    // Get single province
    Route::get('/provinces/{id}', function (int $id, TurkiyeAPIService $api) {
        $province = $api->getProvince($id);
        
        if (!$province) {
            return response()->json([
                'success' => false,
                'message' => 'İl bulunamadı'
            ], 404);
        }
        
        // Add calculated fields
        $province['population_formatted'] = number_format($province['population'] ?? 0);
        $province['area_formatted'] = number_format($province['area'] ?? 0) . ' km²';
        $province['density'] = $api->calculateDensity(
            $province['population'] ?? 0,
            $province['area'] ?? 1
        );
        $province['investment_score'] = $api->calculateInvestmentScore($province);
        
        return response()->json([
            'success' => true,
            'data' => $province
        ]);
    });
    
    // Get coastal provinces
    Route::get('/coastal', function (TurkiyeAPIService $api) {
        $coastal = $api->getCoastalProvinces();
        
        return response()->json([
            'success' => true,
            'data' => $coastal,
            'count' => count($coastal)
        ]);
    });
    
    // Get metropolitan cities
    Route::get('/metropolitan', function (TurkiyeAPIService $api) {
        $metropolitan = $api->getMetropolitanProvinces();
        
        return response()->json([
            'success' => true,
            'data' => $metropolitan,
            'count' => count($metropolitan)
        ]);
    });
    
    // Calculate investment score
    Route::post('/investment-score', function (Request $request, TurkiyeAPIService $api) {
        $validator = validator($request->all(), [
            'province_id' => 'required|integer',
            'district_id' => 'nullable|integer'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $province = $api->getProvince($request->province_id);
        $district = null;
        
        if ($request->district_id) {
            $district = collect($province['districts'] ?? [])
                ->firstWhere('id', $request->district_id);
        }
        
        $score = $api->calculateInvestmentScore($province, $district);
        
        return response()->json([
            'success' => true,
            'score' => $score,
            'category' => $score >= 80 ? 'Yüksek Potansiyel' : 
                         ($score >= 50 ? 'Orta Potansiyel' : 'Gelişmekte'),
            'stars' => $score >= 80 ? 3 : ($score >= 50 ? 2 : 1)
        ]);
    });
});
```

**Test URLs:**
```
GET http://127.0.0.1:8000/api/turkiye-api/provinces
GET http://127.0.0.1:8000/api/turkiye-api/provinces/48
GET http://127.0.0.1:8000/api/turkiye-api/coastal
GET http://127.0.0.1:8000/api/turkiye-api/metropolitan
POST http://127.0.0.1:8000/api/turkiye-api/investment-score
     { "province_id": 48, "district_id": 1197 }
```

---

## ✅ **FAZ 1 TAMAMLANDI - ÇEKLİST**

```yaml
□ TurkiyeAPIService.php oluşturuldu
□ Cache mechanism (30 gün) aktif
□ Fallback sistemi (local DB) çalışıyor
□ Service Provider'a kaydedildi
□ Cache warming command hazır
□ Scheduler'a eklendi
□ Test API endpoints çalışıyor
□ Unit tests yazıldı
□ Yalıhan Bekçi'ye öğretildi
```

---

## 🎯 **FAZ 2: LOCATION CASCADE MODERNİZASYONU** (2-3 Gün)

### **Hedef:**
İlan ekleme formundaki il/ilçe/mahalle seçimini TurkiyeAPI ile zenginleştirmek.

**Dosyalar:**
- `app/Http/Controllers/Api/LocationController.php` (YENİ)
- `resources/views/admin/ilanlar/components/location-map.blade.php` (GÜNCELLE)
- `routes/api.php` (GÜNCELLE)

**İşlemler:**
1. LocationController oluştur (TurkiyeAPI entegrasyonu)
2. Frontend'i güncelle (metadata gösterimi)
3. Dual format response (Context7 + legacy)

**Süre:** 2-3 gün

---

## 🎯 **FAZ 3: İLAN DETAY SAYFASI** (1 Gün)

### **Hedef:**
İlan detay sayfasında demografik widget göstermek.

**Dosyalar:**
- `resources/views/components/ilan/demographic-info.blade.php` (YENİ)
- `resources/views/admin/ilanlar/show.blade.php` (GÜNCELLE)

**Gösterilecek:**
- İl/İlçe nüfusu
- Nüfus yoğunluğu
- Bölge
- Rakım
- Kıyı ili / Büyükşehir badges
- Yatırım potansiyeli skoru (100/100)

**Süre:** 1 gün

---

## 🎯 **FAZ 4: AI PROMPT ZENGİNLEŞTİRME** (1 Gün)

### **Hedef:**
AI'ya demografik veri ekleyerek içerik kalitesini artırmak.

**Dosyalar:**
- `app/Services/AIService.php` (GÜNCELLE)

**İyileştirme:**
```diff
- Basit: "Muğla, Bodrum'da villa"
+ Zengin: "Ege Bölgesi'nin incisi Muğla'nın (1M nüfus) 
          198K nüfuslu Bodrum ilçesinde..."
```

**Süre:** 1 gün

---

## 🎯 **FAZ 5: DASHBOARD İSTATİSTİKLERİ** (1-2 Gün)

### **Hedef:**
Dashboard'a demografik analiz widgetları eklemek.

**Widgets:**
1. Büyükşehir İlanları (30 şehir)
2. Kıyı İlleri Yazlık İstatistikleri
3. Top 10 Nüfuslu Şehir
4. Büyükşehir vs Normal İl Fiyat Karşılaştırması

**Süre:** 1-2 gün

---

## 🎯 **FAZ 6: GELİŞMİŞ FİLTRELEME** (1 Gün)

### **Hedef:**
İlan arama'ya demografik filtreler eklemek.

**Filtreler:**
- Bölge (7 coğrafi bölge)
- Sadece Kıyı İlleri
- Sadece Büyükşehirler
- Nüfus aralığı

**Süre:** 1 gün

---

## 📊 **TOPLAM ZAMAN ÇİZELGESİ**

```yaml
Gün 1-2:   FAZ 1 - Service + Cache ⬅️ ŞİMDİ
Gün 3-5:   FAZ 2 - Location Cascade
Gün 6:     FAZ 3 - İlan Detay Widget
Gün 7:     FAZ 4 - AI Enhancement
Gün 8-9:   FAZ 5 - Dashboard
Gün 10:    FAZ 6 - Filtreleme

TOPLAM: 10 İŞ GÜNÜ (2 HAFTA)
```

---

## 🎯 **İLK HEDEF: FAZ 1 (Bugün!)**

```bash
1. TurkiyeAPIService.php oluştur (2-3 saat)
2. Service Provider'a kaydet (10 dk)
3. Cache warming command (30 dk)
4. Test endpoints (30 dk)
5. Test et (30 dk)

TOPLAM: 4-5 SAAT
```

---

**🚀 BAŞLAYALIM MI?** "Başla" dersen hemen TurkiyeAPIService.php'yi oluşturuyorum! 🎯

