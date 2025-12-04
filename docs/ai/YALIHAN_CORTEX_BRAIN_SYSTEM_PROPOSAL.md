# 🧠 Yalihan Cortex - Merkezi "Beyin" Sistemi Önerisi

**Tarih:** 2025-12-03  
**Durum:** 📋 Öneri - Uygulanacak  
**Versiyon:** 2.0

---

## 📊 Mevcut Durum Analizi

### ✅ YalihanCortex Kullanılan Yerler

1. **AIController** (`app/Http/Controllers/Api/AIController.php`)
   - ✅ `matchForSale()` - Talep eşleştirme
   - ✅ `getChurnRisk()` - Churn risk analizi
   - ✅ `getTopChurnRisks()` - Top churn risks
   - ✅ `submitFeedback()` - Feedback kaydı
   - ✅ `analyzeNegotiationStrategy()` - Pazarlık stratejisi
   - ✅ `voiceToCRM()` - Voice to CRM

2. **IlanController** (`app/Http/Controllers/Admin/IlanController.php`)
   - ✅ `checkIlanQuality()` - İlan kalite kontrolü (Pre-publishing)

3. **RenderMarketingVideo Job** (`app/Jobs/RenderMarketingVideo.php`)
   - ✅ `generateVideoScript()` - Video script üretimi

4. **AnalyzeAndPrioritizeDemand Job** (`app/Jobs/AnalyzeAndPrioritizeDemand.php`)
   - ✅ Talep analizi ve önceliklendirme

### ❌ YalihanCortex'e Gitmeyen AI İşlemleri

1. **IlanAIController** (`app/Http/Controllers/Admin/AI/IlanAIController.php`)
   - ❌ `generateTitle()` → Doğrudan `OllamaService` kullanıyor
   - ❌ `generateDescription()` → Doğrudan `OllamaService` kullanıyor
   - ❌ `analyzeLocation()` → Doğrudan `OllamaService` kullanıyor
   - ❌ `suggestPrice()` → Doğrudan `OllamaService` kullanıyor

2. **IlanController** (`app/Http/Controllers/Admin/IlanController.php`)
   - ❌ `generateAiTitle()` → Doğrudan `IlanAIController` kullanıyor
   - ❌ `generateAiDescription()` → Doğrudan `IlanAIController` kullanıyor
   - ❌ `getAIPropertySuggestions()` → Doğrudan `IlanAIController` kullanıyor
   - ❌ `optimizePriceWithAi()` → Doğrudan `IlanAIController` kullanıyor

3. **AIContentController** (`app/Http/Controllers/Api/AIContentController.php`)
   - ❌ Doğrudan HTTP istekleri yapıyor (Ollama, OpenAI, Gemini, Claude)

---

## 🎯 Önerilen Çözüm: YalihanCortex Genişletme

### 1. YalihanCortex'e Yeni Metodlar Ekle

```php
// app/Services/AI/YalihanCortex.php

/**
 * İlan Başlığı Üretimi
 * 
 * @param Ilan|array $ilan İlan modeli veya ilan verisi
 * @param array $options ['tone' => 'seo|kurumsal|hizli_satis|luks']
 * @return array
 */
public function generateIlanTitle($ilan, array $options = []): array

/**
 * İlan Açıklaması Üretimi
 * 
 * @param Ilan|array $ilan İlan modeli veya ilan verisi
 * @param array $options ['tone' => 'seo|kurumsal|hizli_satis|luks', 'length' => 'short|medium|long']
 * @return array
 */
public function generateIlanDescription($ilan, array $options = []): array

/**
 * Lokasyon Analizi
 * 
 * @param array $locationData ['il', 'ilce', 'mahalle']
 * @return array
 */
public function analyzeLocation(array $locationData): array

/**
 * Fiyat Önerisi
 * 
 * @param Ilan|array $ilan İlan modeli veya ilan verisi
 * @param array $options ['strategy' => 'aggressive|moderate|premium']
 * @return array
 */
public function suggestPrice($ilan, array $options = []): array

/**
 * AI Provider Seçimi (Akıllı Fallback)
 * 
 * @param string $taskType 'title|description|analysis|generation'
 * @param array $context
 * @return string Provider name ('ollama', 'openai', 'gemini', 'deepseek')
 */
protected function selectBestProvider(string $taskType, array $context = []): string
```

### 2. IlanAIController'ı YalihanCortex'e Yönlendir

```php
// app/Http/Controllers/Admin/AI/IlanAIController.php

class IlanAIController extends Controller
{
    protected YalihanCortex $cortex;
    
    public function __construct(YalihanCortex $cortex)
    {
        $this->cortex = $cortex;
    }
    
    protected function generateTitle(Request $request): JsonResponse
    {
        // ✅ YalihanCortex üzerinden
        $result = $this->cortex->generateIlanTitle($request->all(), [
            'tone' => $request->input('ai_tone', 'seo'),
        ]);
        
        return response()->json([
            'success' => $result['success'] ?? true,
            'variants' => $result['titles'] ?? [],
            'model' => $result['provider'] ?? 'unknown',
        ]);
    }
    
    // ... diğer metodlar
}
```

