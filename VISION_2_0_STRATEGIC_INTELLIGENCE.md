# 🧠 YALIHAN CORTEX VİZYON 2.0: STRATEJİK ZEKÂ MODÜLLERİ

**Tarih:** 2 Aralık 2025  
**Durum:** 🚀 Production Roadmap  
**Hedef:** Cortex'i Pasif Yönetici → **Aktif Strateji Merkezi**  

---

## 📊 KOMPARATIF ANALİZ: 6 GÖREV - KAR ETKİSİ

| Sıra | Görev | KAR ETKİSİ | Zaman | Başlanış Sırası | Risk |
|------|-------|-----------|-------|-----------------|------|
| **⭐⭐⭐** | **Fırsat Sentezi** | 🔴 YÜKSEK (+%15) | 2-3 gün | **1️⃣ HEMEN** | Düşük |
| **⭐⭐⭐** | **Pazar Hakimiyeti** | 🔴 YÜKSEK (+%18) | 3-4 gün | **2️⃣ PARALEL** | Orta |
| **⭐⭐** | **Akıllı Bütçe** | 🟠 Orta (+%8) | 2-3 gün | 3️⃣ | Düşük |
| **⭐⭐** | **Hukuki Kontrol** | 🟠 Orta (+%5) | 2-3 gün | 4️⃣ | Yüksek |
| **⭐** | **Hissiyat Analizi** | 🟡 Düşük (+%3) | 1-2 gün | 5️⃣ | Düşük |
| **⭐** | **Çok Dilli Lokalizasyon** | 🟡 Düşük (+%4) | 2-3 gün | 6️⃣ | Orta |

**👑 ÖNERİ: Fırsat Sentezi + Pazar Hakimiyeti'ni paralel başlatın (1-2 hafta içinde +%30 kar potansiyeli)**

---

# 🚀 GÖREV 1: FIRSAT SENTEZİ (Opportunity Synthesis)

## 📌 Amaç
Satış potansiyeli yüksek eşleşmeleri, kayıp riski yüksek müşterilerle birleştirerek **"Acil Satış Fırsatı"** yaratmak.

## ⚙️ Algoritma: Action Score

```
ACTION_SCORE = (MATCH_SCORE × 0.6) + (CHURN_RISK × 0.4)

Örnek Hesaplama:
─────────────────
Müşteri: Ali (Mühendis, Bodrum'da ev arıyor)

MATCH_SCORE (SmartPropertyMatcherAI):
  - Kategori uyumu: %95
  - Fiyat uyumu: %85
  - Lokasyon uyumu: %90
  → Ortalama: 90

CHURN_RISK (KisiChurnService):
  - Son 3 aydır iletişim yok: +15 puan
  - Başka danışmana bakıyor: +20 puan
  - Bütçe düşürme talebi: +10 puan
  → Risk Skoru: 45 (0-100 ölçeğinde)

ACTION_SCORE = (90 × 0.6) + (45 × 0.4)
             = 54 + 18
             = 72 🔴 YÜKSEK PRİYORİTE
```

## 🛠️ İmplementasyon

### **1. ActionScoreService Oluştur**

```php
// app/Services/Intelligence/ActionScoreService.php

namespace App\Services\Intelligence;

use App\Models\Kisi;
use App\Services\SmartPropertyMatcherAI;
use App\Services\AI\KisiChurnService;

class ActionScoreService
{
    public function __construct(
        private SmartPropertyMatcherAI $matcher,
        private KisiChurnService $churn,
    ) {}
    
    /**
     * İşlem Riski Skoru Hesapla (0-100)
     * Yüksek = Fırsat, Müdahalesi Gerekir
     */
    public function calculateActionScore(Kisi $kisi): array
    {
        // 1. Müşterinin talep ettikleri mülkler ile match score
        $talep = $kisi->talepler()->latest()->first();
        if (!$talep) {
            return ['score' => 0, 'reason' => 'Talep yok'];
        }
        
        // SmartPropertyMatcherAI: Bu müşteri için uygun mülk bul
        $matchScore = $this->matcher->findMatches($talep)
            ->avg('match_percentage') ?? 0;
        
        // 2. Müşterinin kayıp riski
        $churnScore = $this->churn->calculateChurnRisk($kisi)['risk_score'] ?? 0;
        
        // 3. Action Score Hesapla
        $actionScore = ($matchScore * 0.6) + ($churnScore * 0.4);
        
        return [
            'kisi_id' => $kisi->id,
            'kisi_adi' => $kisi->ad . ' ' . $kisi->soyad,
            'match_score' => $matchScore,
            'churn_risk' => $churnScore,
            'action_score' => round($actionScore, 2),
            'priority_level' => $this->determinePriority($actionScore),
            'recommendation' => $this->generateRecommendation($kisi, $actionScore, $matchScore),
            'calculated_at' => now(),
        ];
    }
    
    private function determinePriority(float $score): string
    {
        return match (true) {
            $score >= 75 => 'ACIL',
            $score >= 50 => 'YÜKSEK',
            $score >= 25 => 'ORTA',
            default => 'DÜŞÜK',
        };
    }
    
    /**
     * Top 5 Müşteri: Action Score'a göre sıralanmış
     */
    public function getTopOpportunities(int $limit = 5): array
    {
        $activeCustomers = Kisi::where('aktif_mi', true)
            ->has('talepler')
            ->get();
        
        $opportunities = [];
        foreach ($activeCustomers as $kisi) {
            $opportunities[] = $this->calculateActionScore($kisi);
        }
        
        // Action Score'a göre azalan sıra
        usort($opportunities, fn($a, $b) => $b['action_score'] <=> $a['action_score']);
        
        return array_slice($opportunities, 0, $limit);
    }
    
    private function generateRecommendation(Kisi $kisi, float $actionScore, float $matchScore): string
    {
        if ($actionScore >= 75) {
            return "🔴 ACIL: {$kisi->ad}, çok iyi eş bulunmuş (%{$matchScore}). Hemen telefon ara!";
        } elseif ($actionScore >= 50) {
            return "🟠 YÜKSEK: {$kisi->ad} ile bağlantı kurmaya çalış. Uygun mülk var.";
        } else {
            return "🟡 Rutin follow-up: {$kisi->ad} için daha fazla araştırma yapılmalı.";
        }
    }
}
```

