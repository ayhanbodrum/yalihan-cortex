# 🧠 Yalıhan Emlak - Gemini AI Master Prompt

**Kullanım:** Her yeni Gemini sohbetinde bu dosyanın içeriğini kopyala-yapıştır yap.

---

## 📋 PROJE ÖZETİ

**Proje:** Yalıhan Emlak - Laravel 11 + Context7 Standartları  
**Veritabanı:** MySQL (yalihanemlak_ultra)  
**CSS Framework:** Tailwind CSS ONLY (Neo Design System YASAK)  
**JavaScript:** Vanilla JS ONLY (ağır kütüphaneler yasak)  
**AI Sistemi:** YalihanCortex (merkezi zeka sistemi)

---

## 🚫 KRİTİK YASAK PATTERN'LER

### Database Fields

```php
// ❌ YASAK - ASLA KULLANMA
'enabled', 'is_active', 'aktif', 'durum', 'active'
'order' (yerine 'display_order')
'sehir', 'sehir_id', 'semt_id' (yerine 'il', 'il_id', 'mahalle_id')
'musteri', 'musteri_id' (yerine 'kisi', 'kisi_id')

// ✅ ZORUNLU
'status' (boolean veya varchar)
'display_order' (sıralama için)
'il_id', 'ilce_id', 'mahalle_id' (lokasyon)
'kisi_id' (kişi referansı)
```

### CSS Classes

```html
<!-- ❌ YASAK -->
<button class="neo-btn neo-btn-primary">Kaydet</button>
<button class="btn btn-primary">Kaydet</button>
<div class="card">...</div>

<!-- ✅ ZORUNLU - Tailwind CSS ONLY -->
<button
    class="px-4 py-2 bg-blue-600 text-white rounded-lg
               hover:bg-blue-700 hover:scale-105
               active:scale-95
               focus:ring-2 focus:ring-blue-500
               transition-all duration-200
               dark:bg-blue-500 dark:hover:bg-blue-600"
>
    Kaydet
</button>
```

### Route Naming

```php
// ❌ YASAK
Route::prefix('crm')->group(...);  // Double prefix yasak
Route::prefix('admin.crm')->group(...);  // Double prefix yasak

// ✅ ZORUNLU
Route::prefix('admin')->group(...);  // Tek prefix
Route::name('admin.ilanlar.index')->get(...);
```

---

## 🏗️ PROJE MİMARİSİ

### Ana Model: Ilan

```php
// app/Models/Ilan.php
class Ilan extends Model
{
    protected $table = 'ilanlar';

    // ✅ Context7 Compliant Fields
    protected $fillable = [
        'baslik',           // İlan başlığı
        'aciklama',         // İlan açıklaması
        'fiyat',            // Ana fiyat
        'price_text',       // Fiyatın yazıyla gösterimi
        'status',           // ✅ status (NOT enabled/aktif)
        'il_id',            // ✅ il_id (NOT sehir_id)
        'ilce_id',
        'mahalle_id',       // ✅ mahalle_id (NOT semt_id)
        'ana_kategori_id',
        'alt_kategori_id',

        // Arsa için özel alanlar
        'ada_no', 'parsel_no', 'imar_statusu',
        'alan_m2', 'kaks', 'taks', 'gabari',

        // Video sistemi
        'video_url', 'video_status', 'video_last_frame',
        'nearby_places',    // JSON - POI listesi
    ];

    // İlişkiler
    public function il() { return $this->belongsTo(Il::class); }
    public function ilce() { return $this->belongsTo(Ilce::class); }
    public function mahalle() { return $this->belongsTo(Mahalle::class); }
    public function userDanisman() { return $this->belongsTo(User::class, 'danisman_id'); }
}
```

### AI Servisleri

#### 1. YalihanCortex (Merkezi Zeka)

```php
// app/Services/AI/YalihanCortex.php
class YalihanCortex
{
    // Video script üretimi
    public function generateVideoScript(Ilan $ilan): array
    {
        // TKGM + POI + AI ile video script üretir
        // Ton: "Sakin, güven veren ve lüks"
        // 3 bölüm: Giriş, Çevre, Özellikler
    }

    // Arsa proje analizi
    public function analyzeArsaProject(Ilan $ilan, array $options = []): array
    {
        // KAKS/TAKS + fiyat varsayımı ile proje potansiyeli
    }
}
```

#### 2. AIService (Multi-Provider)

```php
// app/Services/AIService.php
class AIService
{
    // Desteklenen provider'lar: OpenAI, Gemini, DeepSeek, Ollama
    public function analyze($data, $context): array
    public function suggest($context, $category): array
    public function generate($prompt, $options): array

    // Tüm çağrılar AiLog'a kaydedilir (maliyet + süre)
}
```

