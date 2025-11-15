# 🤖 AI KULLANIM ÖRNEKLERİ
**Tarih:** 2025-11-05  
**Versiyon:** v1.0

---

## 📋 İÇİNDEKİLER

1. [AI Servis Kullanımı](#ai-servis-kullanımı)
2. [İlan Açıklama Üretimi](#ilan-açıklama-üretimi)
3. [Fiyat Önerisi](#fiyat-önerisi)
4. [Talep Analizi](#talep-analizi)
5. [Kategori Önerisi](#kategori-önerisi)
6. [Field Suggestion](#field-suggestion)

---

## 🔧 AI SERVİS KULLANIMI

### Temel Kullanım

```php
use App\Services\AIService;

// AIService instance oluştur
$aiService = new AIService();

// Veya dependency injection ile
public function __construct(AIService $aiService)
{
    $this->aiService = $aiService;
}
```

### Provider Değiştirme

```php
// Aktif provider'ı değiştir
$aiService->switchProvider('google'); // google, openai, claude, deepseek, ollama

// Mevcut provider'ı al
$currentProvider = $aiService->getActiveProvider();
```

---

## 📝 İLAN AÇIKLAMA ÜRETİMİ

### Örnek 1: Basit Açıklama

```php
use App\Services\AIService;

$aiService = new AIService();

$prompt = "Bodrum'da denize sıfır lüks villa için profesyonel bir ilan açıklaması yaz. 
Özellikler: 5 yatak odası, havuz, bahçe, deniz manzarası. 
Fiyat: 2.500.000 TL";

$description = $aiService->generate($prompt, [
    'max_tokens' => 500,
    'temperature' => 0.7
]);

echo $description['data'];
```

### Örnek 2: Controller'da Kullanım

```php
// app/Http/Controllers/Admin/IlanController.php
use App\Services\AIService;

public function generateDescription(Request $request)
{
    $request->validate([
        'kategori' => 'required|string',
        'lokasyon' => 'required|string',
        'ozellikler' => 'array'
    ]);

    $aiService = new AIService();
    
    $prompt = "Emlak ilanı için açıklama yaz:
Kategori: {$request->kategori}
Lokasyon: {$request->lokasyon}
Özellikler: " . implode(', ', $request->ozellikler ?? []);

    try {
        $result = $aiService->generate($prompt, [
            'max_tokens' => 500,
            'temperature' => 0.7
        ]);

        return response()->json([
            'success' => true,
            'description' => $result['data']
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'AI açıklama üretilemedi: ' . $e->getMessage()
        ], 500);
    }
}
```

### Örnek 3: API Endpoint Kullanımı

```javascript
// Frontend JavaScript
async function generateDescription() {
    const response = await fetch('/api/admin/ai/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            prompt: "Bodrum'da denize sıfır lüks villa için açıklama yaz",
            options: {
                max_tokens: 500,
                temperature: 0.7
            }
        })
    });

    const data = await response.json();
    if (data.success) {
        document.getElementById('description').value = data.data;
    }
}
```

---

## 💰 FİYAT ÖNERİSİ

### Örnek 1: Basit Fiyat Analizi

```php
use App\Services\AIService;

$aiService = new AIService();

$data = [
    'kategori' => 'Konut',
    'lokasyon' => 'Bodrum',
    'tip' => 'Satılık',
    'metrekare' => 150,
    'ozellikler' => ['Havuz', 'Deniz Manzarası', 'Bahçe']
];

$priceSuggestion = $aiService->analyze($data, [
    'type' => 'price'
]);

print_r($priceSuggestion);
```

### Örnek 2: API Endpoint Kullanımı

```php
// app/Http/Controllers/Api/AIController.php
public function suggestPrice(Request $request)
{
    $kategoriId = $request->input('kategori_id');
    $ilId = $request->input('il_id');
    
    // Veritabanından benzer ilanların fiyat istatistiklerini al
    $stats = \App\Models\Ilan::query()
        ->when($kategoriId, fn($q) => $q->where('alt_kategori_id', $kategoriId))
        ->when($ilId, fn($q) => $q->where('il_id', $ilId))
        ->selectRaw('MIN(fiyat) as min, AVG(fiyat) as avg, MAX(fiyat) as max')
        ->first();

    return response()->json([
        'success' => true,
        'price' => [
            'min' => round($stats->min, -3),
            'avg' => round($stats->avg, -3),
            'max' => round($stats->max, -3)
        ]
    ]);
}
```

---

## 🔍 TALEP ANALİZİ

### Örnek 1: Talep Analizi

```php
use App\Services\AIService;

$aiService = new AIService();

$talepData = [
    'baslik' => 'Bodrum\'da denize sıfır villa',
    'tip' => 'Satılık',
    'kategori_id' => 1,
    'il_id' => 48,
    'ilce_id' => 500
];

$analysis = $aiService->analyze($talepData, [
    'type' => 'talep'
]);

print_r($analysis);
```

### Örnek 2: API Endpoint Kullanımı

```javascript
// Frontend JavaScript
async function analyzeRequest() {
    const response = await fetch('/api/admin/ai/analyze', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            action: 'talep_analysis',
            data: {
                baslik: 'Bodrum\'da denize sıfır villa',
                tip: 'Satılık',
                kategori_id: 1
            }
        })
    });

    const data = await response.json();
    console.log('Analysis:', data.analysis);
}
```

---

## 📂 KATEGORİ ÖNERİSİ

### Örnek 1: Kategori Önerisi

```php
use App\Services\AIService;

$aiService = new AIService();

$context = [
    'ozellikler' => ['Havuz', 'Deniz Manzarası', 'Bahçe'],
    'lokasyon' => 'Bodrum',
    'tip' => 'Satılık'
];

$suggestions = $aiService->suggest($context, 'category');

print_r($suggestions);
```

### Örnek 2: API Endpoint Kullanımı

```javascript
// Frontend JavaScript
async function getCategorySuggestions() {
    const response = await fetch('/api/admin/ai/suggest', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            context: {
                ozellikler: ['Havuz', 'Deniz Manzarası'],
                lokasyon: 'Bodrum'
            },
            type: 'category'
        })
    });

    const data = await response.json();
    console.log('Suggestions:', data);
}
```

---

## 🎯 FIELD SUGGESTION

### Örnek 1: Field Value Suggestion

```php
use App\Services\AIService;

$aiService = new AIService();

$kategoriSlug = 'yazlik';
$yayinTipi = 'Günlük';
$fieldSlug = 'gunluk_fiyat';
$context = [
    'lokasyon' => 'Bodrum',
    'metrekare' => 200,
    'oda_sayisi' => 5
];

$suggestion = $aiService->suggestFieldValue(
    $kategoriSlug,
    $yayinTipi,
    $fieldSlug,
    $context
);

echo "Önerilen günlük fiyat: " . $suggestion;
```

### Örnek 2: Auto-Fill Fields

```php
use App\Services\AIService;

$aiService = new AIService();

$kategoriSlug = 'yazlik';
$yayinTipi = 'Günlük';
$existingData = [
    'lokasyon' => 'Bodrum',
    'metrekare' => 200,
    'oda_sayisi' => 5
];

$suggestions = $aiService->autoFillFields(
    $kategoriSlug,
    $yayinTipi,
    $existingData
);

foreach ($suggestions as $fieldSlug => $value) {
    echo "{$fieldSlug}: {$value}\n";
}
```

---

## 🔄 SMART CALCULATE

### Örnek: Günlük Fiyattan Haftalık Hesaplama

```php
use App\Services\AIService;

$aiService = new AIService();

$sourceField = 'gunluk_fiyat';
$sourceValue = 500;
$targetField = 'haftalik_fiyat';
$context = [
    'sezon' => 'yaz',
    'indirim_orani' => 0.85
];

$calculated = $aiService->smartCalculate(
    $sourceField,
    $sourceValue,
    $targetField,
    $context
);

echo "Haftalık fiyat: " . $calculated . " TL";
```

---

## 📊 HEALTH CHECK

### Örnek: AI Sistem Sağlık Kontrolü

```php
use App\Services\AIService;

$aiService = new AIService();

$health = $aiService->healthCheck();

if ($health['status'] === 'healthy') {
    echo "AI sistem çalışıyor!\n";
    echo "Provider: " . $health['provider'] . "\n";
    echo "Yanıt süresi: " . $health['response_time'] . "ms\n";
} else {
    echo "AI sistem çalışmıyor: " . $health['error'] . "\n";
}
```

---

## 🎨 FRONTEND KULLANIM ÖRNEKLERİ

### Örnek 1: AI Widget Kullanımı

```blade
{{-- resources/views/admin/ilanlar/create.blade.php --}}
<x-admin.ai-widget
    :action="'generate-description'"
    :endpoint="'/api/admin/ai/generate'"
    :title="'AI Açıklama Üret'"
    :data="[
        'kategori' => $kategori->name,
        'lokasyon' => $il->il_adi . ', ' . $ilce->ilce_adi
    ]"
    :context="['type' => 'ilan']" />
```

### Örnek 2: Custom AI Button

```blade
<button onclick="generateWithAI()" 
        class="px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-lg">
    🤖 AI ile Üret
</button>

<script>
async function generateWithAI() {
    const response = await fetch('/api/admin/ai/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            prompt: "Bodrum'da denize sıfır villa için açıklama yaz"
        })
    });

    const data = await response.json();
    if (data.success) {
        document.getElementById('aciklama').value = data.data;
    }
}
</script>
```

---

## 🚨 HATA YÖNETİMİ

### Örnek: Try-Catch ile Hata Yönetimi

```php
use App\Services\AIService;

$aiService = new AIService();

try {
    $result = $aiService->generate($prompt, $options);
    // Başarılı
} catch (\Exception $e) {
    // Hata durumu
    Log::error('AI generation failed', [
        'error' => $e->getMessage(),
        'prompt' => $prompt
    ]);
    
    // Fallback: Kullanıcıya hata mesajı göster
    return response()->json([
        'success' => false,
        'message' => 'AI servisi şu anda kullanılamıyor. Lütfen daha sonra tekrar deneyin.'
    ], 500);
}
```

---

## 📝 NOTLAR

1. **API Key Gerekli**: AI servislerini kullanmak için önce API key'leri eklemeniz gerekir (`/admin/ai-settings`)

2. **Provider Seçimi**: Varsayılan provider `openai`'dir. Değiştirmek için `switchProvider()` metodunu kullanın.

3. **Cache**: AI yanıtları 1 saat süreyle cache'lenir. Cache'i temizlemek için `Cache::forget()` kullanın.

4. **Logging**: Tüm AI istekleri `ai_logs` tablosuna kaydedilir. İstatistikleri `/admin/ai-settings/analytics` sayfasından görebilirsiniz.

5. **Rate Limiting**: API provider'ların rate limit'leri vardır. Çok fazla istek göndermemeye dikkat edin.

---

**Son Güncelleme:** 2025-11-05