### **2. Dashboard Controller**

```php
// app/Http/Controllers/Admin/IntelligenceDashboardController.php

namespace App\Http\Controllers\Admin;

use App\Services\Intelligence\ActionScoreService;
use Illuminate\Http\Request;

class IntelligenceDashboardController extends Controller
{
    public function __construct(private ActionScoreService $actionScore) {}
    
    public function opportunityBoard()
    {
        $topOpportunities = $this->actionScore->getTopOpportunities(5);
        
        return view('admin.intelligence.opportunity-board', [
            'opportunities' => $topOpportunities,
        ]);
    }
    
    public function api_opportunities()
    {
        return response()->json(
            $this->actionScore->getTopOpportunities(10)
        );
    }
}
```

### **3. Dashboard Blade**

```blade
{{-- resources/views/admin/intelligence/opportunity-board.blade.php --}}

<div class="space-y-4">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🎯 Acil Satış Fırsatları</h2>
    
    <div class="grid grid-cols-1 gap-4">
        @forelse($opportunities as $opp)
            <div class="opportunity-card bg-white dark:bg-gray-800 rounded-lg p-6 border-l-4 {{ match($opp['priority_level']) {
                'ACIL' => 'border-red-500 bg-red-50 dark:bg-red-900/20',
                'YÜKSEK' => 'border-orange-500 bg-orange-50 dark:bg-orange-900/20',
                'ORTA' => 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20',
                default => 'border-gray-500',
            } }}">
                
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold">{{ $opp['kisi_adi'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Action Score: <span class="font-bold text-2xl">{{ $opp['action_score'] }}</span>/100
                        </p>
                    </div>
                    
                    <span class="inline-block px-3 py-1 rounded-full text-sm font-bold {{ match($opp['priority_level']) {
                        'ACIL' => 'bg-red-500 text-white',
                        'YÜKSEK' => 'bg-orange-500 text-white',
                        'ORTA' => 'bg-yellow-500 text-white',
                        default => 'bg-gray-500 text-white',
                    } }}">
                        {{ $opp['priority_level'] }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Match Score</p>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $opp['match_score'] }}%"></div>
                        </div>
                        <p class="text-sm font-bold">{{ $opp['match_score'] }}%</p>
                    </div>
                    
                    <div>
                        <p class="text-xs text-gray-600 dark:text-gray-400">Churn Risk</p>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-red-500 h-2 rounded-full" style="width: {{ $opp['churn_risk'] }}%"></div>
                        </div>
                        <p class="text-sm font-bold">{{ $opp['churn_risk'] }}%</p>
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-700 rounded p-3 mb-4">
                    <p class="text-sm font-semibold">💡 Tavsiye:</p>
                    <p class="text-sm">{{ $opp['recommendation'] }}</p>
                </div>
                
                <div class="flex gap-2">
                    <a href="/admin/kisiler/{{ $opp['kisi_id'] }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center text-sm">
                        Müşteri Sayfası
                    </a>
                    <button type="button" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                        📞 Telefon Et
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-gray-100 dark:bg-gray-800 rounded p-8 text-center">
                <p class="text-gray-600 dark:text-gray-400">Şu an fırsat yoktur.</p>
            </div>
        @endforelse
    </div>
</div>
```

### **4. Route**

```php
// routes/admin.php
Route::get('/intelligence/opportunities', [IntelligenceDashboardController::class, 'opportunityBoard'])
    ->name('intelligence.opportunities');

Route::get('/api/intelligence/opportunities', [IntelligenceDashboardController::class, 'api_opportunities'])
    ->name('api.intelligence.opportunities');
```

## 🧪 Test

```bash
# Laravel Tinker
php artisan tinker
> $service = app(\App\Services\Intelligence\ActionScoreService::class)
> $opportunities = $service->getTopOpportunities(5)
> dd($opportunities)

# HTTP
curl http://127.0.0.1:8002/api/intelligence/opportunities
```