#### 3. TKGMService

```php
// app/Services/TKGMService.php
class TKGMService
{
    // Tapu Kadastro verilerini çeker
    public function lookupByAdaParsel(string $adaNo, string $parselNo): array
    {
        // Redis cache kullanır
        // Dönen veriler: alan_m2, imar_statusu, kaks, taks, gabari
    }
}
```

#### 4. WikiMapiaService

```php
// app/Services/Integrations/WikiMapiaService.php
class WikiMapiaService
{
    // POI (Points of Interest) arama
    public function searchNearbyPlaces(float $lat, float $lng, int $radius = 1000): array
    {
        // Nominatim API fallback ile
        // Dönen: [{name, type, distance, lat, lng}, ...]
    }
}
```

---

## 🎨 UI STANDARTLARI

### Tailwind CSS Zorunlulukları

**Her interaktif element için:**

```html
<!-- ✅ ZORUNLU PATTERN -->
<button
    class="
    px-4 py-2                    <!-- Padding -->
    bg-blue-600                  <!-- Background -->
    text-white                   <!-- Text color -->
    rounded-lg                   <!-- Border radius -->
    hover:bg-blue-700           <!-- Hover state -->
    hover:scale-105             <!-- Hover animation -->
    active:scale-95             <!-- Active animation -->
    focus:ring-2                <!-- Focus ring -->
    focus:ring-blue-500         <!-- Focus color -->
    disabled:opacity-50         <!-- Disabled state -->
    disabled:cursor-not-allowed <!-- Disabled cursor -->
    transition-all duration-200  <!-- ✅ ZORUNLU TRANSITION -->
    dark:bg-blue-500            <!-- ✅ ZORUNLU DARK MODE -->
    dark:hover:bg-blue-600     <!-- Dark mode hover -->
"
>
    Buton Metni
</button>
```

### Form Elemanları

```html
<!-- ✅ DOĞRU FORM INPUT -->
<input
    type="text"
    class="w-full px-4 py-2.5
           bg-white dark:bg-gray-800
           text-gray-900 dark:text-white
           border border-gray-300 dark:border-gray-600
           rounded-lg
           focus:ring-2 focus:ring-blue-500
           focus:border-blue-500
           transition-all duration-200"
    placeholder="Örnek metin"
/>
```

---

## 📁 ÖNEMLİ DOSYA YAPISI

```
app/
├── Models/
│   ├── Ilan.php              # ⭐ Ana model
│   ├── Il.php, Ilce.php, Mahalle.php
│   ├── User.php
│   └── Kisi.php              # ✅ kisi (NOT musteri)
│
├── Services/
│   ├── AI/
│   │   ├── YalihanCortex.php      # ⭐ Merkezi zeka
│   │   └── SmartPropertyMatcherAI.php
│   ├── AIService.php              # Multi-provider AI
│   ├── TKGMService.php            # Tapu Kadastro
│   └── Integrations/
│       ├── WikiMapiaService.php   # POI arama
│       └── AudioGenerationService.php  # TTS (ElevenLabs)
│
├── Http/
│   └── Controllers/
│       ├── Admin/
│       │   └── IlanController.php  # ⭐ Ana controller
│       └── Api/
│           └── AIController.php   # AI endpoints
│
└── Jobs/
    └── RenderMarketingVideo.php    # Video render job

resources/views/admin/ilanlar/
├── show.blade.php                  # ⭐ İlan detay sayfası
├── components/
│   ├── video-tab.blade.php        # Video sekmesi (arsa için)
│   ├── video-status-widget.blade.php
│   └── location-map.blade.php     # Harita component
```

---

## 🔧 KOD ÖRNEKLERİ

### Controller Örneği

```php
// app/Http/Controllers/Admin/IlanController.php
public function show(Ilan $ilan)
{
    // ✅ Context7 Compliant
    $iller = Il::orderBy('il_adi')->select(['id', 'il_adi'])->get();
    $ilceler = collect();
    if ($ilan->il_id) {
        $ilceler = Ilce::where('il_id', $ilan->il_id)->get();
    }

    return view('admin.ilanlar.show', compact(
        'ilan', 'iller', 'ilceler', 'mahalleler'
    ));
}
```

### Blade Template Örneği