### 3. IlanController'ı YalihanCortex'e Yönlendir

```php
// app/Http/Controllers/Admin/IlanController.php

public function generateAiTitle(Request $request)
{
    // ✅ YalihanCortex üzerinden
    $cortex = app(YalihanCortex::class);
    
    $context = $request->input('context', []);
    $result = $cortex->generateIlanTitle($context, [
        'tone' => $request->input('ai_tone', 'seo'),
    ]);
    
    return response()->json([
        'success' => $result['success'] ?? true,
        'title' => $result['titles'][0] ?? 'Başlık üretilemedi',
        'alternatives' => array_slice($result['titles'] ?? [], 0, 3),
    ]);
}
```

### 4. AIContentController'ı YalihanCortex'e Yönlendir

```php
// app/Http/Controllers/Api/AIContentController.php

class AIContentController extends Controller
{
    protected YalihanCortex $cortex;
    
    public function __construct(YalihanCortex $cortex)
    {
        $this->cortex = $cortex;
    }
    
    public function generate(Request $request)
    {
        $type = $request->input('type'); // 'title', 'description', etc.
        $data = $request->input('data', []);
        
        // ✅ YalihanCortex üzerinden
        switch ($type) {
            case 'title':
                $result = $this->cortex->generateIlanTitle($data);
                break;
            case 'description':
                $result = $this->cortex->generateIlanDescription($data);
                break;
            // ...
        }
        
        return response()->json($result);
    }
}
```

---

## 🏗️ Yeni Mimari

```
┌─────────────────────────────────────────────────────────┐
│              Tüm AI Controller'lar                       │
│  (IlanAIController, AIController, AIContentController) │
└────────────────────┬────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────┐
│              YalihanCortex (Merkezi Beyin)              │
│  - generateIlanTitle()                                  │
│  - generateIlanDescription()                            │
│  - analyzeLocation()                                    │
│  - suggestPrice()                                       │
│  - matchForSale()                                       │
│  - generateVideoScript()                                │
│  - checkIlanQuality()                                   │
│  - getChurnRisk()                                       │
│  - priceValuation()                                     │
│  - selectBestProvider() (Akıllı Provider Seçimi)       │
└────────────────────┬────────────────────────────────────┘
                     │
         ┌───────────┴───────────┐
         │                       │
         ▼                       ▼
┌─────────────────┐    ┌─────────────────┐
│   OllamaService │    │    AIService    │
│   (Local AI)    │    │ (Multi-Provider)│
└─────────────────┘    └─────────────────┘
         │                       │
         └───────────┬───────────┘
                     │
         ┌───────────┴───────────┐
         │                       │
         ▼                       ▼
┌─────────────────┐    ┌─────────────────┐
│   OpenAI API    │    │   Gemini API    │
│   DeepSeek API  │    │   Claude API    │
└─────────────────┘    └─────────────────┘
```

---

## ✅ Avantajlar

1. **Merkezi Yönetim:** Tüm AI işlemleri tek noktadan yönetilir
2. **Akıllı Provider Seçimi:** Task tipine göre en uygun provider seçilir
3. **Fallback Sistemi:** Bir provider başarısız olursa otomatik yedek provider'a geçer
4. **Logging & Monitoring:** Tüm AI işlemleri AiLog'a kaydedilir
5. **Performance Tracking:** Timer ile performans ölçümü
6. **Context7 Uyumlu:** Tüm işlemler MCP standartlarına uygun
7. **Kod Tekrarı Önleme:** Aynı mantık tekrar yazılmaz
8. **Test Edilebilirlik:** Merkezi test yazılabilir

---

## 📋 Uygulama Adımları

1. ✅ YalihanCortex'e yeni metodlar ekle
2. ✅ IlanAIController'ı YalihanCortex'e yönlendir
3. ✅ IlanController'ı YalihanCortex'e yönlendir
4. ✅ AIContentController'ı YalihanCortex'e yönlendir
5. ✅ Eski doğrudan OllamaService kullanımlarını kaldır
6. ✅ Test et ve performans ölçümü yap
7. ✅ Dokümantasyon güncelle

---

## 🔍 Kontrol Listesi

- [ ] YalihanCortex::generateIlanTitle() eklendi
- [ ] YalihanCortex::generateIlanDescription() eklendi
- [ ] YalihanCortex::analyzeLocation() eklendi
- [ ] YalihanCortex::suggestPrice() eklendi
- [ ] YalihanCortex::selectBestProvider() eklendi
- [ ] IlanAIController YalihanCortex kullanıyor
- [ ] IlanController YalihanCortex kullanıyor
- [ ] AIContentController YalihanCortex kullanıyor
- [ ] Eski doğrudan OllamaService kullanımları kaldırıldı
- [ ] Testler yazıldı
- [ ] Dokümantasyon güncellendi

---

**Sonuç:** YalihanCortex zaten var ve aktif kullanılıyor, ancak tüm AI işlemlerini kapsamıyor. Önerilen genişletme ile tüm AI işlemleri merkezi "Beyin" sistemi üzerinden yönetilecek.