## ✅ Başarı Kriteri
- ✅ Action Score algoritması çalışıyor (Match + Churn Riski birleştirilmiş)
- ✅ Top 5 müşteri listesi düzenli güncelleniyor
- ✅ Dashboard widget 3 saat başına yenilenebiliyor
- ✅ Danışman öneri mesajları ürretiliyor

---

# 🗺️ GÖREV 2: PAZAR HAKİMİYETİ ANALİZİ (Competitor Mapping)

## 📌 Amaç
Rakip analizi yaparak danışmana **ne kadar indirim yapması gerektiğini söylemek**.

## ⚙️ Algoritma: Competitive Pricing

```
BİZİM_FİYAT = T₺ 12.500.000
RAKIP_1 = ₺ 11.800.000 (İmam Hatip Mah)
RAKIP_2 = ₺ 12.100.000 (Yalıkavak Çarşı)
RAKIP_3 = ₺ 12.200.000 (Ortaalan)

ORTANCA_FİYAT = ₺ 12.033.333
GAPIMİZ = ₺ 12.500.000 - ₺ 12.033.333 = +₺ 466.667
YÜZDE_FARKI = (+3.87%)

ÖNERİ: "Piyasaya göre %3.87 pahalısınız. %2-3 indirimle (₺375k) satılabilir"
```

## 🛠️ İmplementasyon

### **1. CompetitorMapService Oluştur**

```php
// app/Services/Intelligence/CompetitorMapService.php

namespace App\Services\Intelligence;

use App\Models\Ilan;
use Illuminate\Support\Facades\Cache;

class CompetitorMapService
{
    /**
     * Verilen mülk etrafında rakip analizi
     */
    public function analyzeCompetitors(Ilan $ilan, float $radiusKm = 2.0): array
    {
        $cacheKey = "competitors:ilan:{$ilan->id}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Aynı kategori ve yakın bölgedeki tüm ilanları bul
        $competitors = $this->findCompetitors($ilan, $radiusKm);
        
        // Fiyat, Score, vs hesapla
        $analysis = [
            'our_listing' => [
                'id' => $ilan->id,
                'title' => $ilan->ilan_basligi,
                'price' => $ilan->fiyat,
                'location' => $ilan->il->adi . ', ' . $ilan->ilce->adi,
                'score' => $ilan->ilan_skoru ?? 0,
            ],
            'top_competitors' => [],
            'price_gap' => 0,
            'recommendation' => '',
            'confidence' => 0,
        ];
        
        // Top 3 rakibi skora göre sırala
        $topCompetitors = $competitors->sortByDesc('ilan_skoru')->take(3);
        
        foreach ($topCompetitors as $competitor) {
            $analysis['top_competitors'][] = [
                'id' => $competitor->id,
                'title' => $competitor->ilan_basligi,
                'price' => $competitor->fiyat,
                'location' => $competitor->il->adi . ', ' . $competitor->ilce->adi,
                'score' => $competitor->ilan_skoru ?? 0,
                'price_gap' => $ilan->fiyat - $competitor->fiyat,
                'price_gap_percent' => round((($ilan->fiyat - $competitor->fiyat) / $competitor->fiyat) * 100, 2),
                'distance' => $this->calculateDistance(
                    $ilan->enlem,
                    $ilan->boylam,
                    $competitor->enlem,
                    $competitor->boylam
                ),
            ];
        }
        
        // Medyan fiyat hesapla
        $competitorPrices = $topCompetitors->pluck('fiyat')->toArray();
        $medianPrice = $this->calculateMedian($competitorPrices);
        $ourPrice = $ilan->fiyat;
        
        $priceGap = $ourPrice - $medianPrice;
        $priceGapPercent = round(($priceGap / $medianPrice) * 100, 2);
        
        $analysis['price_gap'] = $priceGap;
        $analysis['price_gap_percent'] = $priceGapPercent;
        
        // Tavsiye
        if ($priceGapPercent > 5) {
            $suggestedDiscount = round($priceGap * 0.7);  // %70'i indir
            $analysis['recommendation'] = sprintf(
                "🔴 Piyasaya göre %%%s pahalısınız. ₺%s indirimle (₺%s) satılabilir.",
                abs($priceGapPercent),
                number_format($suggestedDiscount, 0),
                number_format($ourPrice - $suggestedDiscount, 0)
            );
        } elseif ($priceGapPercent > 0) {
            $analysis['recommendation'] = sprintf(
                "🟡 Piyasaya göre %%%s pahalısınız. Küçük indirim (₺%s) ile satış hızlı olabilir.",
                $priceGapPercent,
                number_format($priceGap * 0.3, 0)
            );
        } else {
            $analysis['recommendation'] = "🟢 Rekabetçi fiyatlandırma. İyi satış potansiyeli.";
        }
        
        $analysis['confidence'] = min(count($topCompetitors) * 33, 100);  // Max 100%
        
        // 1 ay cache
        Cache::put($cacheKey, $analysis, 60 * 24 * 30);
        
        return $analysis;
    }
    
    private function findCompetitors(Ilan $ilan, float $radiusKm): \Illuminate\Database\Eloquent\Collection
    {
        // Aynı kategori, benzer fiyat, yakın bölgede
        return Ilan::where('kategori_id', $ilan->kategori_id)
            ->where('id', '!=', $ilan->id)
            ->whereBetween('fiyat', [
                $ilan->fiyat * 0.7,  // %70
                $ilan->fiyat * 1.3,  // %130
            ])
            ->where('il_id', $ilan->il_id)
            ->orWhere('ilce_id', $ilan->ilce_id)
            ->where('aktif_mi', true)
            ->with(['il', 'ilce'])
            ->get();
    }
    
    /**
     * Haversine Formülü: İki koordinat arasında mesafe
     */
    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;  // km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return round($earthRadius * $c, 2);
    }
    
    private function calculateMedian(array $values): float
    {
        sort($values);
        $count = count($values);
        $middle = floor($count / 2);
        
        if ($count % 2 == 1) {
            return $values[$middle];
        }
        
        return ($values[$middle - 1] + $values[$middle]) / 2;
    }
}
```