```blade
{{-- ✅ DOĞRU BLADE PATTERN --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg p-6">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
        {{ $ilan->baslik }}
    </h2>

    <button @click="doSomething()"
        class="px-4 py-2 bg-blue-600 text-white rounded-lg
               hover:bg-blue-700 hover:scale-105
               active:scale-95
               focus:ring-2 focus:ring-blue-500
               transition-all duration-200
               dark:bg-blue-500 dark:hover:bg-blue-600">
        İşlem Yap
    </button>
</div>
```

### Service Örneği

```php
// ✅ DOĞRU SERVICE PATTERN
class MyService
{
    public function __construct(
        protected AIService $aiService,
        protected TKGMService $tkgmService
    ) {}

    public function processIlan(Ilan $ilan): array
    {
        // ✅ Context7: status kullan
        if ($ilan->status === 'Aktif') {
            // İşlem yap
        }

        // ✅ Context7: il_id kullan
        $il = $ilan->il; // ✅ NOT $ilan->sehir

        return [
            'success' => true,
            'data' => $result
        ];
    }
}
```

---

## 🎯 ÖNEMLİ KURALLAR ÖZETİ

1. **Database:** `status` kullan (NOT enabled/aktif), `il_id` kullan (NOT sehir_id)
2. **CSS:** Sadece Tailwind utility classes, `transition-all duration-200` zorunlu
3. **Dark Mode:** Her element için dark mode variant zorunlu
4. **JavaScript:** Vanilla JS ONLY, Alpine.js kullanılabilir
5. **AI:** Her zaman `AIService` veya `YalihanCortex` kullan
6. **Routes:** `admin.*` prefix, double prefix yasak
7. **Terminology:** `kisi` kullan (NOT musteri)

---

## 📚 REFERANS DOSYALAR

- **Authority:** `.context7/authority.json` (TEK YETKİLİ KAYNAK)
- **Forbidden Patterns:** `.context7/FORBIDDEN_PATTERNS.md`
- **Tailwind Guide:** `.context7/TAILWIND-TRANSITION-RULE.md`
- **Migration Standards:** `.context7/MIGRATION_TEMPLATE_STANDARDS.md`

---

## 🚀 KULLANIM TALİMATI

**Gemini'ye şunu söyle:**

> "Merhaba! Yalıhan Emlak projesi için çalışıyoruz. Yukarıdaki kuralları ve mimariyi takip et. Özellikle:
>
> - Context7 standartlarına %100 uyumlu ol
> - Tailwind CSS ONLY kullan (neo-\* yasak)
> - Database field'ları: status (NOT enabled), il_id (NOT sehir_id)
> - Her element için transition ve dark mode ekle
>
> Şimdi [GÖREVİNİ BURAYA YAZ]"

---

## 📅 BUGÜN YAPILAN İŞLEMLER (Aralık 2025)

### 1. ✅ Video Sekmesi Oluşturuldu (AraziPro Referanslı)

**Dosya:** `resources/views/admin/ilanlar/components/video-tab.blade.php`

**Özellikler:**

- **Sol Panel:** Video Kayıt kartı
    - Çözünürlük seçenekleri (720p/1080p)
    - Büyük kırmızı "Sesli Video Kaydı Başlat" butonu
    - Özellikler listesi (TKGM + POI + Yalihan Cortex, TTS, 1080p, 360° dönüş, fade geçişleri)
- **Sağ Panel:** Harita görünümü (600px yükseklik)
    - Lokasyon overlay'leri (üst sol)
    - Danışman kartı overlay'i (alt sol)
- **Alt Bölüm:**
    - Sosyal Medya Gönderisi Oluştur butonu (placeholder)
    - Pazar Analizi Metni Oluştur butonu (placeholder)

**Görünürlük:** Sadece arsa ilanları için (`alt_kategori->slug === 'arsa'`)

**Kullanım:**

```blade
@if($isArsa)
    <button @click="tab='video'">Video</button>
@endif

<div x-show="tab==='video'">
    @include('admin.ilanlar.components.video-tab', ['ilan' => $ilan])
</div>
```

### 2. ✅ Video API Endpoints

**Routes:** `routes/api.php`

```php
Route::prefix('ai')->name('api.ai.')->middleware(['web', 'auth'])->group(function () {
    Route::post('/start-video-render/{ilanId}', [AIController::class, 'startVideoRender'])
        ->name('start-video-render');

    Route::get('/video-status/{ilanId}', [AIController::class, 'getVideoStatus'])
        ->name('video-status');
});
```

**Controller:** `app/Http/Controllers/Api/AIController.php`

- `startVideoRender(int $ilanId)` - Video render job'ını kuyruğa ekler
- `getVideoStatus(int $ilanId)` - Video durumunu döndürür (status, progress, url)

**Job:** `app/Jobs/RenderMarketingVideo.php`

