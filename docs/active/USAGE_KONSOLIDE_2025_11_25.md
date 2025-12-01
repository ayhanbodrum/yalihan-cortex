# 📘 EmlakPro Kullanım Rehberleri - Konsolide Dokümantasyon

**Son Güncelleme:** 25 Kasım 2025  
**Context7 Standardı:** C7-USAGE-KONSOLIDE-2025-11-25  
**Kapsamlı Kullanım Kılavuzu:** 6 Rehber Birleştirildi

---

## 📋 İÇİNDEKİLER

1. [CLI Rehberi](#cli-rehberi)
2. [Komutlar Rehberi](#komutlar-rehberi)
3. [Stable-Create Kullanım Rehberi](#stable-create-kullanim-rehberi)
4. [USTA 4.0 Kullanım Rehberi](#usta-40-kullanim-rehberi)
5. [USTA Web Developer Özeti](#usta-web-developer-ozeti)
6. [USTA Auto Fix Rehberi](#usta-auto-fix-rehberi)

---

## 💻 CLI REHBERİ

### Laravel Artisan Commands

#### Migration Commands

```bash
# Context7 uyumlu migration oluşturma
php artisan make:migration:context7 create_table_name --create=table_name

# Migration çalıştırma
php artisan migrate

# Migration rollback
php artisan migrate:rollback

# Migration status
php artisan migrate:status
```

#### Context7 Commands

```bash
# Context7 compliance kontrolü
php artisan context7:validate-migration --all

# Context7 otomatik düzeltme
php artisan context7:validate-migration --auto-fix

# Context7 health check
php artisan context7:health-check

# Context7 daily report
php artisan context7:daily-check
```

#### Cache Commands

```bash
# Tüm cache temizleme
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache rebuild
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Database Commands

```bash
# Database seed
php artisan db:seed

# Specific seeder
php artisan db:seed --class=IlanSeeder

# Fresh migration with seed
php artisan migrate:fresh --seed
```

---

## ⚡ KOMUTLAR REHBERİ

### Development Workflow

#### Daily Development Start

```bash
# 1. MySQL başlatma (MAMP)
# VS Code: Ctrl+Shift+P → "Tasks: Run Task" → "Start MySQL (MAMP)"

# 2. Laravel server başlatma
php artisan serve --port=8002

# 3. Asset build (development)
npm run dev

# 4. Context7 compliance check
php artisan context7:validate-migration --all
```

#### Pre-Commit Checklist

```bash
# 1. Context7 compliance
php artisan context7:validate-migration --all

# 2. Code style check
./vendor/bin/phpcs --standard=PSR12 app/

# 3. Tests
php artisan test

# 4. Asset build
npm run build
```

#### Production Deploy

```bash
# 1. Migration
php artisan migrate --force

# 2. Cache optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Assets
npm run build

# 4. Queue restart
php artisan queue:restart
```

### VS Code Tasks

**Kullanılabilir Tasks:**

- `Start MySQL (MAMP)`
- `Start Laravel Server`
- `Laravel: Run Migrations`
- `Context7: Validate All`
- `Context7: Auto Fix`
- `Assets: Build Production`
- `Assets: Watch Development`

**Kullanım:**

- `Ctrl+Shift+P`
- "Tasks: Run Task" seçin
- İlgili task'ı seçin

---

## 🏗️ STABLE-CREATE KULLANIM REHBERİ

### İlan Create Sayfası Modern Kullanımı

#### Form Section Sırası (Önerilen)

```yaml
1. Kategori Sistemi (İLK ÖNCE)
- Ana kategori seçimi
- Alt kategori otomatik yükleme
- Yayın tipi belirleme

2. Lokasyon ve Harita
- İl/İlçe/Mahalle hiyerarşik seçim
- Leaflet.js harita entegrasyonu
- Koordinat belirleme

3. Fiyat Yönetimi (ERKEN)
- Ana fiyat girişi
- Para birimi seçimi
- Sezonluk fiyat (yazlık için)

4. Fotoğraf Upload (ERKEN)
- Ana fotoğraf yükleme
- Galeri fotoğrafları
- Drag & drop sıralama

5. Temel Bilgiler
- Başlık (AI önerili)
- Açıklama
- Metrekare bilgileri

6. İlan Özellikleri
- Field dependencies
- Kategori-özel alanlar
- Özellik seçimleri
```

#### Alpine.js Components

```javascript
// Form state management
window.ilanCreateForm = function () {
    return {
        formData: {
            ana_kategori_id: null,
            alt_kategori_id: null,
            il_id: null,
            ilce_id: null,
            mahalle_id: null,
        },

        // Kategori değişimi
        kategoriChanged() {
            this.loadAltKategoriler();
            this.resetFormFields();
        },

        // Alt kategori yükleme
        async loadAltKategoriler() {
            const response = await fetch(
                `/api/kategoriler/${this.formData.ana_kategori_id}/alt-kategoriler`
            );
            this.altKategoriler = await response.json();
        },
    };
};
```

#### Context7 Uyum Kuralları

- ✅ Tailwind CSS kullanımı zorunlu
- ❌ Neo Design System yasak
- ✅ `status` field kullanımı (enabled yasak)
- ✅ `is_published` boolean field
- ✅ Alpine.js preferred over jQuery

---

## 🤖 USTA 4.0 KULLANIM REHBERİ

### USTA AI Assistant Features

#### İlan Taslak Asistanı

```javascript
// USTA ile ilan taslağı oluşturma
const ilanTaslagi = await USTA.createIlanDraft({
    kategori: 'konut',
    tip: 'daire',
    lokasyon: 'Bodrum Merkez',
    ozellikler: {
        oda_sayisi: 3,
        salon_sayisi: 1,
        banyo_sayisi: 2,
        metrekare: 120,
    },
});

// Otomatik başlık önerisi
const baslikOneri = await USTA.suggestTitle(ilanTaslagi);

// Açıklama metni oluşturma
const aciklama = await USTA.generateDescription(ilanTaslagi);
```

#### AI Provider Configuration

```php
// config/ai.php
'default' => env('AI_DEFAULT_PROVIDER', 'openai'),

'providers' => [
    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model' => 'gpt-4',
        'max_tokens' => 2000
    ],
    'deepseek' => [
        'api_key' => env('DEEPSEEK_API_KEY'),
        'model' => 'deepseek-chat',
        'max_tokens' => 4000
    ],
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => 'gemini-pro',
        'max_tokens' => 8000
    ]
]
```

#### USTA Prompt Templates

```yaml
İlan Başlık Template:
"Bu {kategori} {tip} ilanı için satış/kiralık durumuna uygun,
SEO dostu ve çekici bir başlık öner. Lokasyon: {lokasyon},
Özellikler: {ozellikler}"

Açıklama Template:
"Bu emlak ilanı için detaylı, profesyonel ve çekici bir açıklama yaz.
İlan Bilgileri: {ilan_data}
Ton: Professional ve güvenilir
Uzunluk: 150-300 kelime"
```

---

## 👨‍💻 USTA WEB DEVELOPER ÖZETİ

### Developer Quick Reference

#### Project Structure

```
app/Modules/
├── Admin/          # Admin panel
├── Auth/           # Authentication
├── Emlak/          # Core property system
├── Talep/          # Request management
├── ArsaModulu/     # Land module
├── Analitik/       # Analytics
├── CRMSatis/       # CRM & Sales
├── Finans/         # Finance
└── TakimYonetimi/  # Team management
```

#### Key Files & Locations

```yaml
Routes:
    - routes/web.php # Web routes
    - routes/api.php # API routes
    - routes/admin.php # Admin routes
    - routes/ai.php # AI routes

Config:
    - config/context7.php # Context7 settings
    - config/ai.php # AI providers
    - config/app.php # App settings

Database:
    - database/migrations/ # Migration files
    - database/seeders/ # Seed files
    - database/factories/ # Model factories

Frontend:
    - resources/views/ # Blade templates
    - resources/js/ # JavaScript files
    - resources/css/ # CSS files
    - public/css/ # Compiled CSS
    - public/js/ # Compiled JS
```

#### Development Standards

- **CSS:** Sadece Tailwind CSS (Neo Design yasak)
- **JS:** Alpine.js preferred (jQuery minimize)
- **PHP:** PSR-12 coding standard
- **Database:** Context7 field naming
- **Testing:** PHPUnit for backend, Vitest for frontend

---

## 🔧 USTA AUTO FIX REHBERİ

### Automated Fixes

#### Context7 Auto-Fix

```bash
# Context7 compliance otomatik düzeltme
php artisan context7:validate-migration --auto-fix

# Migration syntax düzeltme
./scripts/fix-migrations.sh

# Pre-commit hook kurulum
pre-commit install
pre-commit run --all-files
```

#### Common Fix Patterns

#### enabled → status Field Fix

```php
// Otomatik migration
Schema::table('table_name', function (Blueprint $table) {
    $table->enum('status', ['active', 'passive', 'archived'])
          ->default('active')->after('id');

    DB::statement("UPDATE table_name SET status = CASE
        WHEN enabled = 1 THEN 'active'
        ELSE 'passive'
    END");

    $table->dropColumn('enabled');
});
```

#### jQuery → Alpine.js Migration

```javascript
// Eski jQuery kodu
$('#kategori-select').on('change', function() {
    const kategoriId = $(this).val();
    loadAltKategoriler(kategoriId);
});

// Yeni Alpine.js kodu
<div x-data="{ kategoriId: null }"
     x-init="$watch('kategoriId', value => loadAltKategoriler(value))">
    <select x-model="kategoriId">
        <!-- options -->
    </select>
</div>
```

#### Neo Design → Tailwind CSS Migration

```html
<!-- Eski Neo Design -->
<button class="neo-btn neo-btn-primary">Kaydet</button>
<div class="neo-card">Content</div>

<!-- Yeni Tailwind CSS -->
<button
    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition-all duration-200"
>
    Kaydet
</button>
<div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6">Content</div>
```

### VS Code Auto-Fix Tasks

```yaml
Available Auto-Fix Tasks:
    - 'Context7: Auto Fix'
    - 'Migration: Auto Fix Syntax'
    - 'Laravel: Clear Cache'
    - 'Assets: Build Production'

Usage:
    Ctrl+Shift+P → Tasks: Run Task → Select task
```

### Git Pre-commit Hooks

```yaml
Enabled Hooks:
    - Context7 compliance check
    - PHP syntax check
    - JavaScript/CSS linting
    - Secret detection
    - SQL injection check

Hook Configuration:
    File: .pre-commit-config.yaml
    Activation: pre-commit install
    Manual Run: pre-commit run --all-files
```

---

## 📚 KAYNAK DOSYALAR (BİRLEŞTİRİLDİ)

Bu dokümanda şu dosyalar birleştirilmiştir:

1. `docs/usage/CLI_GUIDE.md`
2. `docs/usage/KOMUTLAR_REHBERI.md`
3. `docs/usage/STABLE-CREATE-KULLANIM-REHBERI.md`
4. `docs/usage/USTA_4.0_KULLANIM_REHBERI.md`
5. `docs/usage/USTA_4.0_WEB_DEVELOPER_OZET.md`
6. `docs/usage/USTA_AUTO_FIX_GUIDE.md`

**Context7 Compliance:** ✅ C7-USAGE-KONSOLIDE-2025-11-25  
**Tarih:** 25 Kasım 2025
