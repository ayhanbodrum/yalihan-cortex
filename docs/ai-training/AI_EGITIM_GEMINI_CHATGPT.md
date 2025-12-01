# 🤖 YALIHAN EMLAK - AI EĞİTİM DOKÜMANI
## ChatGPT ve Gemini için Kapsamlı Proje Rehberi

**Versiyon:** 2.0.0  
**Tarih:** 29 Kasım 2025  
**Hedef AI:** ChatGPT, Gemini, Claude  
**Durum:** ✅ Aktif

---

## 📋 İÇİNDEKİLER

1. [Proje Genel Bakış](#proje-genel-bakış)
2. [Teknoloji Stack](#teknoloji-stack)
3. [Proje Davranış Biçimi](#proje-davranış-biçimi)
4. [Context7 Kuralları](#context7-kuralları)
5. [Modül Yapısı](#modül-yapısı)
6. [AI Sistemi ve Rolleri](#ai-sistemi-ve-rolleri)
7. [Kod Yazma Standartları](#kod-yazma-standartları)
8. [Veritabanı Standartları](#veritabanı-standartları)
9. [Frontend Standartları](#frontend-standartları)
10. [API ve Servis Standartları](#api-ve-servis-standartları)
11. [Yasaklı Pattern'ler](#yasaklı-patternler)
12. [Hızlı Başlangıç Komutları](#hızlı-başlangıç-komutları)

---

## 🎯 PROJE GENEL BAKIŞ

### Proje Adı
**Yalıhan Emlak - AI Destekli Emlak Yönetim Platformu**

### Proje Tanımı
Yalıhan Emlak, sadece bir CRM değil, **yapay zeka destekli bütünleşik bir emlak yönetim sistemidir**. Sistem, emlak danışmanlarının işlerini kolaylaştırmak için AI asistanları kullanır ancak **AI hiçbir zaman tek başına karar vermez veya müşteriye mesaj göndermez**.

### Temel Bileşenler

```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│   🏛️ YALIHAN EMLAK SİSTEMİ                             │
│                                                         │
│   1. 📊 Emlak CRM (Portföy, Müşteri, Sözleşme)         │
│   2. 📈 Piyasa Veri Tabanı (Harici İlanlar)            │
│   3. 🤖 AI Analiz Sistemi (Raporlar, Analizler)        │
│   4. 🏠 Demirbaş/Envanter Sistemi                      │
│   5. 💬 Çok Kanallı İletişim (Telegram, WhatsApp)      │
│   6. ⚙️ Otomasyon Katmanı (n8n)                        │
│   7. 🧠 AI Katmanı (AnythingLLM + Ollama)              │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### Proje Metrikleri

```yaml
Context7 Compliance: 98.82% → 99.5% (hedef)
Component Library: 12 bileşen
Bundle Size: 44KB (11.57KB gzipped) ✅
Database Tables: 57 tablo
Eloquent Models: 98 model
Admin Controllers: 61 controller
Active Features: 15+ modül
System Health: B+ (87/100)
```

---

## 🛠️ TEKNOLOJİ STACK

### Backend
```yaml
Framework: Laravel 10.x
PHP Version: 8.2+
Database: MariaDB/MySQL
ORM: Eloquent
Queue: Redis
Cache: Redis
```

### Frontend
```yaml
Template Engine: Blade
CSS Framework: Tailwind CSS (Pure Utility Classes ONLY)
JavaScript: Alpine.js + Vanilla JS
Design System: Context7 (Neo Design YASAK)
```

### AI & Automation
```yaml
AI Platform: AnythingLLM (Docker)
AI Models: Ollama (Local)
Automation: n8n (HTTP Webhook + REST)
Messaging: Telegram (aktif), WhatsApp (planlı)
```

### Development Tools
```yaml
Code Quality: PHPStan, PHP CS Fixer, Pint
Version Control: Git
Pre-commit Hooks: Context7 validation
MCP Servers: 
  - Yalıhan Bekçi MCP (AI Learning)
  - Context7 Validator MCP
  - Laravel MCP
```

---

## 🎭 PROJE DAVRANIŞBIÇIMI

### 1. AI'nın Rolü

**KRİTİK PRENSIP:**
> AI, bu projede insan danışmanların **yardımcısıdır**, müşterinin yerine karar veren veya tek başına işlem yapan bir aktör **DEĞİLDİR**.

### 2. İş Akışı Mantığı

```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│   Danışman İsteği                                       │
│         ↓                                               │
│   AI Taslak Üretir                                      │
│         ↓                                               │
│   DB'ye Kaydedilir (status: draft)                      │
│         ↓                                               │
│   Danışman İnceler/Düzenler                             │
│         ↓                                               │
│   Danışman Onaylar                                      │
│         ↓                                               │
│   İşlem Gerçekleşir                                     │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

### 3. Veri Ayrımı

| Veri Tipi | Database | Açıklama |
|-----------|----------|----------|
| **Market Data** | `yalihan_market` | Sahibinden, Emlakjet vb. harici ilanlar |
| **AI Analysis** | `yalihan_ai` | AI raporları, konuşmalar, analizler |
| **CRM Data** | Ana DB | Kendi portföyümüz, müşteriler, sözleşmeler |

### 4. Onay Mekanizması

**Her AI çıktısı için zorunlu:**
- ✅ `status` field: `draft`, `pending_review`, `approved`, `published`
- ✅ `ai_generated_at` timestamp
- ✅ `ai_model_used` string
- ✅ `approved_by` foreign key
- ✅ `approved_at` timestamp

---

## 📐 CONTEXT7 KURALLARI

### Versiyon ve Enforc ement
```yaml
Version: 6.0.0 (C7-UNIVERSAL-IDE-STANDARDS-2025-11-24)
Enforcement: STRICT
  - Pre-commit hooks
  - CI/CD pipeline
  - MCP real-time validation
  - Auto-teaching (Yalıhan Bekçi)
```

### Temel Prensip
> **"Context7, kod standartlarını yöneten merkezi bir sistemdir. Tüm kod, Context7 kurallarına uygun olmalıdır."**

### Kritik Kurallar

#### 1. Database Field Naming

| ❌ YASAK | ✅ DOĞRU | Sebep |
|----------|----------|-------|
| `order` | `display_order` | SQL keyword |
| `enabled` | `status` | Boolean field yasak |
| `is_active` | `status` | Boolean field yasak |
| `aktif` | `status` | Türkçe yasak |
| `durum` | `status` | Türkçe yasak |
| `sehir_id` | `il_id` | Yanlış terminoloji |
| `semt_id` | `mahalle_id` | Yanlış terminoloji |
| `musteri_*` | `kisi_*` | Yanlış terminoloji |

#### 2. CSS Framework

| ❌ YASAK | ✅ DOĞRU |
|----------|----------|
| `neo-btn` | Tailwind utility classes |
| `neo-card` | Tailwind utility classes |
| `neo-input` | Tailwind utility classes |
| `btn-primary` | Tailwind utility classes |
| `form-control` | Tailwind utility classes |

**PERMANENT STANDARD:** Neo Design System tamamen YASAK. Sadece Pure Tailwind CSS kullanılır.

#### 3. Route Naming

```php
// ❌ YASAK - Double prefix
Route::get('/admin/dashboard', ...)->name('admin.admin.dashboard');

// ✅ DOĞRU
Route::get('/admin/dashboard', ...)->name('admin.dashboard');
```

#### 4. JavaScript Libraries

| ❌ YASAK | ✅ DOĞRU | Sebep |
|----------|----------|-------|
| React-Select (170KB) | Vanilla JS (3KB) | Çok ağır |
| Choices.js (48KB) | Context7 Live Search | Çok ağır |
| Select2 | Vanilla JS | jQuery dependency |

---

## 🧩 MODÜL YAPISI

### 1. CRM Modülü

```
app/Modules/Crm/
├── Models/
│   ├── Kisi.php          # Müşteri/Satıcı
│   ├── Ilan.php          # İlan
│   ├── Talep.php         # Müşteri talebi
│   ├── Randevu.php       # Randevular
│   └── Sozlesme.php      # Sözleşmeler
├── Controllers/
│   └── Admin/
│       ├── KisiController.php
│       ├── IlanController.php
│       └── ...
└── Services/
    ├── KisiService.php
    └── IlanService.php
```

### 2. Piyasa DB (yalihan_market)

```sql
-- Harici portal ilanları
market_listings
├── portal_name (sahibinden, emlakjet, vb.)
├── portal_listing_id
├── fiyat
├── il_id, ilce_id, mahalle_id
├── m2_brut, m2_net
├── first_seen_at
└── last_seen_at

-- Fiyat geçmişi
market_price_history
├── listing_id
├── fiyat
└── recorded_at

-- Bölge istatistikleri
market_price_stats
├── il_id, ilce_id, mahalle_id
├── avg_price_per_m2
└── updated_at
```

### 3. AI Analiz DB (yalihan_ai)

```sql
-- AI arsa analizleri
ai_land_plot_analyses
├── ilan_id
├── analysis_type
├── confidence_score
├── recommendations (JSON)
├── ai_model_used
└── ai_generated_at

-- AI konuşmaları
ai_conversations
├── user_id
├── channel (telegram, whatsapp, etc.)
├── messages (JSON)
└── created_at

-- AI mesajları
ai_messages
├── conversation_id
├── role (user/assistant)
├── content
└── created_at
```

### 4. Demirbaş/Envanter

```sql
-- Envanter şablonları
inventory_templates
├── name
├── property_type_id
└── created_at

-- Şablon öğeleri
inventory_template_items
├── template_id
├── room_name
├── item_name
├── quantity
└── created_at

-- Mülk envanter öğeleri
property_inventory_items
├── property_id
├── room_name
├── item_name
├── quantity
├── condition
└── created_at
```

---

## 🤖 AI SİSTEMİ VE ROLLERİ

### 1. İlan Taslak Asistanı

**Akış:**
```
Danışman (Telegram) → Sesli/Yazılı Anlatım
    ↓
n8n → AnythingLLM → İlan JSON Taslağı
    ↓
Laravel → listings tablosuna status = 'draft' kaydet
    ↓
Danışman → Onaylar / Düzeltir / Yayınlar
```

**Kod Gereksinimleri:**
```php
// Migration
Schema::create('draft_listings', function (Blueprint $table) {
    $table->id();
    $table->text('ai_response')->nullable();
    $table->string('status')->default('draft'); // draft, pending_review, approved, published
    $table->string('ai_model_used')->nullable();
    $table->string('ai_prompt_version')->nullable();
    $table->timestamp('ai_generated_at')->nullable();
    $table->foreignId('approved_by')->nullable();
    $table->timestamp('approved_at')->nullable();
    $table->timestamps();
});
```

### 2. Arsa/Piyasa Analiz Asistanı

**Akış:**
```
CRM'de Arsa Kaydı → "AI Analiz" Butonu
    ↓
n8n → yalihan_market + yalihan_ai DB'lerinden veri topla
    ↓
AnythingLLM → Fiyat bandı, emsal analizi, risk ve not üret
    ↓
Laravel → ai_land_plot_analyses tablosuna kaydet
    ↓
Danışman → PDF veya ekran raporu olarak kullanır
```

### 3. Mesaj Taslak Asistanı

**Akış:**
```
Instagram DM / Web Form / E-posta / Telegram Mesajı Gelir
    ↓
AI → Müşterinin niyetini, bütçesini, talebini analiz eder
    ↓
Danışman İçin Cevap Taslağı Üretir:
    - WhatsApp/Instagram/Email metni
    - Uygun portföy önerileri (DB'den çekilmiş)
    ↓
Laravel → DB'de sakla (ai_messages, communications, draft_replies)
    ↓
Danışman → Onaylar / Düzeltir / Gönderir
```

**KRİTİK:** İnsan onayı olmadan ASLA gönderilmez!

### 4. Demirbaş/Envanter Asistanı

**Akış:**
```
Danışman (Telegram) → "Salon: bir üçlü koltuk, iki tekli koltuk, bir TV kumandası"
    ↓
n8n + AnythingLLM → JSON envanter listesine çevir
    ↓
Laravel → property_inventory_items tablosunda sakla
    ↓
İç kullanıcıya gösterilir (müşteriye açılmaz)
```

---

## 💻 KOD YAZMA STANDARTLARI

### 1. Laravel Kod Yapısı

```php
// ✅ DOĞRU - Service Layer Kullanımı
class IlanController extends Controller
{
    public function __construct(
        private IlanService $ilanService
    ) {}
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'baslik' => 'required|string|max:255',
            'fiyat' => 'required|numeric',
            'status' => 'required|in:draft,active,sold',
        ]);
        
        $ilan = $this->ilanService->create($validated);
        
        return ResponseService::success([
            'data' => $ilan,
            'message' => 'İlan başarıyla oluşturuldu'
        ]);
    }
}

// ❌ YANLIŞ - Controller'da iş mantığı
class IlanController extends Controller
{
    public function store(Request $request)
    {
        $ilan = new Ilan();
        $ilan->baslik = $request->baslik;
        $ilan->fiyat = $request->fiyat;
        $ilan->enabled = 1; // ❌ YASAK: enabled kullanımı
        $ilan->save();
        
        return response()->json($ilan);
    }
}
```

### 2. Model Standartları

```php
// ✅ DOĞRU
class Ilan extends Model
{
    protected $table = 'ilanlar';
    
    protected $fillable = [
        'baslik',
        'fiyat',
        'status', // ✅ DOĞRU: status kullanımı
        'display_order', // ✅ DOĞRU: display_order kullanımı
        'il_id', // ✅ DOĞRU: il_id kullanımı
    ];
    
    protected $casts = [
        'status' => 'string',
        'display_order' => 'integer',
        'created_at' => 'datetime',
    ];
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    // Relations
    public function il()
    {
        return $this->belongsTo(Il::class);
    }
}
```

### 3. Migration Standartları

```php
// ✅ DOĞRU
Schema::create('ilanlar', function (Blueprint $table) {
    $table->id();
    $table->string('baslik');
    $table->decimal('fiyat', 12, 2);
    $table->string('status')->default('draft'); // ✅ DOĞRU
    $table->integer('display_order')->default(0); // ✅ DOĞRU
    $table->foreignId('il_id')->constrained('iller'); // ✅ DOĞRU
    $table->timestamps();
    
    // Indexes
    $table->index('status');
    $table->index('display_order');
    $table->index('il_id');
});

// ❌ YANLIŞ
Schema::create('ilanlar', function (Blueprint $table) {
    $table->id();
    $table->string('baslik');
    $table->decimal('fiyat', 12, 2);
    $table->boolean('enabled')->default(true); // ❌ YASAK
    $table->integer('order')->default(0); // ❌ YASAK
    $table->foreignId('sehir_id')->constrained('sehirler'); // ❌ YASAK
    $table->timestamps();
});
```

---

## 🎨 FRONTEND STANDARTLARI

### 1. Tailwind CSS Kullanımı

```html
<!-- ✅ DOĞRU - Pure Tailwind -->
<button 
    class="px-4 py-2 bg-blue-600 text-white rounded-lg 
           hover:bg-blue-700 active:scale-95
           transition-all duration-200 
           dark:bg-blue-700 dark:hover:bg-blue-800
           focus:ring-2 focus:ring-blue-500 focus:outline-none"
>
    Kaydet
</button>

<!-- ❌ YANLIŞ - Neo Design -->
<button class="neo-btn neo-btn-primary">
    Kaydet
</button>
```

### 2. Zorunlu CSS Kuralları

```css
/* 1. Her interaktif element transition içermeli */
transition-all duration-200

/* 2. Dark mode variant'ları ZORUNLU */
dark:bg-gray-800 dark:text-white dark:border-gray-700

/* 3. Focus state'leri ZORUNLU (accessibility) */
focus:ring-2 focus:ring-blue-500 focus:outline-none

/* 4. Hover efektleri ZORUNLU */
hover:bg-blue-700 hover:shadow-lg

/* 5. Active state'ler ZORUNLU */
active:scale-95
```

### 3. Form Standartları

```html
<!-- Input -->
<input 
    type="text"
    class="w-full px-4 py-2.5 
           border border-gray-300 rounded-lg 
           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
           transition-all duration-200
           dark:bg-gray-800 dark:border-gray-700 dark:text-white
           dark:focus:ring-blue-600"
    placeholder="Ad Soyad"
/>

<!-- Select -->
<select 
    class="w-full px-4 py-2.5 
           border border-gray-300 rounded-lg 
           cursor-pointer
           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
           transition-all duration-200
           dark:bg-gray-900 dark:border-gray-700 dark:text-white
           dark:focus:ring-blue-600"
    style="color-scheme: light dark;"
>
    <option value="">Seçiniz</option>
    <option value="1">Seçenek 1</option>
</select>
```

### 4. Component Standartları

```html
<!-- Card Component -->
<div 
    class="bg-white rounded-xl shadow-lg 
           border border-gray-200 
           p-6
           transition-all duration-300 
           hover:shadow-xl hover:scale-[1.02]
           dark:bg-gray-800 dark:border-gray-700"
>
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
        Başlık
    </h3>
    <p class="text-gray-600 dark:text-gray-400">
        İçerik
    </p>
</div>
```

---

## 🌐 API VE SERVİS STANDARTLARI

### 1. Response Format

```php
// Başarılı response
return ResponseService::success([
    'data' => $data,
    'message' => 'İşlem başarılı'
], 200);

// Hata response
return ResponseService::error(
    'Hata mesajı',
    ['field' => 'Detaylı hata'],
    422
);

// Server error
return ResponseService::serverError('Sunucu hatası', $exception);
```

### 2. Cache Kullanımı

```php
// Cache'e kaydet
CacheHelper::remember('namespace', 'key', 'medium', function() {
    return $data;
});

// Cache'den al
$data = CacheHelper::get('namespace', 'key');

// Cache'i temizle
CacheHelper::forget('namespace', 'key');
```

### 3. Logging

```php
// Genel log
LogService::info('Bilgi mesajı', ['context' => $data]);
LogService::error('Hata mesajı', ['context' => $data], $exception);

// API log
LogService::api('/api/endpoint', $requestData, $responseData, $duration);

// Database log
LogService::database('insert', 'table_name', $data, $affectedRows);

// Auth log
LogService::auth('login', $userId, ['ip' => $ip]);
```

---

## 🚫 YASAKLI PATTERN'LER

### Database

```php
// ❌ YASAK
'order' => 1
'enabled' => true
'is_active' => 1
'aktif' => 1
'durum' => 'aktif'
'sehir_id' => 1
'musteri_id' => 1

// ✅ DOĞRU
'display_order' => 1
'status' => 'active'
'status' => 'active'
'status' => 'active'
'status' => 'active'
'il_id' => 1
'kisi_id' => 1
```

### CSS

```html
<!-- ❌ YASAK -->
<div class="neo-btn">Button</div>
<div class="neo-card">Card</div>
<div class="btn-primary">Button</div>
<div class="form-control">Input</div>

<!-- ✅ DOĞRU -->
<div class="px-4 py-2 bg-blue-600 text-white rounded-lg">Button</div>
<div class="bg-white rounded-xl shadow-lg p-6">Card</div>
<div class="px-4 py-2 bg-blue-600 text-white rounded-lg">Button</div>
<input class="w-full px-4 py-2.5 border rounded-lg">
```

### Routes

```php
// ❌ YASAK - Double prefix
Route::name('admin.admin.dashboard');
Route::name('crm.crm.kisi');

// ✅ DOĞRU
Route::name('admin.dashboard');
Route::name('admin.kisi');
```

---

## ⚡ HIZLI BAŞLANGIÇ KOMUTLARI

### Development

```bash
# Sunucuları başlat
php artisan serve                    # Laravel server (http://127.0.0.1:8000)
node mcp-servers/yalihan-bekci-mcp.js  # Yalıhan Bekçi MCP
node mcp-servers/context7-validator-mcp.js  # Context7 Validator
node mcp-servers/laravel-mcp.cjs     # Laravel MCP

# Asset build
npm run dev                          # Development mode
npm run build                        # Production build

# Database
php artisan migrate                  # Run migrations
php artisan migrate:rollback         # Rollback last migration
php artisan db:seed                  # Run seeders

# Cache
php artisan cache:clear              # Clear cache
php artisan config:clear             # Clear config cache
php artisan route:clear              # Clear route cache
php artisan view:clear               # Clear view cache
```

### Context7 Validation

```bash
# Yasaklı pattern kontrolü
grep -r "order\|aktif\|enabled\|is_active" --include="*.php" app/

# Neo Design kontrolü
grep -r "neo-btn\|neo-card\|neo-input" resources/views/

# Pre-commit hook çalıştır
.githooks/context7-pre-commit

# Yalıhan Bekçi raporu
php artisan yalihan-bekci:report
```

### Code Quality

```bash
# PHPStan analizi
./vendor/bin/phpstan analyse

# PHP CS Fixer
./vendor/bin/php-cs-fixer fix --dry-run

# Pint (Laravel code style)
./vendor/bin/pint --test
```

---

## 📚 DOKÜMANTASYON REFERANSLARI

### Aktif Standartlar
- `.context7/authority.json` - Context7 kuralları
- `docs/FORM_STANDARDS.md` - Form standartları
- `docs/active/RULES_KONSOLIDE_2025_11_25.md` - Konsolide kurallar
- `YALIHAN_BEKCI_EGITIM_DOKUMANI.md` - Yalıhan Bekçi eğitimi

### Proje Dokümantasyonu
- `README.md` - Proje genel bakış
- `docs/index.md` - Dokümantasyon merkezi
- `MASTER_PROMPT_YALIHAN_EMLAK_AI.md` - AI master prompt
- `docs/ai-training/` - AI eğitim dokümanları

### Modül Dokümantasyonu
- `docs/modules/` - Modül detayları
- `docs/technical/` - Teknik dokümantasyon
- `docs/api/` - API dokümantasyonu

---

## 🎯 AI İÇİN ÖNEMLİ NOTLAR

### 1. Kod Üretirken Kontrol Listesi

- [ ] Context7 kurallarına uygun mu?
- [ ] Yasaklı pattern kullanılmış mı?
- [ ] Dark mode variant'ları var mı?
- [ ] Transition'lar eklenmiş mi?
- [ ] Focus state'ler var mı?
- [ ] AI çıktıları için onay mekanizması var mı?
- [ ] Database field isimleri doğru mu?
- [ ] Service layer kullanılmış mı?
- [ ] Response format standartlara uygun mu?
- [ ] Log mekanizması eklenmiş mi?

### 2. Önce Sor, Sonra Yaz

Kod yazmadan önce şunları kontrol et:
1. Bu işi mevcut stack ile yapabilir miyim?
2. Yeni SaaS/servis gerekli mi?
3. Context7 kurallarına uygun mu?
4. AI onay mekanizması gerekli mi?

### 3. Mantra

> **"Bu projede yazdığım her kod, Yalıhan Emlak'ın gerçek sahadaki işini kolaylaştırmalı; mevcut teknoloji ile gerçekten yapılabilir olmalı; AI sadece taslak ve öneri üretmeli; son söz her zaman insanda kalmalı; ve tüm bu süreçler Context7 standartlarına uygun olmalı."**

---

**Son Güncelleme:** 29 Kasım 2025  
**Versiyon:** 2.0.0  
**Durum:** ✅ Aktif ve Güncel  
**Context7 Version:** 6.0.0

---

Made with ❤️ by Yalıhan Emlak Team