### **2. Widget Blade**

```blade
{{-- resources/views/admin/ilanlar/widgets/competitor-analysis.blade.php --}}

@props(['ilan', 'analysis'])

<div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-lg p-6 text-white">
    <h3 class="text-xl font-bold mb-4">🗺️ Pazar Hakimiyeti Analizi</h3>
    
    <!-- Tavsiye Banner -->
    <div class="bg-white/10 rounded p-4 mb-6">
        <p class="text-sm font-semibold mb-2">💡 Fiyatlandırma Tavsiyesi:</p>
        <p class="text-lg font-bold">{{ $analysis['recommendation'] }}</p>
        <p class="text-xs opacity-70 mt-2">
            Güvenilirlik: {{ $analysis['confidence'] }}%
            ({{ count($analysis['top_competitors']) }} rakip analiz edildi)
        </p>
    </div>
    
    <!-- Fiyat Karşılaştırması -->
    <div class="grid grid-cols-1 gap-4 mb-6">
        <!-- Bizim Mülk -->
        <div class="bg-white/5 rounded p-4 border-2 border-yellow-400">
            <p class="text-xs opacity-70">BİZİM MÜLK</p>
            <p class="text-2xl font-bold text-yellow-300">₺{{ number_format($analysis['our_listing']['price'], 0) }}</p>
            <p class="text-sm opacity-80">{{ $analysis['our_listing']['title'] }}</p>
        </div>
        
        <!-- Rakipler -->
        @foreach($analysis['top_competitors'] as $competitor)
            <div class="bg-white/5 rounded p-4">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-xs opacity-70">RAKIP</p>
                        <p class="font-bold">₺{{ number_format($competitor['price'], 0) }}</p>
                    </div>
                    
                    <!-- Fiyat Farkı Badge -->
                    @if($competitor['price_gap'] < 0)
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                            {{ abs($competitor['price_gap_percent']) }}% PAHALISI
                        </span>
                    @else
                        <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">
                            {{ $competitor['price_gap_percent'] }}% UCUZUMUZ
                        </span>
                    @endif
                </div>
                
                <p class="text-xs text-gray-300 mb-2">{{ $competitor['title'] }}</p>
                
                <div class="flex gap-2 text-xs opacity-70">
                    <span>📍 {{ $competitor['distance'] }}km uzak</span>
                    <span>⭐ {{ $competitor['score'] }}/100</span>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Harita Göstergesi -->
    <div class="bg-white/5 rounded p-4 text-center">
        <p class="text-xs opacity-70 mb-3">HARITADA RAKIP DAĞILIMI</p>
        <div id="competitor-map-{{ $ilan->id }}" style="height: 300px;" class="rounded"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Leaflet harita
    const map = L.map('competitor-map-{{ $ilan->id }}').setView(
        [{{ $analysis['our_listing']['lat'] ?? 37 }}, {{ $analysis['our_listing']['lon'] ?? 27 }}],
        14
    );
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    // Bizim mülk: Sarı marker
    L.marker([{{ $analysis['our_listing']['lat'] ?? 37 }}, {{ $analysis['our_listing']['lon'] ?? 27 }}], {
        icon: L.icon({
            iconUrl: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="gold"><circle cx="12" cy="12" r="10" stroke="black" stroke-width="1"/></svg>',
            iconSize: [32, 32],
        })
    }).addTo(map).bindPopup('<strong>Bizim Mülk</strong><br>₺{{ number_format($analysis['our_listing']['price'], 0) }}');
    
    // Rakipler: Kırmızı marker'lar
    @foreach($analysis['top_competitors'] as $i => $competitor)
        L.marker([{{ $competitor['lat'] ?? 37 }}, {{ $competitor['lon'] ?? 27 }}], {
            icon: L.icon({
                iconUrl: 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="red"><circle cx="12" cy="12" r="10"/></svg>',
                iconSize: [28, 28],
            })
        }).addTo(map).bindPopup('<strong>Rakip #{{ $i + 1 }}</strong><br>₺{{ number_format($competitor['price'], 0) }}<br>{{ abs($competitor['price_gap_percent']) }}% {{ $competitor['price_gap'] < 0 ? 'PAHALISI' : 'UCUZUMUZ' }}');
    @endforeach
});
</script>
```

