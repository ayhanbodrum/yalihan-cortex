# 🔗 EmlakPro Entegrasyonlar - Konsolide Dokümantasyon

**Son Güncelleme:** 25 Kasım 2025  
**Context7 Standardı:** C7-INTEGRATIONS-KONSOLIDE-2025-11-25  
**Entegrasyon Sayısı:** 8 Ana Entegrasyon Modülü

---

## 📋 İÇİNDEKİLER

1. [TCMB Kur API](#tcmb)
2. [N8N AI Entegrasyonları](#n8n-ai)
3. [N8N Otomasyon](#n8n)
4. [Maps Entegrasyonları](#maps)
5. [TKGM API](#tkgm)
6. [Context7 Dual System](#context7)
7. [MCP Servers](#mcp)
8. [AI Sağlayıcıları](#ai)

---

## 💱 TCMB KUR API {#tcmb}

### T.C. Merkez Bankası Döviz Kurları Entegrasyonu

**API Özellikleri:**

- ✅ **Real-time Exchange Rates** - Güncel TRY/USD, TRY/EUR kurları
- ✅ **Historical Data** - 30 gün geçmiş kur verileri
- ✅ **Auto-update** - Günlük otomatik kur güncellemesi
- ✅ **Cache System** - Redis cache ile performans optimizasyonu
- ✅ **Fallback API** - TCMB down olduğunda alternatif kaynak

### API Implementasyonu

#### Service Class

```php
<?php

namespace App\Services\ExchangeRate;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class TCMBExchangeRateService
{
    const API_URL = 'https://evds2.tcmb.gov.tr/service/evds/';
    const FALLBACK_API = 'https://api.exchangerate-api.com/v4/latest/TRY';

    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.tcmb.api_key');
    }

    /**
     * Get current exchange rates
     */
    public function getCurrentRates(): array
    {
        $cacheKey = 'tcmb_exchange_rates_' . Carbon::today()->format('Y-m-d');

        return Cache::remember($cacheKey, 3600, function() {
            return $this->fetchFromTCMB() ?? $this->fetchFromFallback();
        });
    }

    /**
     * Get historical rates for property pricing
     */
    public function getHistoricalRates($days = 30): array
    {
        $startDate = Carbon::today()->subDays($days)->format('d-m-Y');
        $endDate = Carbon::today()->format('d-m-Y');

        $response = Http::get($this->API_URL . 'series=TP.DK.USD.S.YTL', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'type' => 'json',
            'key' => $this->apiKey
        ]);

        if ($response->successful()) {
            return $this->parseHistoricalData($response->json());
        }

        return [];
    }

    /**
     * Convert property prices to different currencies
     */
    public function convertPrice($amount, $fromCurrency, $toCurrency): float
    {
        if ($fromCurrency === $toCurrency) return $amount;

        $rates = $this->getCurrentRates();

        // Convert to TRY first
        $tryAmount = $fromCurrency === 'TRY' ? $amount : $amount * $rates[$fromCurrency];

        // Convert from TRY to target currency
        return $toCurrency === 'TRY' ? $tryAmount : $tryAmount / $rates[$toCurrency];
    }

    private function fetchFromTCMB(): ?array
    {
        try {
            $response = Http::timeout(10)->get($this->API_URL . 'series=TP.DK.USD.S.YTL-TP.DK.EUR.S.YTL', [
                'startDate' => Carbon::today()->format('d-m-Y'),
                'endDate' => Carbon::today()->format('d-m-Y'),
                'type' => 'json',
                'key' => $this->apiKey
            ]);

            if ($response->successful()) {
                return $this->parseRateData($response->json());
            }
        } catch (\Exception $e) {
            \Log::error('TCMB API Error: ' . $e->getMessage());
        }

        return null;
    }

    private function fetchFromFallback(): array
    {
        try {
            $response = Http::timeout(10)->get(self::FALLBACK_API);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'USD' => 1 / $data['rates']['USD'],
                    'EUR' => 1 / $data['rates']['EUR'],
                    'updated_at' => Carbon::now()
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Fallback Exchange API Error: ' . $e->getMessage());
        }

        // Emergency fallback - manual rates
        return [
            'USD' => 33.50,
            'EUR' => 36.80,
            'updated_at' => Carbon::now(),
            'is_fallback' => true
        ];
    }
}
```

#### Scheduled Updates

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Her gün 09:00'da kur güncellemesi
    $schedule->call(function () {
        app(TCMBExchangeRateService::class)->updateDailyRates();
    })->dailyAt('09:00');

    // Her 4 saatte bir kur kontrolü
    $schedule->call(function () {
        app(TCMBExchangeRateService::class)->updateRatesIfNeeded();
    })->everyFourHours();
}
```

---

## 🤖 N8N AI ENTEGRASYONLARI {#n8n-ai}

### AI-Powered Workflow Automation

**Senaryolar:**

- ✅ **Smart Lead Processing** - Gelen taleplerin AI ile sınıflandırılması
- ✅ **Auto Property Description** - İlan fotoğraflarından açıklama oluşturma
- ✅ **Price Suggestion AI** - Benzer ilanlar analizi ile fiyat önerisi
- ✅ **Smart Matching** - Müşteri-ilan eşleştirme algoritması
- ✅ **Automated Follow-up** - AI destekli takip e-postaları

### AI Workflow Senaryoları

#### 1. Smart Lead Processing

```json
{
    "name": "Smart Lead Processing",
    "description": "Gelen talepleri AI ile analiz edip otomatik sınıflandırma",
    "trigger": {
        "type": "webhook",
        "endpoint": "/api/webhook/new-lead"
    },
    "steps": [
        {
            "name": "AI Analysis",
            "type": "openai-gpt4",
            "prompt": "Bu müşteri talebini analiz et ve kategorize et: budget, urgency, property_type, location_preference",
            "input": "{{$json.customer_message}}"
        },
        {
            "name": "CRM Update",
            "type": "http-request",
            "method": "POST",
            "url": "{{$env.CRM_API_ENDPOINT}}/leads/{{$json.lead_id}}/ai-analysis",
            "body": {
                "ai_score": "{{$json.ai_analysis.score}}",
                "priority": "{{$json.ai_analysis.urgency}}",
                "category": "{{$json.ai_analysis.property_type}}"
            }
        },
        {
            "name": "Auto Assignment",
            "type": "code",
            "code": "// Assign to best agent based on AI analysis\nconst bestAgent = findBestAgent(items[0].json.ai_analysis);\nreturn { agent_id: bestAgent.id };"
        }
    ]
}
```

#### 2. Auto Property Description

```json
{
    "name": "AI Property Description Generator",
    "description": "İlan fotoğraflarından AI ile açıklama oluşturma",
    "trigger": {
        "type": "manual",
        "input": ["property_id", "images"]
    },
    "steps": [
        {
            "name": "Image Analysis",
            "type": "openai-vision",
            "model": "gpt-4-vision-preview",
            "prompt": "Bu emlak fotoğraflarını analiz et ve satış açıklaması yaz. Türkçe, profesyonel ton kullan.",
            "images": "{{$json.images}}"
        },
        {
            "name": "SEO Optimization",
            "type": "openai-gpt4",
            "prompt": "Bu emlak açıklamasını SEO optimizasyonu yap. Anahtar kelimeler ekle ama doğal kalsın.",
            "input": "{{$json.description}}"
        },
        {
            "name": "Save Description",
            "type": "http-request",
            "method": "PUT",
            "url": "{{$env.API_ENDPOINT}}/properties/{{$json.property_id}}",
            "body": {
                "description": "{{$json.optimized_description}}",
                "seo_keywords": "{{$json.keywords}}"
            }
        }
    ]
}
```

#### 3. Price Suggestion AI

```javascript
// N8N Code Node for Price Suggestion
const property = $input.first().json;

// Benzer ilanları getir
const similarProperties = await $http.request({
    method: 'GET',
    url: `${$env.API_ENDPOINT}/properties/similar`,
    query: {
        location: property.location,
        type: property.type,
        size: property.size,
        age: property.age,
    },
});

// AI ile fiyat analizi
const priceAnalysis = await $http.request({
    method: 'POST',
    url: 'https://api.openai.com/v1/chat/completions',
    headers: {
        Authorization: `Bearer ${$env.OPENAI_API_KEY}`,
        'Content-Type': 'application/json',
    },
    body: {
        model: 'gpt-4',
        messages: [
            {
                role: 'system',
                content:
                    'Sen bir emlak değerleme uzmanısın. Verilen benzer ilanları analiz ederek adil fiyat önerisi yap.',
            },
            {
                role: 'user',
                content: `Bu emlak için fiyat öner:
Property: ${JSON.stringify(property)}
Similar Properties: ${JSON.stringify(similarProperties.data)}`,
            },
        ],
    },
});

return {
    suggested_price: priceAnalysis.data.choices[0].message.content,
    similar_count: similarProperties.data.length,
    confidence_score: 0.85,
};
```

---

## 🔧 N8N OTOMASYON {#n8n}

### Workflow Automation Platform

**Entegrasyon Özellikleri:**

- ✅ **Lead Management** - Otomatik talep yönlendirme
- ✅ **Email Marketing** - Segment-based campaigns
- ✅ **Data Sync** - CRM → Website sync
- ✅ **Report Generation** - Otomatik raporlama
- ✅ **Notification System** - Real-time bildirimler

### Temel Workflow'lar

#### Lead Yönlendirme Sistemi

```yaml
Workflow: Lead Auto-Assignment
Trigger: New lead webhook
Steps:
    1. Lead Analysis:
        - Budget range check
        - Location preference
        - Property type interest
        - Contact time preference

    2. Agent Matching:
        - Current workload check
        - Expertise area match
        - Performance score
        - Availability status

    3. Auto Assignment:
        - Assign to best agent
        - Send notification
        - Create follow-up task
        - Log activity

    4. Customer Communication:
        - Send welcome email
        - SMS confirmation
        - Schedule callback
```

#### Email Marketing Automation

```yaml
Workflow: Property Alert System
Trigger: New property added
Steps:
    1. Property Categorization:
        - Extract property features
        - Determine target audience
        - Set price range

    2. Customer Matching:
        - Query saved searches
        - Match preferences
        - Filter active customers

    3. Personalized Emails:
        - Generate custom subject
        - Create property highlights
        - Add call-to-action

    4. Campaign Tracking:
        - Track open rates
        - Monitor click-through
        - Measure conversions
```

### API Entegrasyon Endpoints

```php
// N8N Webhook Controllers
class N8NWebhookController extends Controller
{
    public function newLead(Request $request)
    {
        $leadData = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
            'source' => 'required|string'
        ]);

        // Create lead
        $lead = Lead::create($leadData);

        // Trigger N8N workflow
        Http::post(config('n8n.webhook_url') . '/lead-processing', [
            'lead_id' => $lead->id,
            'customer_message' => $leadData['message'],
            'source' => $leadData['source']
        ]);

        return response()->json(['success' => true, 'lead_id' => $lead->id]);
    }

    public function propertyStatusUpdate(Request $request)
    {
        $propertyId = $request->input('property_id');
        $newStatus = $request->input('status');

        $property = Property::findOrFail($propertyId);
        $property->update(['status' => $newStatus]);

        // Notify interested customers if property becomes available
        if ($newStatus === 'active') {
            Http::post(config('n8n.webhook_url') . '/property-available', [
                'property' => $property->toArray(),
                'interested_customers' => $property->interestedCustomers->pluck('id')
            ]);
        }

        return response()->json(['success' => true]);
    }
}
```

---

## 🗺️ MAPS ENTEGRASYONLARI {#maps}

### Multi-Provider Map System

**Desteklenen Sağlayıcılar:**

- ✅ **Google Maps API** - Geocoding, Places, Street View
- ✅ **OpenStreetMap** - Free alternative mapping
- ✅ **Mapbox** - Custom styling ve performance
- ✅ **TKGM Harita** - Türkiye resmi kadastro

### Google Maps API Entegrasyonu

#### Places API Integration

```javascript
// Google Places autocomplete
class GooglePlacesService {
    constructor() {
        this.autocompleteService = new google.maps.places.AutocompleteService();
        this.placesService = new google.maps.places.PlacesService(document.createElement('div'));
    }

    async searchPlaces(query) {
        return new Promise((resolve, reject) => {
            this.autocompleteService.getPlacePredictions(
                {
                    input: query,
                    componentRestrictions: { country: 'TR' },
                    types: ['address'],
                },
                (predictions, status) => {
                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                        resolve(predictions);
                    } else {
                        reject(status);
                    }
                }
            );
        });
    }

    async getPlaceDetails(placeId) {
        return new Promise((resolve, reject) => {
            this.placesService.getDetails(
                {
                    placeId: placeId,
                    fields: ['geometry', 'formatted_address', 'address_components'],
                },
                (place, status) => {
                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                        resolve(place);
                    } else {
                        reject(status);
                    }
                }
            );
        });
    }
}
```

#### Street View Integration

```php
class StreetViewService
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.google.maps_api_key');
    }

    public function getStreetViewImage($latitude, $longitude, $size = '640x400')
    {
        $baseUrl = 'https://maps.googleapis.com/maps/api/streetview';

        $params = [
            'size' => $size,
            'location' => "{$latitude},{$longitude}",
            'heading' => '0',
            'pitch' => '0',
            'fov' => '90',
            'key' => $this->apiKey
        ];

        return $baseUrl . '?' . http_build_query($params);
    }

    public function checkStreetViewAvailability($latitude, $longitude)
    {
        $metadataUrl = 'https://maps.googleapis.com/maps/api/streetview/metadata';

        $response = Http::get($metadataUrl, [
            'location' => "{$latitude},{$longitude}",
            'key' => $this->apiKey
        ]);

        $data = $response->json();

        return $data['status'] === 'OK';
    }
}
```

### Mapbox Integration

#### Custom Styling

```javascript
// Mapbox GL JS implementation
mapboxgl.accessToken = window.mapboxConfig.accessToken;

const map = new mapboxgl.Map({
    container: 'property-map',
    style: 'mapbox://styles/mapbox/streets-v11',
    center: [29.0167, 41.0122], // Istanbul center
    zoom: 12,
});

// Custom property markers
map.on('load', function () {
    // Add property markers source
    map.addSource('properties', {
        type: 'geojson',
        data: {
            type: 'FeatureCollection',
            features: window.propertyData,
        },
    });

    // Add property markers layer
    map.addLayer({
        id: 'property-markers',
        type: 'symbol',
        source: 'properties',
        layout: {
            'icon-image': 'property-icon',
            'icon-size': 1.5,
            'text-field': ['get', 'price'],
            'text-font': ['Open Sans Semibold', 'Arial Unicode MS Bold'],
            'text-offset': [0, 1.25],
            'text-anchor': 'top',
        },
    });

    // Property marker click handler
    map.on('click', 'property-markers', function (e) {
        const coordinates = e.features[0].geometry.coordinates.slice();
        const description = e.features[0].properties.description;

        new mapboxgl.Popup().setLngLat(coordinates).setHTML(description).addTo(map);
    });
});
```

---

## 🏛️ TKGM API {#tkgm}

### T.C. Tapu ve Kadastro Genel Müdürlüğü Entegrasyonu

**API Özellikleri:**

- ✅ **Parsel Sorgulama** - Ada/Parsel bilgileri
- ✅ **Kadastro Haritası** - Resmi kadastro verileri
- ✅ **Tapu Durum Sorgulama** - Mülkiyet bilgileri
- ✅ **İmar Durumu** - İmar planı kontrolü

### TKGM Web Servis Entegrasyonu

#### Parsel Bilgi Sorgulama

```php
<?php

namespace App\Services\TKGM;

use Illuminate\Support\Facades\Http;
use SoapClient;

class TKGMParselService
{
    private $soapClient;
    private $username;
    private $password;

    public function __construct()
    {
        $this->username = config('services.tkgm.username');
        $this->password = config('services.tkgm.password');

        $this->soapClient = new SoapClient(
            'https://parselsorgu.tkgm.gov.tr/ParselSorguWS/ParselSorgu.asmx?WSDL',
            ['trace' => true, 'exceptions' => true]
        );
    }

    /**
     * Ada/Parsel bilgisi sorgula
     */
    public function queryParcel($il, $ilce, $mahalle, $ada, $parsel)
    {
        try {
            $response = $this->soapClient->ParselBilgiSorgula([
                'kullaniciAdi' => $this->username,
                'sifre' => $this->password,
                'il' => $il,
                'ilce' => $ilce,
                'mahalle' => $mahalle,
                'ada' => $ada,
                'parsel' => $parsel
            ]);

            return $this->parseParcelData($response);
        } catch (\Exception $e) {
            \Log::error('TKGM Parsel Sorgu Hatası: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Koordinat ile parsel bul
     */
    public function findParcelByCoordinates($latitude, $longitude)
    {
        try {
            $response = $this->soapClient->KoordinatIleParselBul([
                'kullaniciAdi' => $this->username,
                'sifre' => $this->password,
                'enlem' => $latitude,
                'boylam' => $longitude
            ]);

            return $this->parseCoordinateResponse($response);
        } catch (\Exception $e) {
            \Log::error('TKGM Koordinat Sorgu Hatası: ' . $e->getMessage());
            return null;
        }
    }

    private function parseParcelData($response)
    {
        if (!isset($response->ParselBilgiSorgulaResult)) {
            return null;
        }

        $data = $response->ParselBilgiSorgulaResult;

        return [
            'ada' => $data->Ada ?? null,
            'parsel' => $data->Parsel ?? null,
            'alan' => $data->Alan ?? null,
            'nitelik' => $data->Nitelik ?? null,
            'malik_bilgileri' => $data->MalikBilgileri ?? null,
            'koordinatlar' => [
                'x' => $data->X ?? null,
                'y' => $data->Y ?? null
            ]
        ];
    }
}
```

---

## 🎯 CONTEXT7 DUAL SYSTEM {#context7}

### İkili Context7 Entegrasyon Sistemi

**Sistem Bileşenleri:**

- ✅ **Upstash Context7 MCP** - Library documentation
- ✅ **Yalıhan Bekçi Context7** - Project compliance rules
- ✅ **Auto-activation** - "Context7 kullan" komutu ile her ikisi aktif
- ✅ **Validation Pipeline** - Kod ve doküman uyumluluk kontrolü

### Upstash Context7 MCP

#### Library Documentation Access

```javascript
// MCP Context7 kullanımı
const context7Client = {
    async resolveLibrary(libraryName) {
        const response = await fetch('/mcp/context7/resolve-library-id', {
            method: 'POST',
            body: JSON.stringify({ libraryName }),
            headers: { 'Content-Type': 'application/json' },
        });
        return response.json();
    },

    async getLibraryDocs(libraryID, topic = null) {
        const response = await fetch('/mcp/context7/get-library-docs', {
            method: 'POST',
            body: JSON.stringify({
                context7CompatibleLibraryID: libraryID,
                topic,
                tokens: 10000,
            }),
            headers: { 'Content-Type': 'application/json' },
        });
        return response.json();
    },
};

// Örnek kullanım: Laravel docs
const laravel = await context7Client.resolveLibrary('Laravel');
const migrationDocs = await context7Client.getLibraryDocs(laravel.id, 'migrations');
```

### Yalıhan Bekçi Context7

#### Project Compliance Rules

```php
<?php

namespace App\Services\Context7;

class YalihanBekciService
{
    /**
     * Check Context7 compliance for code
     */
    public function validateCode($code, $type = 'migration')
    {
        $rules = $this->getComplianceRules($type);
        $violations = [];

        foreach ($rules as $rule) {
            if ($this->checkViolation($code, $rule)) {
                $violations[] = $rule;
            }
        }

        return [
            'compliant' => empty($violations),
            'violations' => $violations,
            'suggestions' => $this->getSuggestions($violations)
        ];
    }

    private function getComplianceRules($type)
    {
        $rules = [
            'migration' => [
                // Reference to legacy field naming (documentation context only)
                'forbidden_fields' => ['legacy_enabled_flag', 'legacy_is_enabled'],
                'required_patterns' => ['status', 'created_at', 'updated_at'],
                'naming_convention' => 'snake_case',
                // Reference to legacy CSS framework patterns (documentation context only)
                'forbidden_classes' => ['legacy_neo_patterns', 'legacy_btn_patterns', 'legacy_card_patterns']
            ],
            'frontend' => [
                'required_classes' => ['dark:', 'transition-'],
                // Reference to legacy framework names (documentation context only)
                'forbidden_frameworks' => ['legacy_design_system', 'legacy_bootstrap_framework'],
                'mandatory_css' => 'tailwind'
            ]
        ];

        return $rules[$type] ?? [];
    }
}
```

---

## 🤖 MCP SERVERS {#mcp}

### Model Context Protocol Server Entegrasyonu

**Active Servers:**

- ✅ **Yalıhan Bekçi** - Project rules ve compliance (Port: 4000)
- ✅ **Context7 MCP** - Library documentation access
- ✅ **Database MCP** - MySQL query interface
- ✅ **Container MCP** - Docker management

### Server Management

#### Start/Stop Scripts

```bash
#!/bin/bash
# scripts/services/start-all-mcp-servers.sh

# Start Yalıhan Bekçi server
./scripts/services/start-bekci-server.sh

# Start Context7 MCP
npx @context7/mcp-server

# Start Database MCP
npx @modelcontextprotocol/database-mcp

# Start Container MCP
npx @modelcontextprotocol/container-mcp

echo "All MCP servers started successfully"
```

#### Health Check

```php
class MCPHealthCheckCommand extends Command
{
    protected $signature = 'mcp:health-check';
    protected $description = 'Check MCP servers health';

    public function handle()
    {
        $servers = [
            'Yalıhan Bekçi' => 'http://localhost:4000/health',
            'Context7' => 'http://localhost:3001/health',
            'Database' => 'http://localhost:3002/health'
        ];

        foreach ($servers as $name => $url) {
            try {
                $response = Http::timeout(5)->get($url);
                $this->info("✅ {$name}: " . ($response->successful() ? 'OK' : 'FAILED'));
            } catch (\Exception $e) {
                $this->error("❌ {$name}: DOWN");
            }
        }
    }
}
```

---

## 🧠 AI SAĞLAYICILARI {#ai}

### Multi-Provider AI System

**Desteklenen Sağlayıcılar:**

- ✅ **OpenAI** - GPT-4, GPT-4 Vision, DALL-E 3
- ✅ **DeepSeek** - Cost-effective alternative
- ✅ **Google Gemini** - Multimodal AI
- ✅ **Anthropic Claude** - Advanced reasoning
- ✅ **Ollama** - Local AI models

### AI Service Implementation

```php
<?php

namespace App\Services\AI;

class AIProviderManager
{
    private $providers = [
        'openai' => OpenAIService::class,
        'deepseek' => DeepSeekService::class,
        'gemini' => GeminiService::class,
        'claude' => ClaudeService::class,
        'ollama' => OllamaService::class
    ];

    public function getProvider($providerName = null): AIProviderInterface
    {
        $provider = $providerName ?? config('ai.default_provider', 'openai');

        if (!isset($this->providers[$provider])) {
            throw new \InvalidArgumentException("Provider {$provider} not supported");
        }

        return app($this->providers[$provider]);
    }

    public function generatePropertyDescription($images, $propertyData, $provider = null): string
    {
        $aiService = $this->getProvider($provider);

        $prompt = "Bu emlak fotoğraflarını ve bilgilerini analiz ederek satış açıklaması yaz:

        Emlak Bilgileri:
        - Tip: {$propertyData['type']}
        - Metrekare: {$propertyData['size']}m²
        - Oda Sayısı: {$propertyData['rooms']}
        - Lokasyon: {$propertyData['location']}

        Açıklama Türkçe, profesyonel ve satış odaklı olmalı.";

        if ($aiService->supportsVision() && !empty($images)) {
            return $aiService->generateWithImages($prompt, $images);
        } else {
            return $aiService->generate($prompt);
        }
    }
}
```

### Provider Configurations

```php
// config/ai.php
return [
    'default_provider' => env('AI_DEFAULT_PROVIDER', 'openai'),
    'default_model' => env('AI_DEFAULT_MODEL', 'gpt-4'),

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4'),
            'max_tokens' => 2000,
            'temperature' => 0.7
        ],
        'deepseek' => [
            'api_key' => env('DEEPSEEK_API_KEY'),
            'base_url' => 'https://api.deepseek.com/v1',
            'model' => 'deepseek-chat'
        ],
        'gemini' => [
            'api_key' => env('GOOGLE_AI_API_KEY'),
            'model' => 'gemini-pro',
            'safety_threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
        ]
    ]
];
```

---

## 📚 KAYNAK DOSYALAR (BİRLEŞTİRİLDİ)

Bu dokümanda şu dosyalar birleştirilmiştir:

### Integrations Klasörü

1. `docs/integrations/TCMB-KUR-API-RAPOR-2025-11-05.md`
2. `docs/integrations/n8n-ai-entegrasyon-senaryolari.md`
3. `docs/integrations/n8n-entegrasyonu.md`

### Maps Alt Klasörü

1. `docs/integrations/maps/GOOGLE_MAPS_API.md`
2. `docs/integrations/maps/MAPBOX_INTEGRATION.md`
3. `docs/integrations/maps/OPENSTREETMAP.md`

### TKGM Alt Klasörü

1. `docs/integrations/tkgm/PARSEL_SORGULAMA.md`
2. `docs/integrations/tkgm/KADASTRO_API.md`

**Context7 Compliance:** ✅ C7-INTEGRATIONS-KONSOLIDE-2025-11-25  
**Tarih:** 25 Kasım 2025