- Asenkron video render işlemi
- Status güncellemeleri: 'queued' → 'rendering' → 'completed' / 'failed'

### 3. ✅ Hızlı İşlemler Butonları İyileştirmesi

**Dosya:** `resources/views/admin/ilanlar/show.blade.php`

**Değişiklikler:**

- Butonlar yatay düzende (ikon + yazı yan yana)
- Her buton için açıklayıcı metinler eklendi:
    - **İlanı Düzenle:** "Bilgileri güncelle"
    - **İlanı Kopyala:** "Taslak olarak kaydet"
    - **Durum Değiştir:** "Aktif ↔ Pasif"
    - **AI Analiz:** "Fiyat & SEO önerileri"
- Tailwind CSS ile modern tasarım
- Hover ve active animasyonları

**Örnek:**

```html
<button
    class="inline-flex items-center gap-2 px-5 py-3 bg-green-600 
               hover:bg-green-700 text-white rounded-xl 
               hover:scale-105 active:scale-95 
               transition-all duration-200"
>
    <svg class="w-5 h-5">...</svg>
    <span>İlanı Kopyala</span>
</button>
```

### 4. ✅ Bug Fixler

**Problem:** `location-map.blade.php` component'i `$iller` değişkeni bekliyordu ama `video-tab.blade.php`'de geçirilmemişti.

**Çözüm:**

- `IlanController@show` metoduna `$iller`, `$ilceler`, `$mahalleler` eklendi
- `video-tab.blade.php`'de `location-map` include edilirken değişkenler geçirildi

**Kod:**

```php
// IlanController.php
$iller = Il::orderBy('il_adi')->select(['id', 'il_adi'])->get();
$ilceler = collect();
if ($ilan->il_id) {
    $ilceler = Ilce::where('il_id', $ilan->il_id)->get();
}
// ... mahalleler de benzer şekilde

return view('admin.ilanlar.show', compact(
    'ilan', 'iller', 'ilceler', 'mahalleler', ...
));
```

```blade
{{-- video-tab.blade.php --}}
@include('admin.ilanlar.components.location-map', [
    'ilan' => $ilan,
    'iller' => $iller ?? collect(),
    'ilceler' => $ilceler ?? collect(),
    'mahalleler' => $mahalleler ?? collect(),
])
```

### 5. ✅ Gemini Master Prompt Dosyası

**Dosya:** `GEMINI_MASTER_PROMPT.md`

**İçerik:**

- Proje özeti ve mimarisi
- Yasak pattern'ler (Context7 kuralları)
- Kod örnekleri (Model, Controller, Blade, Service)
- UI standartları (Tailwind CSS zorunlulukları)
- Bugün yapılan işlemler (bu bölüm)

**Kullanım:** Her yeni Gemini sohbetinde bu dosyanın içeriğini kopyala-yapıştır yap.

---

## 🎯 SONRAKI ADIMLAR (Placeholder'lar)

### 1. Sosyal Medya Gönderisi Oluşturma

**Dosya:** `resources/views/admin/ilanlar/components/video-tab.blade.php` (satır ~240)

**Şu an:** Placeholder fonksiyon (`generateSocialPost()`)

**Yapılacak:**

- API endpoint: `POST /api/ai/generate-social-post/{ilanId}`
- `AIController@generateSocialPost` metodu
- `YalihanCortex` ile Instagram/Facebook/LinkedIn gönderisi üretimi
- Dönen format: `{title, description, hashtags, platforms: ['instagram', 'facebook', 'linkedin']}`

### 2. Pazar Analizi Metni Oluşturma

**Dosya:** `resources/views/admin/ilanlar/components/video-tab.blade.php` (satır ~260)

**Şu an:** Placeholder fonksiyon (`generateMarketAnalysis()`)

**Yapılacak:**

- API endpoint: `POST /api/ai/generate-market-analysis/{ilanId}`
- `AIController@generateMarketAnalysis` metodu
- TKGM verileri + bölge analizi + `nearby_places` kullanarak profesyonel pazar analizi metni
- Dönen format: `{analysis_text, key_points: [], recommendations: []}`

### 3. Gerçek Video Render Pipeline

**Şu an:** Simüle ediliyor (`RenderMarketingVideo` job'ında)

**Yapılacak:**

- Gerçek video render engine entegrasyonu
- Canvas API + Google TTS + Smooth Audio Mixing
- 360° dönüş animasyonu
- Fade geçişleri
- Final video dosyası kaydetme (`storage/videos/`)

---

**Son Güncelleme:** Aralık 2025  
**Versiyon:** 1.1.0 (Bugün yapılan işlemler eklendi)