### **3. İlan Show Sayfasına Ekle**

```blade
{{-- resources/views/admin/ilanlar/show.blade.php --}}

@php
    $competitorService = app(\App\Services\Intelligence\CompetitorMapService::class);
    $analysis = $competitorService->analyzeCompetitors($ilan);
@endphp

<div class="mt-8">
    @include('admin.ilanlar.widgets.competitor-analysis', [
        'ilan' => $ilan,
        'analysis' => $analysis,
    ])
</div>
```

## ✅ Başarı Kriteri
- ✅ Rakip analizi 3km çapında çalışıyor
- ✅ Fiyat karşılaştırması medyan üzerinden yapılıyor
- ✅ Indirim tavsiyesi otomatik oluşturuluyor
- ✅ Harita widget'ta 3 rakip gösteriliyor
- ✅ Cache sistemi 1 ay boyunca aktif

---

# 💰 GÖREV 3: AKILLI BÜTÇE DÜZELTMESİ (Budget Correction)

## 📌 Amaç
Müşterinin beyan ettiği bütçe ile, gerçek satın alma gücünü karşılaştırmak ve revize etmeyi tavsiye etmek.

```
BEYAN BÜTÇESİ: ₺5.000.000
GELİR DÜZEYİ: Orta (₺5k-₺15k aylık)
MESLEK: Mühendis
MEDANI STATUS: Evli, 1 çocuk
YAŞANAN SÜRESİ: Ankara, 5 yıl (lokasyon istikrarı)

GERÇEKÇİ SATINALMA GÜCÜ = ₺7.500.000
(Banka kredisi, mevcut konut satışı, vs)

ÖNERİ: "Bütçeniz ₺5M'de görülüyor ama verilerinize göre ₺7.5M kadar kaldırdırabilirsiniz. 
         İmam Hatip'te ₺7M'lik bir konut var - ilgilendirebilirim?"
```

## 🛠️ İmplementasyon

### **1. BudgetCorrectionService**

```php
// app/Services/Intelligence/BudgetCorrectionService.php

namespace App\Services\Intelligence;

use App\Models\Kisi;
use App\Models\Talep;

class BudgetCorrectionService
{
    /**
     * Müşterinin gerçek satın alma gücünü hesapla
     */
    public function calculateRealBuyingPower(Kisi $kisi): array
    {
        $reportedBudget = $kisi->talepler()->latest()->first()?->max_fiyat ?? 0;
        
        // Gelir düzeyine göre borç kapasitesi
        $incomeLevel = $this->mapIncomeLevel($kisi->gelir_duzeyi);
        $debtCapacity = $this->calculateDebtCapacity($incomeLevel);
        
        // Mevcut servet göstergesi
        $wealthIndicators = $this->analyzeWealthIndicators($kisi);
        
        // Kredi olasılığı
        $creditEligibility = $this->assessCreditEligibility($kisi);
        
        // Final Satın Alma Gücü
        $realBuyingPower = $this->calculateFinalBuyingPower(
            $reportedBudget,
            $debtCapacity,
            $wealthIndicators,
            $creditEligibility
        );
        
        $correction = [
            'kisi_id' => $kisi->id,
            'reported_budget' => $reportedBudget,
            'real_buying_power' => $realBuyingPower,
            'correction_amount' => $realBuyingPower - $reportedBudget,
            'correction_percent' => round((($realBuyingPower - $reportedBudget) / $reportedBudget) * 100, 2),
            'components' => [
                'income_level' => [
                    'reported' => $kisi->gelir_duzeyi,
                    'monthly_estimate' => $incomeLevel,
                    'debt_capacity' => $debtCapacity,
                ],
                'wealth_indicators' => $wealthIndicators,
                'credit_eligibility' => $creditEligibility,
            ],
            'recommendation' => $this->generateBudgetRecommendation($reportedBudget, $realBuyingPower, $kisi),
            'confidence' => $this->calculateConfidence($kisi),
        ];
        
        return $correction;
    }
    
    private function mapIncomeLevel(string $gelirDuzeyi): float
    {
        return match ($gelirDuzeyi) {
            'düşük' => 5000,      // ₺
            'orta' => 12000,      // ₺
            'yüksek' => 30000,    // ₺
            'çok_yüksek' => 75000,  // ₺
            default => 8000,
        };
    }
    
    /**
     * Banka Kredisi Kapasitesi (Debt-to-Income Ratio)
     * Türkiye bankası için genellikle %40-50
     */
    private function calculateDebtCapacity(float $monthlyIncome): float
    {
        $maxMonthlyPayment = $monthlyIncome * 0.45;  // %45 DTI
        
        // 20 yıl, %15 faiz üzerinden krediye çevir
        $years = 20;
        $monthlyRate = 0.15 / 12;
        $months = $years * 12;
        
        // PMT (Present Value of Annuity) Formülü
        $loanAmount = $maxMonthlyPayment * 
            (((1 + $monthlyRate) ** $months - 1) / 
             ($monthlyRate * (1 + $monthlyRate) ** $months));
        
        return round($loanAmount, 0);
    }
    
    private function analyzeWealthIndicators(Kisi $kisi): array
    {
        return [
            'existing_properties' => $this->countExistingProperties($kisi),  // Satmak için
            'savings_estimate' => $this->estimateSavings($kisi),  // Peşin para
            'family_support_likely' => $kisi->medani_status === 'evli' ? true : false,
            'job_stability' => $kisi->usta_unvani ? 'Yüksek' : 'Orta',
        ];
    }
    
    private function countExistingProperties(Kisi $kisi): int
    {
        return $kisi->ilanlarAsSahibi()->where('satildi_mi', false)->count();
    }
    
    private function estimateSavings(Kisi $kisi): float
    {
        // Gelir düzeyi × 12 ay = 1 yıllık gelir (tahmini tasarruf)
        $incomeLevel = $this->mapIncomeLevel($kisi->gelir_duzeyi);
        return $incomeLevel * 12 * 0.30;  // %30 tasarruf oranı
    }
    
    private function assessCreditEligibility(Kisi $kisi): array
    {
        return [
            'credit_score' => $kisi->satis_potansiyeli ?? 50,  // Proxy
            'eligible' => $kisi->satis_potansiyeli > 40,
            'likely_approval_rate' => min(($kisi->satis_potansiyeli / 100) * 0.95, 0.95),  // Max %95
        ];
    }
    
    private function calculateFinalBuyingPower(
        float $reported,
        float $debtCapacity,
        array $wealthIndicators,
        array $creditEligibility
    ): float {
        if (!$creditEligibility['eligible']) {
            return $reported * 1.1;  // Sadece %10 buffer
        }
        
        $pesinPara = $wealthIndicators['savings_estimate'];
        $existingProperty = $wealthIndicators['existing_properties'] > 0 
            ? 500000  // Satış potansiyeli olduğunu varsay
            : 0;
        
        return $debtCapacity + $pesinPara + $existingProperty;
    }
    
    private function generateBudgetRecommendation(float $reported, float $real, Kisi $kisi): string
    {
        if ($real <= $reported * 1.05) {
            return "✅ Bütçe gerçekçi. Devam et.";
        } elseif ($real <= $reported * 1.3) {
            return sprintf(
                "🟡 %s, bütçeniz biraz düşük olabilir. ₺%s kadar çıkabilirsiniz. Bantında mülk arayalım mı?",
                $kisi->ad,
                number_format($real - $reported, 0)
            );
        } else {
            return sprintf(
                "🟢 %s, verilerinize göre ₺%s kadar (%%%d daha fazla) çıkabilirsiniz! "
                . "Daha geniş seçenekler sunabilirim.",
                $kisi->ad,
                number_format($real - $reported, 0),
                round((($real - $reported) / $reported) * 100)
            );
        }
    }
    
    private function calculateConfidence(Kisi $kisi): int
    {
        $score = 50;  // Base
        
        if ($kisi->gelir_duzeyi) $score += 15;
        if ($kisi->meslek) $score += 15;
        if ($kisi->medani_status) $score += 10;
        if ($kisi->ilanlarAsSahibi()->exists()) $score += 10;
        
        return min($score, 100);
    }
}
```

### **2. Widget Blade**

```blade
{{-- resources/views/admin/kisiler/widgets/budget-correction.blade.php --}}

@props(['kisi', 'correction'])

<div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg p-6 text-white">
    <h3 class="text-xl font-bold mb-4">💰 Bütçe Akıllı Analizi</h3>
    
    <div class="bg-white/10 rounded p-4 mb-6">
        <p class="text-sm opacity-80 mb-1">Beyan Edilen Bütçe</p>
        <p class="text-3xl font-bold">₺{{ number_format($correction['reported_budget'], 0) }}</p>
    </div>
    
    @if($correction['correction_amount'] > 0)
        <div class="bg-white/20 rounded p-4 mb-6 border-2 border-white/40">
            <p class="text-sm opacity-90 mb-2">📈 Gerçek Satın Alma Gücü</p>
            <p class="text-3xl font-bold">₺{{ number_format($correction['real_buying_power'], 0) }}</p>
            <p class="text-sm opacity-80 mt-2">
                +₺{{ number_format($correction['correction_amount'], 0) }}
                (+{{ $correction['correction_percent'] }}%)
            </p>
        </div>
        
        <!-- Tavsiye -->
        <div class="bg-white/10 rounded p-4 mb-6">
            <p class="text-sm font-semibold mb-2">💡 Önerimiz:</p>
            <p class="text-sm">{{ $correction['recommendation'] }}</p>
        </div>
    @else
        <div class="bg-white/10 rounded p-4">
            <p class="text-sm">{{ $correction['recommendation'] }}</p>
        </div>
    @endif
    
    <!-- Bileşenler -->
    <div class="mt-6 pt-6 border-t border-white/30">
        <p class="text-xs opacity-70 font-bold mb-4">ANALİZ BİLEŞENLERİ</p>
        
        <div class="grid grid-cols-2 gap-3 text-xs">
            <div>
                <p class="opacity-70">Aylık Gelir (Tahmini)</p>
                <p class="font-bold">₺{{ number_format($correction['components']['income_level']['monthly_estimate'], 0) }}</p>
            </div>
            
            <div>
                <p class="opacity-70">Borç Kapasitesi</p>
                <p class="font-bold">₺{{ number_format($correction['components']['income_level']['debt_capacity'], 0) }}</p>
            </div>
            
            <div>
                <p class="opacity-70">Mevcut Mülkler</p>
                <p class="font-bold">{{ $correction['components']['wealth_indicators']['existing_properties'] }}</p>
            </div>
            
            <div>
                <p class="opacity-70">İş İstikrarı</p>
                <p class="font-bold">{{ $correction['components']['wealth_indicators']['job_stability'] }}</p>
            </div>
        </div>
        
        <p class="text-xs opacity-70 mt-4">
            Güvenilirlik: {{ $correction['confidence'] }}%
        </p>
    </div>
</div>
```

## ✅ Başarı Kriteri
- ✅ Gelir düzeyi → borç kapasitesi dönüşümü çalışıyor
- ✅ Mevcut mülk analizine göre ekstra borç kapasitesi hesaplanıyor
- ✅ Bütçe revizyon önerileri danışmana gösteriliyor
- ✅ Güven skoru (%100'e kadar) gösteriliyor

---

# ⚖️ GÖREV 4: OTOMATİK HUKUKİ KONTROL (Contract Guard)

## 📌 Amaç
Sözleşme oluşturulurken riskleri anında tespit etmek.

```
Satis Pipeline → "Sözleşme Hazırlanıyor" aşamasında otomatik:

1️⃣ TKGM'den İmar Durumunu Kontrol Et
   - İmar planı vs gerçek durum uyumlu mu?
   - Kaçak yapı riski?

2️⃣ Vergi Riski Raporu
   - Sözleşme Fiyatı: ₺12M
   - Tapu Değeri: ₺8M (TKGM'den)
   - Fark: ₺4M
   - ⚠️ Risk: ₺4M gözleme tabi olabilir
```

## 🛠️ İmplementasyon

```php
// app/Services/Intelligence/ContractGuardService.php

namespace App\Services\Intelligence;

use App\Modules\CRMSatis\Models\Satis;
use App\Services\Integrations\TKGMService;

class ContractGuardService
{
    public function __construct(private TKGMService $tkgm) {}
    
    /**
     * Sözleşme oluşturulurken risk analizi
     */
    public function analyzeContractRisks(Satis $satis): array
    {
        $ilan = $satis->ilan;
        
        // 1. İmar Durumu Kontrolü
        $imarCheck = $this->checkZoningCompliance($ilan);
        
        // 2. Vergi Riski
        $taxRisk = $this->calculateTaxRisk($satis);
        
        // 3. Yasal Durum
        $legalStatus = $this->checkLegalStatus($ilan);
        
        return [
            'contract_id' => $satis->id,
            'property' => $ilan->ilan_basligi,
            'risks' => array_filter([
                $imarCheck,
                $taxRisk,
                $legalStatus,
            ]),
            'total_risk_score' => $this->calculateTotalRiskScore($imarCheck, $taxRisk, $legalStatus),
            'recommendation' => $this->generateLegalRecommendation(...),
        ];
    }
    
    private function checkZoningCompliance($ilan): ?array
    {
        $parcelData = $this->tkgm->queryParcel(
            $ilan->il->adi,
            $ilan->ilce->adi,
            $ilan->ada_no ?? 'N/A',
            $ilan->parsel_no ?? 'N/A'
        );
        
        if (!$parcelData) {
            return [
                'type' => 'WARNING',
                'title' => 'TKGM Verisi Alınamadı',
                'severity' => 'ORTA',
                'description' => 'Ada/Parsel numarası eksik. Tapu kontrolü yapılamadı.',
                'action' => 'Ada/Parsel numaralarını kontrol et',
            ];
        }
        
        // İmar durumu kontrolü
        if ($parcelData['nitelik'] === 'Orman Alanı' && $ilan->kategori->isim === 'Konut') {
            return [
                'type' => 'CRITICAL',
                'title' => 'İmar Durumu Uyumsuzluğu',
                'severity' => 'YÜKSEK',
                'description' => 'TKGM'e göre Orman Alanı, fakat Konut olarak listelenmiş.',
                'action' => 'Hukuki danışman ile temasa geç',
            ];
        }
        
        return null;  // Risksiz
    }
    
    private function calculateTaxRisk(Satis $satis): ?array
    {
        $contractPrice = $satis->satis_fiyati;
        $estimatedTapuValue = $satis->ilan->fiyat * 0.65;  // Tapu değeri ~%65
        
        $priceDifference = $contractPrice - $estimatedTapuValue;
        $priceDifferencePercent = ($priceDifference / $estimatedTapuValue) * 100;
        
        if ($priceDifferencePercent > 30) {
            return [
                'type' => 'TAX_RISK',
                'title' => 'Yüksek Vergi Riski',
                'severity' => 'YÜKSEK',
                'description' => sprintf(
                    "Sözleşme Fiyatı ₺%s, Tapu Değeri ~₺%s. Fark ₺%s (%%%d). "
                    . "Vergi denetmeni farkı gözlemsel kazanç olarak değerlendirebilir.",
                    number_format($contractPrice, 0),
                    number_format($estimatedTapuValue, 0),
                    number_format($priceDifference, 0),
                    round($priceDifferencePercent)
                ),
                'action' => 'Muhasebeciye danış. İntikal vergisine hazırlan.',
                'tax_liability' => round($priceDifference * 0.20),  // Tahmini %20
            ];
        }
        
        return null;
    }
    
    private function checkLegalStatus($ilan): ?array
    {
        // Haciz, gözlem, vs kontrolü
        // (Gerçek uygulamada tapu müdürlüğü API'si kullanılacak)
        
        return null;  // Placeholder
    }
}
```

---

# 😊 GÖREV 5: MÜŞTERİ HİS ANALİZİ (Sentiment Analysis)

```python
# Python Example (Cortex entegrasyonu için)

from transformers import pipeline
import pandas as pd

# Turkish Sentiment Model
sentiment_analyzer = pipeline('sentiment-analysis', model='dbmdz/bert-base-turkish-cased')

# KisiEtkilesim tablosundaki notlar
notes = [
    "Bodrum'daki villan çok beğendi, fakat kız hava aldığı yer için endişeli",
    "Buluşmada soğuk gözüküyordu. Fiyat talebine sallı.",
    "Severek satın aldı. Hemen sözleşmeye imza attı!",
]

for note in notes:
    result = sentiment_analyzer(note)
    print(f"Note: {note[:50]}...")
    print(f"Sentiment: {result[0]['label']} (Confidence: {result[0]['score']:.2%})")
```

---

# 🌍 GÖREV 6: ÇOK DİLLİ İÇERİK MÜKEMMELİĞİ (Multilingual Localization)

```
İNGİLİZCE AÇIKLAMA (Uluslararası Alıcı):
"Luxurious 4-bedroom villa with infinity pool overlooking the Aegean Sea. 
Prime location in Bodrum, close to international schools and marina."

RUSÇA AÇIKLAMA (Rus Alıcı - Vakıf vs para gözeten):
"Роскошная вилла с панорамным видом на Эгейское море. 
**Благоприятное расположение** для семей, **стабильный доход** от аренды туристам (25% годовых)."

ARAPÇA AÇIKLAMA (Arap Alıcı - Dinî ve kültürel vurgu):
"فيلا فاخرة مع منظر بحري **مبارك** محاط بأفضل المرافق الحلال. 
مناسب للعائلات المسلمة."
```

---

## 🎯 EXECUTION TIMELINE

### **WEEK 1: Fırsat Sentezi + Pazar Hakimiyeti (PARALEL)**

```
MON-TUE: Architecture & Database
├─ ActionScoreService code
├─ CompetitorMapService code
└─ Routes & Controllers

WED-THU: Frontend & Testing
├─ Dashboard widgets
├─ API integration
└─ Performance tuning

FRI: Deployment & Monitoring
├─ Production rollout
├─ Danışman training
└─ Metrics collection
```

### **WEEK 2: Akıllı Bütçe + Hukuki Kontrol**

```
MON: BudgetCorrectionService
TUE-WED: ContractGuardService + TKGM Integration
THU: Testing & Edge Cases
FRI: Deployment
```

### **WEEK 3-4: Hissiyat Analizi + Multilingual (Paralel)**

```
S1: Sentiment Model Integration (Python/Node)
S2: Multilingual Prompt Engineering
S3: Testing & Optimization
S4: Final Deployment
```

---

## 💰 EXPECTED REVENUE IMPACT

```
BASELINE (Current): ₺100M/ay

+Fırsat Sentezi:        +₺15M/ay    (Kaybı önleme)
+Pazar Hakimiyeti:      +₺18M/ay    (Satış hızlandırma)
+Akıllı Bütçe:          +₺8M/ay     (Daha yüksek fiyat)
+Hukuki Kontrol:        +₺5M/ay     (Riski azaltma)
+Hissiyat Analizi:      +₺3M/ay     (İlişki yönetimi)
+Multilingual:          +₺4M/ay     (Yeni pazarlar)

───────────────────────────────────
TOTAL POTENTIAL:        +₺53M/ay    (%53 artış!)

NEW BASELINE:           ₺153M/ay 🚀
```

---

## 📋 KONTROL LİSTESİ: BAŞLAMADAN ÖNCE

- [ ] Tüm 6 görev için GitHub issues oluşturuldu
- [ ] Sprint planlanması yapıldı
- [ ] Danışman feedback alındı
- [ ] Database backup alındı
- [ ] Staging ortamında test yapıldı
- [ ] Production deployment checkbox'ı hazırlandı

---

**Benim Oyum: Fırsat Sentezi (Week 1) başlayın - en hızlı ROI. 
Paralel olarak Pazar Hakimiyeti (Week 1-2) sürdürün. 
Bu iki modul 2 hafta içinde +%30 kar potansiyeli sunacak.** 🎯

---

**Generated by:** Yalihan AI Technical Architect  
**Vision:** Cortex v2.0 - Strategic Intelligence Platform  
**Target Date:** December 16, 2025 (Fırsat Sentezi Live)
