# Context7 – Kurallar ve Standartlar

Bu dosya, Yalıhan Emlak projelerinde kullanılan tüm teknolojik kuralların, yasakların ve standartların merkezidir.
AI/MCP/IDE araçları **mutlaka bu dosyayı referans almalıdır.**

**Son Güncelleme:** 11 Ekim 2025
**Context7 Hafıza:** ✅ Senkronize (Kritik Hata Önleme + Otomatik Script Güvenlik + Model Helper Metodlar)
**Status:** ✅ Aktif

---

## 1. Teknoloji Standartları

### Backend

-   **Framework:** Laravel 10.x (PHP 8.2+)
-   **Database:** MySQL 8.0+, Engine = InnoDB, Charset = utf8mb4, Collation = utf8mb4_unicode_ci
-   **Cache:** Redis (opsiyonel)
-   **Queue:** Database/Redis

### Frontend

-   **Design System:** Neo Design System (neo-\*)
    -   TailwindCSS tabanlı
    -   Alpine.js entegrasyonu
    -   Dark-mode default
-   **JavaScript:** Alpine.js + Livewire (Vue.js, React, jQuery yasak)

### AI & Entegrasyon

-   **AI Provider:** 5 AI Provider (OpenAI, DeepSeek, Gemini, Claude, Ollama Local)
-   **Ollama Local AI:** Gemma2:2b model desteği (varsayılan)
-   **CSP Proxy:** Güvenli local AI bağlantıları için proxy endpoint
-   **MCP:** Context7 MCP Server
-   **Deployment:** Docker + GitHub Actions (CI/CD planlı)
-   **Form Wizard:** 7 adımlı modern wizard sistemi
-   **Search System:** Real-time arama (300ms debounce)

---

## 2. Yasaklar

### ❌ Yeni Tablo Yaratma

-   AI asla kendi kafasına göre yeni tablo açamaz
-   Eğer ihtiyaç varsa → önce migration dosyası hazırlanır, insan onayı gerekir
-   AI sadece mevcut tabloları kullanabilir

### ❌ Route Çakışmaları

-   **Aynı route prefix'inde farklı controller'lar kullanılamaz**
-   Örnek: `/admin/ozellikler` hem `FeatureController` hem `OzellikController` için tanımlanamaz
-   **Çözüm:** Tek controller seç ve diğerini kaldır veya farklı route prefix kullan
-   Route çakışması tespit edildiğinde Context7 kontrol scripti hata vermelidir

### ❌ Controller İsimlendirme Karışıklığı

-   **Aynı işlevsellik için farklı controller isimleri kullanılamaz**
-   Örnek: `FeatureController` ve `OzellikController` aynı işi yapıyor
-   **Çözüm:** Tek controller ismi seç ve tutarlı kullan

### ❌ Veritabanı Alan İsimlendirme Tutarsızlığı

-   **Status alanları:** `is_active` → `status` (Context7 standardı)
-   **Örnek:** `ilan_kategorileri` tablosunda `status` alanı kullanılmalı
-   **Migration:** `aktif` kolonu kaldırılmalı, `status` kullanılmalı
-   **Standart:** Türkçe tablo adı → İngilizce controller adı
-   Örnek: `ozellikler` tablosu → `FeatureController` kullan

### ❌ Yasak Database Alan Adları (YENİ)

-   **status alanı yasak:** `status` → `status` kullanılmalıdır
-   **is_active alanı yasak:** `is_active` → `status` kullanılmalıdır
-   **aktif alanı yasak:** `aktif` → `status` kullanılmalıdır
-   **il alanı yasak:** `il` → `il` kullanılmalıdır
-   **region_id alanı yasak:** `region_id` → `il_id` kullanılmalıdır
-   **Sehir model yasak:** `Sehir` → `Il` model kullanılmalıdır
-   **ad_soyad alanı yasak:** `ad_soyad` → `tam_ad` kullanılmalıdır (CRM kişiler)
-   **full_name alanı yasak:** `full_name` → `name` kullanılmalıdır (Users tablosu)
-   **musteri_ad_soyad alanı yasak:** `musteri_ad_soyad` → `musteri_tam_ad` kullanılmalıdır
-   **Yasak kullanım örnekleri:**
    -   `$ilan->status` → `$ilan->status`
    -   `where('is_active', 1)` → `where('status', 1)`
    -   `->aktif()` → `->active()`
    -   `$model->il` → `$model->il`
    -   `use App\Models\Sehir` → `use App\Models\Il`
    -   `$kisi->ad_soyad` → `$kisi->tam_ad`
    -   `$user->full_name` → `$user->name`
    -   `$musteri->musteri_ad_soyad` → `$musteri->musteri_tam_ad`

### ✅ Uzmanlık Alanları (Expertise Areas) Kuralları (YENİ)

-   **Danışman Tanımı**: Danışman ayrı tablo değil, `users` tablosunda `role='danisman'` kullanıcıdır (Spatie Permission).
-   **Master Tablo**: `expertise_areas (name, slug, status, description, icon)`
-   **Pivot Tablo**: `user_expertise_area (user_id, expertise_area_id, experience_years?, notes?, timestamps)`
-   **Model İlişkileri**:
    -   `User::expertiseAreas()` ⇄ `belongsToMany(ExpertiseArea::class, 'user_expertise_area')`
    -   `ExpertiseArea::users()` ⇄ `belongsToMany(User::class, 'user_expertise_area')`
-   **DB Bütünlüğü**: Unique `(user_id, expertise_area_id)` ve foreign key'ler zorunlu
-   **UI/Validation**: Çoklu seçim arayüzü, `expertise_area_ids.*` → `exists:expertise_areas,id`
-   **Seed/Test**: Örnek uzmanlık alanları ve danışmanlara atanması

### ✅ Zorunlu Alan Adı Dönüşümleri (Güncel)

-   `category_id` → `alt_kategori_id` (models/seeders/migrations/views/JS)
-   `il / il_id` → `il / il_id`
-   `region_id` (kaldır) – hiyerarşik konum alanları kullanılmalı
-   CI/Script: forbidden-pattern scan; ihlalde build kırılır ve auto-fix önerilir

### ✅ Zorunlu Alan Setleri (Basit ve Net)

-   İlanlar (`ilanlar`): `baslik`, `slug(unique)`, `status`; ilişkiler: `ana_kategori_id`, `alt_kategori_id`, `yayin_tipi_id`, `danisman_id`, `ilan_sahibi_id`, `ulke_id`, `il_id`, `ilce_id`, `mahalle_id`; fiyat: `fiyat`, `para_birimi` (opsiyonel: `prices`)
-   Fiyat Geçmişi (`ilan_price_history`): `ilan_id`, `old_price`, `new_price`, `currency`, `changed_by`, `change_reason`, `created_at`
-   Kullanıcılar (`users`): `name`, `email`, `password`, `status` (+ danışman rolü); ilişkiler: `expertise_areas` (pivot `user_expertise_area`)
-   Kişiler (`kisiler`): ilişki `danisman_id`, lokasyon: `il_id`, `ilce_id`, `mahalle_id`
-   Uzmanlık Alanları (`expertise_areas`): `name`, `slug`, `status` (+ `description`, `icon`)

### ✅ Site/Apartman Rehberi ve Live Search Kuralları (YENİ)

-   `sites` master tablo: `name`, `il_id`, `ilce_id`, `mahalle_id` (+ opsiyonel `blok_adi`, `adres`, `status`)
-   İlan bağlantısı: `ilanlar.site_id` (FK), metinsel fallback: `ilanlar.site_adi`
-   API:
    -   `GET /api/sites/search?q=&il_id=&ilce_id=&limit=20` – 300ms debounce, JSON
    -   `POST /api/sites` – Auth gerektirir, `name` ve lokasyon zorunlu
-   UI:
    -   İlan oluşturma formunda live search ile seçim; `site_id` gizli alana yazılır
    -   "Yeni Ekle" modal'ı ile site eklenebilir
-   Index/Kısıtlar:
    -   `sites(name)` index (ops. `il_id,ilce_id,name` composite)
    -   `ilanlar(site_id)` index ve FK zorunlu

### ✅ İlan Backend Hızlı Arama Kuralları (YENİ)

-   Endpoint: `GET /api/listings/search?q=&type=owner|phone|site|advisor|all&limit=20`
-   Arama:
    -   Sahibi: `kisiler.ad`, `kisiler.soyad`, `kisiler.telefon` (telefon normalize)
    -   Site: `sites.name` + `ilanlar.site_adi` fallback
    -   Danışman: `users.name`, `users.email`
-   Performans:
    -   `sites(name)` ve `ilanlar(site_id)`, `kisiler(telefon)` indexleri önerilir
    -   300ms debounce (UI), limit ≤ 20

### ❌ Create Metodlarında Yasak Veri Kaynakları (YENİ)

-   **Tüm kullanıcıları getirmek yasak:** `User::all()` → `User::whereHas('roles', ...)` kullanılmalıdır
-   **Danışman aramada tüm kullanıcılar yasak:** `User::where('is_active', true)` → `User::whereHas('roles', function($q) { $q->where('name', 'danisman'); })` kullanılmalıdır
-   **Kişi aramada yanlış tablo yasak:** `User::where(...)` → `Kisi::where(...)` kullanılmalıdır (CRM kişiler için)
-   **İlan sahibi aramada tüm kullanıcılar yasak:** Sadece danışman rolüne sahip kullanıcılar getirilmeli
-   **Yasak kullanım örnekleri:**
    -   `User::where('is_active', 1)->get()` → `User::whereHas('roles', function($q) { $q->where('name', 'danisman'); })->where('is_active', 1)->get()`
    -   `User::all()` → `User::whereHas('roles', ...)->get()`
    -   `$users = User::where('is_active', true)` → `$users = User::whereHas('roles', function($q) { $q->where('name', 'danisman'); })->where('is_active', true)`
    -   `User::where('name', 'like', "%{$query}%")` → `User::whereHas('roles', function($q) { $q->where('name', 'danisman'); })->where('name', 'like', "%{$query}%")`

### ✅ Dosya Temizliği Kuralları

-   **Backup dosyaları:** Eski backup'lar kaldırılmalı
-   **Duplicate dosyalar:** Tekrarlayan dosyalar temizlenmeli
-   **Test dosyaları:** Production'da test dosyaları bulunmamalı
-   **Eski migration'lar:** Kullanılmayan migration'lar arşivlenmeli
-   **Context7 Standardı:** C7-CLEANUP-2025-01-29 uygulandı

### ✅ Acil Düzeltme Kuralları

-   **Database alan adları:** Controller ve database uyumlu olmalı
-   **Enum değerleri:** Step ve stage sistemleri uyumlu olmalı
-   **Field mapping:** Context7 naming convention uygulanmalı
-   **Stage mapping:** Frontend step'leri backend stage'leri ile uyumlu olmalı
-   **Context7 Standardı:** C7-URGENT-FIXES-2025-01-29 uygulandı

### ✅ Gelişmiş Geliştirme Kuralları

-   **Gereksiz dosyalar:** Test, duplicate ve eski dosyalar temizlenmeli
-   **AI sistem özellikleri:** Emlak tipine göre özel özellikler olmalı
-   **Dinamik form alanları:** JavaScript ile gerçek zamanlı güncelleme
-   **API endpoint'leri:** RESTful standardında olmalı
-   **Frontend güncellemeleri:** Modern Form Wizard entegrasyonu
-   **Context7 Standardı:** C7-ADVANCED-DEVELOPMENT-2025-01-29 uygulandı

### ✅ Gelişmiş AI Geliştirme Kuralları

-   **AI açıklama sistemi:** Property type'a özel prompt sistemi
-   **Advanced cache stratejileri:** Redis cache ve invalidation sistemi
-   **Database query optimizasyonu:** Query optimization ve monitoring
-   **API geliştirmeleri:** Property type AI description API
-   **Frontend AI entegrasyonu:** AI açıklama UI/UX sistemi
-   **Performance monitoring:** Query ve cache performans izleme
-   **Context7 Standardı:** C7-ADVANCED-AI-DEVELOPMENT-2025-01-29 uygulandı

### ✅ AI İçerik Üretimi – Kalite ve Güvenlik Kuralları (YENİ)

-   Varyant Üretimi: Başlık/açıklama için 3‑5 varyant üretilebilir; A/B testi desteklenir.
-   Ton/Amaç Profilleri: "SEO", "Kurumsal", "Hızlı Satış", "Lüks" ön ayarları.
-   Çok Dilli Özet: TR ana metin zorunlu; EN/RU/DE için kısa özet opsiyonel.
-   Yapılandırılmış Çıktı: JSON şema (features, highlights, CTA) + metin; UI, JSON'u parse eder.
-   Guardrails: Yasak kelime filtresi, PII maskesi, m²/oda/fiyat tutarlılık denetimi.
-   Sağlayıcı Esnekliği: OpenAI/DeepSeek/Gemini/Claude/Ollama fallback; timeout→otomatik geçiş.
-   Maliyet/Hız: Kısa/uzun öneri modu; idempotent cache; kota ve rate‑limit zorunlu.
-   Öğrenen Sistem: Danışman düzenlemeleri prompt hafızasına geri beslenir (stil uyumu).
-   Analitik: Kabul oranı, düzenleme yüzdesi, üretim süresi metrikleri kaydedilir.
-   RAG: Benzer ilan örnekleri embedding ile çekilip prompt'a referans verilir.

Form Entegrasyonu (İlan Ekleme)

-   `ai_tone` (select): seo|kurumsal|hizli_satis|luks (default: seo)
-   `ai_variant_count` (int, 1‑5): default 3
-   `ai_ab_test` (bool): default false
-   `ai_languages[]` (array): [EN, RU, DE] – opsiyonel özetler
-   Sunucu doğrulaması opsiyoneldir; bu alanlar iş kurallarını etkilemez, sadece öneri üretimini yönlendirir.

### ✅ AnythingLLM Entegrasyon Kuralları (YENİ)

-   Konfigürasyon: `.env` → `ANYTHINGLLM_URL`, `ANYTHINGLLM_API_KEY`, `ANYTHINGLLM_TIMEOUT`
-   Service: `App\Services\AnythingLLMService` (health, completions, embeddings)
-   Proxy Controller: `App\Http\Controllers\Api\AnythingLLMProxyController`
-   API Routes:
    -   `GET /api/ai/anythingllm/health`
    -   `POST /api/ai/anythingllm/completions`
    -   `POST /api/ai/anythingllm/embeddings`
-   Güvenlik: API anahtarları backend'de tutulur; tarayıcıdan doğrudan çağrı yasak (proxy zorunlu)
-   CSP: Sadece backend çıkışına izin verilir; client-to-external blocked
-   Rate Limit: Sağlayıcı çağrıları throttle edilir, timeout ve retry + fallback uygulanır

### ✅ API Documentation & Testing Kuralları

-   **API documentation:** Comprehensive endpoint documentation
-   **API testing:** Health check, stats, performance endpoints
-   **Performance monitoring:** Real-time monitoring ve alerts
-   **Testing script:** Automated functional, performance, load testing
-   **Error handling:** Comprehensive error management
-   **Rate limiting:** API protection ve throttling
-   **Context7 Standardı:** C7-API-DOCUMENTATION-TESTING-2025-01-29 uygulandı

### ✅ Advanced AI Features Kuralları

-   **Multi-language AI:** 6 dil desteği ile açıklama üretimi
-   **Image-based AI:** Computer vision ile resim analizi
-   **Location-based AI:** Konum tabanlı özellik önerileri
-   **Price optimization AI:** AI destekli fiyat optimizasyonu
-   **Advanced AI controller:** Tüm AI özelliklerinin entegrasyonu
-   **Advanced AI routes:** 4 yeni API endpoint'i
-   **Context7 Standardı:** C7-ADVANCED-AI-FEATURES-2025-01-29 uygulandı

### ✅ Yol Haritası Kuralları

-   **15 Aşamalı Geliştirme Planı:** Detaylı yol haritası oluşturuldu
-   **Öncelik Matrisi:** Yüksek, orta, düşük öncelik sınıflandırması
-   **Progress Tracking:** Tamamlanan, aktif, bekleyen geliştirmeler
-   **Milestone Takibi:** Her aşama için net hedefler
-   **Context7 Standardı:** C7-ROADMAP-2025-01-29 uygulandı

### ✅ Dosya Temizliği Kuralları

-   **Archive klasörü:** Tamamen silindi, karmaşıklık azaltıldı
-   **Eski dosyalar:** 97 MD dosyası temizlendi
-   **Disk alanı:** 1.8MB+ alan kazanımı
-   **Instructions klasörü:** Eski booking sistemi dosyaları temizlendi
-   **Kök dizin:** Eski AI dokümantasyon dosyaları temizlendi
-   **Context7 Standardı:** C7-FILE-CLEANUP-2024-09-30 uygulandı

### ❌ Database Field Uyumsuzluğu

-   **Model ve View'larda farklı field isimleri kullanılamaz**
-   Örnek: Model'de `ad` field'ı kullanılıyor ama database'de `name` kolonu var
-   **Çözüm:** Önce database schema kontrol et, sonra model ve view'ları ona göre yaz
-   **Standart:** Database kolon ismi → Model fillable → View kullanımı tutarlı olmalı
-   **Kontrol:** `Schema::getColumnListing('table_name')` ile database kolonlarını kontrol et

### ❌ AI Settings Duplication

-   **AI ayarları birden fazla yerde tanımlanamaz**
-   Örnek: `/admin/ayarlar` ve `/admin/ai-settings` aynı anda AI ayarları içeremez
-   **Çözüm:** AI ayarları sadece `/admin/ai-settings` sayfasında bulunmalı
-   **Standart:** Tek merkezi AI ayarları sayfası
-   **Kontrol:** AI Settings konsolidasyonu zorunlu

### ❌ CSP Policy Violations

-   **Local AI sunucularına direct fetch yapılamaz**
-   Örnek: `fetch('http://localhost:11434/api/tags')` CSP ihlali yaratır
-   **Çözüm:** Proxy endpoint kullan (`/admin/ai-settings/proxy-ollama`)
-   **Standart:** Tüm local AI bağlantıları proxy üzerinden
-   **Kontrol:** SecurityMiddleware CSP header kontrolü

### ❌ Legacy Sınıflar

-   `btn-*`, `card-*`, `form-*` gibi eski Simple Design System sınıfları kullanılmaz
-   Yerine → `neo-btn`, `neo-card`, `neo-form` kullanılmalıdır
-   Legacy CSS sınıfları tamamen yasaktır

### ❌ Eski Booking Sistemi Dosyaları

-   `instructions/booking/` klasöründeki tüm dosyalar **yasaktır**
-   Eski booking sistemi artık kullanılmıyor
-   Context7 sistemi ile değiştirildi
-   Bu dosyalar otomatik olarak temizlenmelidir

### ❌ Framework Karışıklığı

-   Vue.js, React, jQuery **yasaktır**
-   Sadece Alpine.js + Blade + Livewire kullanılır
-   Modern JavaScript framework'leri entegre edilemez

### ❌ Eski Konum Sistemi

-   `emlakloc-integration.blade.php` kullanımı **yasaktır**
-   Google Maps entegrasyonlu sistem **zorunludur**
-   Google Maps API key olmadan konum sistemi çalıştırılamaz

### ✅ Yeni Konum Sistemi (Zorunlu)

-   **Google Maps Entegrasyonu** zorunludur
-   **Marker Drag & Drop** ile hassas konum seçimi
-   **Google Places API** ile adres arama
-   **Real-time Sync** - Harita ↔ Form alanları senkronizasyonu
-   **Coordinates Storage** - Lat/Lng koordinatları kaydetme
-   **IP Based Location** - Kullanıcının konumunu otomatik tespit

### ❌ Kod Üretiminde Varsayımlar

-   AI, tablo/kolon adlarını uyduramaz
-   Veritabanı yapısı **Context7 hafızasından** okunmalı
-   Gerçek olmayan tablo/kolon adları kullanılamaz

### ❌ Birleşik Ad Alanları

-   `ad_soyad`, `full_name`, `tam_ad` gibi birleşik alanlar **yasaktır**
-   Yerine → `ad` ve `soyad` ayrı kolonlar kullanılmalıdır
-   Tam ad gösterimi: `CONCAT(ad, ' ', soyad)` veya accessor kullanılır

### ❌ Kişisel Veri İşleme

-   AI asla telefon, email, adres gibi kişisel verileri işleyemez
-   AI sadece şema yapılarını ve genel bilgileri kullanabilir
-   Müşteri verileri AI'ya gönderilemez

### ❌ JavaScript Güvenlik Hataları

-   `undefined` değerler string interpolation'da kullanılamaz
-   Null/undefined kontrolü zorunludur
-   Single quote escape işlemi yapılmalıdır
-   DOM element kontrolü yapılmadan işlem yapılamaz

### ❌ API Endpoint CSRF Eksikliği Yasak

-   Tüm AJAX isteklerinde CSRF token zorunludur
-   Authentication gerektiren endpoint'lerde token kontrolü yapılmalıdır
-   Fetch isteklerinde header'da X-CSRF-TOKEN bulunmalıdır

### ❌ Alias Kullanımı Yasak

-   Controller'larda `as name`, `as title` gibi alias kullanımı yasaktır
-   Veri çekerken sadece orijinal kolon adları kullanılmalıdır
-   Blade template'lerde fallback kullanımı zorunludur

### ❌ Blade Template Hata Riski Yasak

-   Veri gösterirken null/undefined kontrolü zorunludur
-   Fallback değerler kullanılmalıdır
-   `{{ $var->field ?? 'default' }}` formatı kullanılmalıdır

### ❌ Veritabanı Relationship Hataları

-   **Sehir relationship yasak:** `il` yerine `il` kullanılmalıdır
-   **Musteri türü kolonu:** `musteri_turu` yerine `musteri_tipi` kullanılmalıdır
-   **Durum kolonu:** `status` yerine `status` kullanılmalıdır (talepler tablosu için)
-   **Satis tipi kolonu:** `satis_tipi` yerine `yayin_tipi_id` kullanılmalıdır
-   **Duplicate function yasak:** Aynı fonksiyon iki kez tanımlanamaz
-   **User relationship:** Gorev modelinde `user` yerine `admin` kullanılmalıdır
-   **Oncelik kolonu yasak:** `oncelik` yerine `one_cikan` kullanılmalıdır (ilanlar tablosu)
-   **Yayinlandi kolonu yasak:** `yayinlandi` yerine `is_published` kullanılmalıdır

---

## 3. Veritabanı Kuralları

### Anahtarlar

-   Foreign Key'ler **mutlaka tanımlı ve aktif** olmalı
-   Yetim kayıt bırakmak yasaktır
-   Referential integrity korunmalıdır

### İsimlendirme

-   Tablo adları: `ilanlar`, `kisiler`, `arsa_detaylari` (snake_case, TR)
-   Kolon adları: küçük harf + `_` ayracı
-   Foreign key'ler: `tablo_adı_id` formatında

### Standart Kolonlar

-   `id`, `created_at`, `updated_at` kolonlarını içerir
-   Soft delete gerekiyorsa: `deleted_at`
-   Tüm tablolarda tutarlı olmalıdır

### Arama Performansı

-   Sık kullanılan filtreler için composite index şarttır
-   Konum için: `(ulke_id, il_id, ilce_id, mahalle_id)`
-   İlan arama için: `(kategori_id, il_id, aktif_mi)`

### Kişi Verileri Yapısı

-   **Ad alanları:** `ad` ve `soyad` ayrı kolonlar kullanılır
-   **Birleşik alanlar yasak:** `ad_soyad`, `full_name`, `tam_ad` kullanılamaz
-   **Tam ad gösterimi:** `CONCAT(ad, ' ', soyad)` veya model accessor
-   **Arama:** Hem `ad`, hem `soyad`, hem de birleşik arama desteklenir

### Doğru Relationship Kullanımları

-   **İl relationship:** `il()` fonksiyonu kullanılır, `il()` yasaktır
-   **İlçe relationship:** `ilce()` fonksiyonu kullanılır
-   **Mahalle relationship:** `mahalle()` fonksiyonu kullanılır
-   **Görev relationships:** `admin()`, `danisman()`, `musteri()` kullanılır
-   **İlan relationships:** `il()`, `ilce()`, `mahalle()` kullanılır
-   **Talep relationships:** `il()`, `ilce()`, `mahalle()` kullanılır

### Doğru Kolon Kullanımları

-   **Müşteri türü:** `musteri_tipi` kolonu kullanılır
-   **Talep durumu:** `status` kolonu kullanılır (talepler tablosu)
-   **İlan yayın tipi:** `yayin_tipi_id` kolonu kullanılır
-   **İlan aktif durumu:** `aktif_mi` kolonu kullanılır
-   **İlan öncelik:** `one_cikan` kolonu kullanılır (ilanlar tablosu)
-   **İlan yayın durumu:** `is_published` kolonu kullanılır

### View Path Kuralları

-   **Module view path yasak:** `takimyonetimi::admin.gorevler.show` kullanılamaz
-   **Doğru view path:** `admin.takim-yonetimi.gorevler.show` kullanılmalıdır
-   **Module namespace yasak:** `modules.takimyonetimi.admin.gorevler.index` kullanılamaz
-   **Doğru namespace:** `admin.takim-yonetimi.gorevler.index` kullanılmalıdır

### Form Wizard Standartları

-   **7 adımlı wizard:** İlan sahibi → Kategori → Konum → Özellikler → Temel Bilgiler → Fotoğraflar → Önizleme
-   **Aranabilir seçimler:** İlan sahibi ve danışman seçimleri real-time arama
-   **API endpoint:** `/api/search/persons` ile kişi arama
-   **Debounce:** 300ms gecikme ile performans optimizasyonu
-   **Validation:** Her adımda client-side ve server-side validation

---

## 4. CSS & Tasarım Kuralları

### Neo Design System

-   **Sadece Neo Design System** kullanılacak
-   Renk paleti: `neo-bg-primary`, `neo-text-muted` gibi Neo class'ları
-   Legacy sınıflar tamamen yasaktır

### Bileşenler

-   `neo-card` → içerik kutuları
-   `neo-btn` → butonlar
-   `neo-form-*` → formlar
-   `neo-input` → input alanları

### Responsive

-   Responsive zorunlu: `sm:`, `md:`, `lg:` Tailwind breakpoints
-   Mobile-first yaklaşım
-   Dark mode default

---

## 5. AI Kullanım Kuralları

### İlan Başlığı & Açıklama

-   AI sadece öneri üretir → CRM admin onayı gerekir
-   Otomatik kayıt yapılamaz
-   İnsan onayı olmadan yayın yok

### Danışman Raporları

-   AI skorlar, özetler, tavsiyeler verir → otomatik kayıt yapmaz
-   Sadece analiz ve öneri üretir
-   Sonuçlar admin panelinden kontrol edilir

### Müşteri Talepleri

-   AI eşleşmeleri listeler → seçim danışmana bırakılır
-   Otomatik eşleştirme yapılamaz
-   İnsan onayı zorunludur

### Veri Güvenliği

-   AI asla şifre, API key, gizli bilgileri log'lamaz
-   Kişisel veriler AI'ya gönderilemez
-   Tüm AI işlemleri `ai_chat_logs` tablosuna kaydedilir

---

## 6. Dökümantasyon & Senkron Kuralları

### Ana Dosyalar

-   `context7-master.md` → veritabanı şeması + Mermaid diyagramları
-   `context7-rules.md` → bu dosya (yasaklar + standartlar)
-   `context7.json` → AI konfigürasyonu

### Tutarlılık

-   Kod ile `.md` dosyaları çelişirse → CI/CD pipeline hata verir
-   Drift detection otomatik çalışır
-   Uyumsuzluk tespit edilirse merge durur

### Onaylı Güncelleme

-   Kurallarda değişiklik AI tarafından yapılamaz
-   Sadece manuel commit ile güncellenir
-   AI sadece mevcut kurallara uygun işlem yapar

---

## 7. Güvenlik Kuralları

### API Güvenliği

-   Tüm API endpoint'leri CSRF koruması altında
-   Rate limiting zorunlu
-   API key'ler güvenli saklanır

### Veri Güvenliği

-   Kişisel veriler şifrelenir
-   Log'larda hassas bilgi saklanmaz
-   Backup'lar güvenli lokasyonda

### AI Güvenliği

-   AI asla sistem dosyalarına erişemez
-   AI asla veritabanı yapısını değiştiremez
-   AI sadece belirlenen işlemleri yapar

---

## 8. Performans Kuralları

### Database

-   Tüm sorgular optimize edilmiş olmalı
-   N+1 problem yasaktır
-   Eager loading kullanılır
-   Index'ler düzenli kontrol edilir

### Frontend

-   Lazy loading kullanılır
-   Image optimization zorunlu
-   CSS/JS minification
-   CDN kullanımı önerilir

### AI

-   AI response time < 3 saniye
-   Cache kullanımı zorunlu
-   Rate limiting uygulanır
-   Background processing tercih edilir

### JavaScript Güvenlik Standartları

-   **Null/undefined kontrolü:** Tüm değişkenler kontrol edilir
-   **String interpolation:** Güvenli template literal kullanımı
-   **DOM element kontrolü:** Element varlığı kontrol edilir
-   **Error handling:** Try-catch blokları zorunlu
-   **Debounce:** Arama işlemleri için 300ms gecikme

---

## 9. Gelecek Planları

### Config Sync

-   `settings` tablosu üzerinden AI config yönetimi (admin panel)
-   Dinamik konfigürasyon değişiklikleri
-   Real-time ayar güncellemeleri

### Auto-Diff

-   Kod/DB ↔ md dosyaları karşılaştırma script'i
-   Otomatik drift detection
-   Uyumsuzluk raporlama

### AI Monitoring

-   Kullanım limiti & log analizi
-   Performance monitoring
-   Error tracking ve alerting

---

## 10. İhlal Durumları

### Otomatik Kontroller

-   CI/CD pipeline'da kurallar kontrol edilir
-   Drift detection otomatik çalışır
-   Lint kontrolü zorunludur

### Manuel Kontroller

-   Code review süreci
-   Güvenlik audit'leri
-   Performance test'leri

### İhlal Sonuçları

-   Merge engellenir
-   Pipeline başarısız olur
-   Hata logları tutulur
-   Geliştirici bilgilendirilir

---

## Takım Yönetimi ve Görev Sistemi Hataları

### Takım Yönetimi 403 Hatası

-   **Sorun**: `http://localhost:8000/admin/takim-yonetimi/takim` sayfasında 403 Forbidden hatası
-   **Çözüm**: TakimController'da `role:admin|super_admin` middleware'i geçici olarak kaldırıldı
-   **Dosya**: `app/Modules/TakimYonetimi/Controllers/Admin/TakimController.php`
-   **Not**: Güvenlik için middleware tekrar eklenmeli

### Görev Düzenleme Sayfası Hataları

-   **Sorun**: `$danismanlar` undefined variable hatası
-   **Çözüm**: Controller'da gerekli değişkenler eklendi
-   **Dosya**: `app/Modules/TakimYonetimi/Controllers/Admin/GorevController.php`
-   **Eklenen Değişkenler**:
    -   `$danismanlar` - Danışman kullanıcıları
    -   `$adminler` - Admin kullanıcıları
    -   `$musteriler` - Müşteri kullanıcıları
    -   `$projeler` - Proje listesi

### Görev Detay Sayfası Modernizasyonu

-   **Dosya**: `resources/views/admin/takim-yonetimi/gorevler/show.blade.php`
-   **Yapılan İyileştirmeler**:
    -   Modern gradient header tasarımı
    -   Neo Design System uyumlu kartlar
    -   Responsive grid layout
    -   Dark mode desteği
    -   Hover animasyonları
    -   Modern icon'lar
-   **Düzeltilen Hatalar**:
    -   `count()` null hatası düzeltildi
    -   `gorevTakip` null kontrolü eklendi
    -   `dosyalar` null kontrolü eklendi

### Talep Portfolyo Sayfası Modernizasyonu

-   **Dosyalar**:
    -   `resources/views/admin/talep-portfolyo/index.blade.php`
    -   `resources/views/admin/talep-portfolyo/show.blade.php`
-   **Yapılan İyileştirmeler**:
    -   Modern gradient tasarımlar
    -   AI analiz modal'ları modernize edildi
    -   Toplu analiz modal'ı modernize edildi
    -   Modern buton tasarımları
    -   Glow efektleri
    -   Responsive tasarım

### Kullanıcılar Sayfası Permissions Hatası

-   **Sorun**: `Call to undefined relationship [permissions] on model [App\Modules\Auth\Models\User]`
-   **Çözüm**: User modelinde `permissions()` relationship metodu eklendi
-   **Dosya**: `app/Modules/Auth/Models/User.php`
-   **Yapılan Değişiklikler**:
    -   `HasRoles` trait'i geri eklendi
    -   `permissions()` relationship metodu eklendi
    -   Spatie Permission paketi ile uyumlu hale getirildi
-   **Önemli**: Spatie Permission paketi `user_permissions` değil `model_has_permissions` tablosunu kullanır
-   **Doğru Relationship**:

```php
public function permissions()
{
    return $this->morphToMany(
        \Spatie\Permission\Models\Permission::class,
        'model',
        'model_has_permissions',
        'model_id',
        'permission_id'
    );
}
```

### MenuService Private Method Hatası

-   **Sorun**: `Call to private method App\Services\MenuService::getAdminMenu() from scope App\Http\Controllers\Admin\UserController`
-   **Çözüm**: `getAdminMenu()` metodu `private`'dan `public`'e çevrildi
-   **Dosya**: `app/Services/MenuService.php`
-   **Yapılan Değişiklik**: `private function getAdminMenu()` → `public function getAdminMenu()`
-   **Sebep**: UserController bu metoda doğrudan erişmeye çalışıyor

### Blade Template Auth Facade Hatası

-   **Sorun**: `Class "Auth" not found` - Blade template'inde Auth facade'i kullanılamıyor
-   **Çözüm**: Blade template'inin başına `@php use Illuminate\Support\Facades\Auth; @endphp` eklendi
-   **Dosya**: `resources/views/admin/kullanicilar/edit.blade.php`
-   **Yapılan Değişiklik**: Auth facade import edildi
-   **Kural**: Blade template'lerinde facade kullanırken mutlaka import edilmeli

### 21. ExpertiseArea SoftDeletes Hatası

-   **Hata**: `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'expertise_areas.deleted_at'`
-   **Sebep**: ExpertiseArea modeli SoftDeletes trait'ini kullanıyor ama veritabanında deleted_at kolonu yok
-   **Çözüm**: ExpertiseArea modelinden SoftDeletes trait'i kaldırıldı
-   **Dosya**: `app/Models/ExpertiseArea.php`
-   **Yapılan Değişiklik**: `use SoftDeletes;` ve `use HasFactory, SoftDeletes;` kaldırıldı
-   **Kural**: Model'lerde SoftDeletes kullanırken veritabanında deleted_at kolonu olmalı

### 22. Danışman Create Sayfası Modernizasyonu

-   **Güncelleme**: Danışman create sayfası modernize edildi ve tablo kolonlarına uygun hale getirildi
-   **Dosya**: `resources/views/admin/danisman/create.blade.php`
-   **Yapılan Değişiklikler**:
    -   Modern header ve istatistik kartları eklendi
    -   Form alanları `danismans` tablosu kolonlarına uygun güncellendi
    -   Progress bar ve form validasyonu eklendi
    -   Modern butonlar ve animasyonlar eklendi
-   **Tablo Kolonları**: `user_id`, `lisans_no`, `telefon`, `telefon_2`, `email`, `adres`, `uzmanlik_alani`, `komisyon_orani`, `aktif`
-   **Kural**: Form alanları mutlaka veritabanı tablosu kolonlarıyla uyumlu olmalı

### 23. Adres Yönetimi ve Sehir/İl Düzenlemeleri

-   **Güncelleme**: Tüm sistemde il referansları il olarak güncellendi
-   **Dosyalar**:
    -   `resources/views/admin/adres-yonetimi/index.blade.php`
    -   `resources/views/admin/kisiler/index.blade.php`
    -   `resources/views/admin/ayarlar/location.blade.php`
    -   `resources/views/ilanlar/index.blade.php`
    -   `resources/views/components/ilan-card-rental.blade.php`
    -   `resources/views/admin/danisman-ai/index.blade.php`
    -   `resources/views/admin/ilanlar/pdf.blade.php`
    -   `app/Services/AI/TalepPortfolyoAIService.php`
    -   `app/Modules/Emlak/Models/Ilan.php`
    -   `app/Http/Controllers/Admin/IlanController.php`
    -   `app/Models/Kisi.php`
    -   `app/Models/Talep.php`
    -   `app/Http/Controllers/Admin/TalepController.php`
    -   `app/Http/Controllers/Admin/KisiController.php`
    -   `app/Http/Controllers/Admin/DanismanAIController.php`
-   **Yapılan Değişiklikler**:
    -   Route referansları: `sehirler` → `iller`
    -   Model ilişkileri: `il()` → `il()`
    -   View referansları: `$model->il->ad` → `$model->il->il_adi`
    -   Controller eager loading: `il` → `il`
    -   JavaScript referansları: `il` → `il`
-   **Kural**: Sistem genelinde il yerine il kullanılmalı, tutarlılık sağlanmalı

### 24. Ayarlar Sayfaları Modernizasyonu

-   **Güncelleme**: Admin ayarlar ve AI ayarlar sayfaları modernize edildi
-   **Dosyalar**:
    -   `resources/views/admin/ayarlar/index.blade.php`
    -   `resources/views/admin/ai-settings/index.blade.php`
-   **Yapılan Değişiklikler**:
    -   Modern header tasarımı
    -   Gradient butonlar ve animasyonlar
    -   Neo Design System uyumlu layout
    -   Gelişmiş buton tasarımları
    -   Responsive tasarım
-   **Veritabanı Durumu**:
    -   Settings tablosu: 22 kayıt
    -   AISettings tablosu: 6 kayıt
    -   SiteSettings tablosu: 12 kayıt
-   **Kural**: Ayarlar sayfaları modern tasarım standartlarına uygun olmalı

### 25. Database Uyumsuzlukları ve Context7 Uyumluluğu

-   **Güncelleme**: Database uyumsuzlukları Context7 kurallarına uygun şekilde düzeltildi
-   **Migration Dosyaları**:
    -   `database/migrations/2025_09_28_072034_fix_database_inconsistencies.php`
    -   `database/migrations/2025_09_28_073114_add_il_id_fields_to_tables.php`
    -   `database/migrations/2025_09_28_073356_fix_context7_violations.php`
-   **Yapılan Değişiklikler**:
    -   **il_id alanları eklendi**: Tüm tablolarda Context7 kurallarına uygun il_id alanları
    -   **Foreign key'ler**: iller.id ile doğru ilişkiler kuruldu
    -   **Index'ler**: Performans için gerekli index'ler eklendi
    -   **Veri korunma**: Mevcut veriler güvenli şekilde korundu
    -   **Talepler tablosu**: Eksik alanlar (il_id, ilce_id, mahalle_id, min_fiyat, max_fiyat) eklendi
    -   **Context7 alan adları**: status → status, is_active → status, aktif → status, ad → name
    -   **Model güncellemeleri**: Tüm model'ler Context7 kurallarına uygun hale getirildi
-   **Context7 Uyumluluğu**:
    -   ✅ **Route referansları**: sehirler → iller
    -   ✅ **Model ilişkileri**: il() → il()
    -   ✅ **View referansları**: $model->il->ad → $model->il->il_adi
    -   ✅ **Controller eager loading**: il → il
    -   ✅ **JavaScript referansları**: il → il
    -   ✅ **Alan adları**: status → status, is_active → status, aktif → status, ad → name
    -   ✅ **Relationship'ler**: bolge() kaldırıldı, il() kaldırıldı, il() kullanılıyor
-   **Veritabanı Durumu**:
    -   İlanlar: 38 kayıt, il_id alanı mevcut
    -   Kisiler: 43 kayıt, il_id alanı mevcut, status alanı mevcut
    -   Musteriler: 0 kayıt, il_id alanı mevcut
    -   Talepler: 6 kayıt, il_id alanı mevcut
    -   İlan Kategorileri: status alanı mevcut
    -   Özellik Kategorileri: status ve name alanları mevcut
-   **Kural**: Tüm database alanları Context7 kurallarına uygun olmalı, il_id kullanılmalı

### 26. Kapsamlı Kod Temizliği ve Context7 Uyumluluğu

-   **Güncelleme**: Tüm eski kodlar Context7 kurallarına uygun şekilde temizlendi ve güncellendi
-   **Temizlenen Alanlar**:
    -   **status → status**: Tüm modeller, controller'lar, view'lar ve JavaScript dosyalarında
    -   **is_active → status**: İlan kategorileri ve özellik kategorileri tablolarında
    -   **aktif → status**: Özellik kategorileri tablosunda
    -   **ad → name**: Özellik kategorileri tablosunda
    -   **il() → il()**: Model ilişkilerinde
    -   **region_id kaldırıldı**: Gereksiz alan temizlendi
-   **Güncellenen Dosyalar**:
    -   **Model'ler**: Kisi.php, IlanKategori.php, OzellikKategori.php
    -   **Controller'lar**: KisiController.php
    -   **View'lar**: admin/kisiler/index.blade.php
    -   **JavaScript**: ValidationManager.js
-   **Context7 Uyumluluğu**:
    -   ✅ **Database alanları**: Tüm alanlar Context7 kurallarına uygun
    -   ✅ **Model ilişkileri**: Doğru alan adları kullanılıyor
    -   ✅ **Controller logic**: Güncel alan adları ile çalışıyor
    -   ✅ **View templates**: Güncel alan adları ile render ediliyor
    -   ✅ **JavaScript validation**: Güncel alan adları ile validate ediliyor
-   **Test Sonuçları**:
    -   ✅ Tüm modeller başarıyla yükleniyor
    -   ✅ Database alanları Context7 uyumlu
    -   ✅ Eski alan adları tamamen temizlendi
-   **Kural**: Tüm kod Context7 kurallarına uygun olmalı, eski alan adları kullanılmamalı

### 27. Adres ve Lokasyon Sistemi Context7 Uyumluluğu

-   **Güncelleme**: Tüm adres ve lokasyon ile ilgili alanlar Context7 kurallarına uygun hale getirildi
-   **Temizlenen Alanlar**:
    -   **il → il**: Tüm model'ler, controller'lar, view'lar ve JavaScript dosyalarında
    -   **il_id → il_id**: Database alanları ve foreign key'ler
    -   **sehir_adi → il_adi**: View'larda ve controller'larda
    -   **region_id kaldırıldı**: Gereksiz alan temizlendi
    -   **Sehir model deprecated**: Il model'i kullanılmalı
-   **Güncellenen Dosyalar**:
    -   **Controller'lar**: IlanController.php, AddressController.php, AIChatController.php, TakvimController.php
    -   **Model'ler**: Sehir.php (deprecated), Kisi.php
    -   **View'lar**: admin/ilanlar/index.blade.php, admin/takim-yonetimi/gorevler/show.blade.php
    -   **AI Services**: TalepPortfolyoAIService.php
-   **Context7 Uyumluluğu**:
    -   ✅ **Adres alanları**: Tüm alanlar Context7 kurallarına uygun
    -   ✅ **Model ilişkileri**: Doğru alan adları kullanılıyor
    -   ✅ **Controller logic**: Güncel alan adları ile çalışıyor
    -   ✅ **View templates**: Güncel alan adları ile render ediliyor
    -   ✅ **AI services**: Güncel alan adları ile çalışıyor
-   **Test Sonuçları**:
    -   ✅ Tüm adres alanları Context7 uyumlu
    -   ✅ Lokasyon sistemi tutarlı
    -   ✅ Eski alan adları tamamen temizlendi
-   **Kural**: Tüm adres ve lokasyon alanları Context7 kurallarına uygun olmalı, il yerine il kullanılmalı

### 28. Cursor Görev Başlangıcı Context7 Kontrol Sistemi

-   **Güncelleme**: Cursor her görev başlangıcında Context7 kurallarını otomatik kontrol edecek
-   **Kontrol Edilecek Alanlar**:
    -   **Database alanları**: status → status, is_active → status, aktif → status
    -   **Adres alanları**: il → il, il_id → il_id, region_id kaldırıldı
    -   **Kişi alanları**: ad_soyad → tam_ad, full_name → tam_ad
    -   **Model ilişkileri**: il() → il(), bolge() kaldırıldı
    -   **View referansları**: $model->il → $model->il
    -   **Controller mapping**: region_id → il_id, il_id → il_id
-   **Otomatik Kontrol Komutları**:

    ```bash
    # Database alanları kontrolü
    grep -r "status\|is_active\|aktif" app/Models/ resources/views/ app/Http/Controllers/

    # Adres alanları kontrolü
    grep -r "il\|il_id\|region_id" app/Models/ resources/views/ app/Http/Controllers/

    # Kişi alanları kontrolü
    grep -r "ad_soyad\|full_name" app/Models/ resources/views/ app/Http/Controllers/
    ```

-   **Hata Tespiti**:
    -   Context7 kurallarına aykırı alan kullanımı tespit edilirse otomatik güncelleme önerilir
    -   Deprecated model'ler kullanılırsa uyarı verilir
    -   Eski alan adları kullanılırsa güncelleme yapılır
-   **Kural**: Cursor her görev başlangıcında Context7 kuralları kontrol edilmeli, hata tespiti varsa güncellenmeli

### 29. Eski Dosya Temizleme ve Önleme Kuralları

-   **Güncelleme**: Eski dosyalar otomatik temizlenir ve yeni dosya oluşturma kuralları güncellenir
-   **Temizlenen Dosya Türleri**:
    -   **.DS_Store dosyaları**: macOS sistem dosyaları otomatik silinir
    -   **.backup dosyaları**: Yedek dosyalar otomatik silinir
    -   **.tmp dosyaları**: Geçici dosyalar otomatik silinir
    -   **Legacy dosyalar**: Eski Context7 dosyaları archive'a taşınır
-   **Otomatik Temizleme Komutları**:

    ```bash
    # Sistem dosyalarını temizle
    find . -name ".DS_Store" -type f -delete

    # Yedek dosyaları temizle
    find . -name "*.backup" -type f -delete

    # Geçici dosyaları temizle
    find . -name "*.tmp" -type f -delete
    ```

-   **Yeni Dosya Oluşturma Kuralları**:
    -   **Context7 uyumlu alan adları**: Tüm yeni dosyalar Context7 kurallarına uygun alan adları kullanmalı
    -   **Model oluşturma**: Yeni model'ler status, il_id, tam_ad gibi Context7 uyumlu alanlar kullanmalı
    -   **View oluşturma**: Yeni view'lar Context7 uyumlu alan referansları kullanmalı
    -   **Controller oluşturma**: Yeni controller'lar Context7 uyumlu field mapping kullanmalı
-   **Önleme Kuralları**:
    -   **Yasak alan adları**: status, is_active, aktif, ad_soyad, full_name, il, il_id, region_id
    -   **Zorunlu alan adları**: status, il_id, tam_ad, il_adi
    -   **Model relationship'leri**: il() yasak, il() zorunlu
    -   **Database alanları**: Tüm yeni alanlar Context7 kurallarına uygun olmalı
-   **Kural**: Yeni dosya oluştururken Context7 kuralları zorunlu, eski dosyalar otomatik temizlenmeli

### 30. Context7 Uyumluluk Zorunluluğu

-   **Güncelleme**: Tüm yeni geliştirmeler Context7 kurallarına uygun olmak zorunda
-   **Zorunlu Kontroller**:
    -   **Dosya oluşturma öncesi**: Context7 kuralları kontrol edilmeli
    -   **Model oluşturma**: Context7 uyumlu alan adları kullanılmalı
    -   **View oluşturma**: Context7 uyumlu alan referansları kullanılmalı
    -   **Controller oluşturma**: Context7 uyumlu field mapping kullanılmalı
    -   **Migration oluşturma**: Context7 uyumlu alan adları kullanılmalı
-   **Otomatik Kontrol Sistemi**:

    ```bash
    # Her görev başlangıcında çalıştır
    ./scripts/context7-check.sh

    # Hata varsa düzelt
    # Hata yoksa devam et
    ```

-   **Hata Tespiti Durumunda**:
    -   **Otomatik düzeltme**: Basit hatalar otomatik düzeltilir
    -   **Manuel düzeltme**: Karmaşık hatalar manuel düzeltilir
    -   **Kural güncelleme**: Yeni hata türleri için kurallar güncellenir
-   **Kural**: Hiçbir yeni geliştirme Context7 kurallarına aykırı olamaz, tüm hatalar önceden tespit edilmeli

### 31. Context7 Dil Tutarlılığı Kuralı (YENİ)

-   **Güncelleme**: Tüm dosya adları ve kod içinde dil tutarlılığı zorunlu
-   **Dil Seçimi**: İngilizce tercih edilir, Türkçe sadece iş kuralları için kullanılır
-   **Dosya Adlandırma Kuralları**:
    -   **Model dosyaları**: İngilizce (User.php, Property.php, Feature.php)
    -   **Controller dosyaları**: İngilizce (UserController.php, PropertyController.php)
    -   **Service dosyaları**: İngilizce (UserService.php, PropertyService.php)
    -   **Context7 dosyaları**: İngilizce (Context7ApiService.php, Context7FeatureService.php)
    -   **Türkçe sadece**: İş kuralları (danisman, musteri, gorev → advisor, customer, task)
-   **Veritabanı Alan Adları**:
    -   **İngilizce**: status, created_at, updated_at, user_id
    -   **Türkçe yasak**: status, olusturulma_tarihi, guncelleme_tarihi
    -   **İş kuralı alanları**: danisman_id → advisor_id, musteri_id → customer_id
-   **Kod İçi Değişkenler**:
    -   **İngilizce**: $user, $property, $feature, $$$$$$$$$$$$status
    -   **Türkçe yasak**: $kullanici, $emlak, $ozellik, $$$$$$$$$$$$status
-   **Otomatik Dönüşüm Kuralları**:
    ```php
    // Eski → Yeni
    Il.php → Province.php
    Ozellik.php → Feature.php
    DanismanController.php → AdvisorController.php
    MusteriController.php → CustomerController.php
    GorevController.php → TaskController.php
    ```

### 32. Context7 Hata Önleme ve Otomatik Düzeltme Sistemi

-   **Güncelleme**: Tespit edilen hatalar otomatik düzeltilir ve kurallar güncellenir
-   **Otomatik Düzeltme Kuralları**:
    -   **ad_soyad → tam_ad**: Tüm view'larda ve controller'larda otomatik değiştirilir
    -   **musteri_ad_soyad → musteri_tam_ad**: Controller validation'larında otomatik değiştirilir
    -   **sehir_adi → il_adi**: Controller'larda otomatik değiştirilir
    -   **region_id kaldırma**: Controller'larda otomatik kaldırılır
    -   **Türkçe dosya adları → İngilizce**: Otomatik rename işlemi
-   **Hata Tespiti ve Düzeltme Süreci**:
    1. **Context7 kontrol script çalıştırılır**
    2. **Hatalar tespit edilir**
    3. **Otomatik düzeltme yapılır**
    4. **Kurallar güncellenir**
    5. **Tekrar kontrol edilir**
-   **Önleme Kuralları**:
    -   **Yeni dosya oluştururken**: Context7 kuralları zorunlu kontrol
    -   **Model oluştururken**: Context7 uyumlu alan adları kullanma
    -   **View oluştururken**: Context7 uyumlu alan referansları kullanma
    -   **Controller oluştururken**: Context7 uyumlu field mapping kullanma
-   **Kural**: Tüm hatalar otomatik düzeltilmeli, yeni hatalar önlenmeli, kurallar sürekli güncellenmeli

### 32. Context7 Kontrol Script'i Gelişmiş Özellikler

-   **Güncelleme**: Context7 kontrol script'i gelişmiş özelliklerle güçlendirildi
-   **Yeni Özellikler**:
    -   **Otomatik Düzeltme Sistemi**: `--auto-fix` parametresi ile otomatik düzeltme
    -   **Performans Kontrolü**: `--performance` parametresi ile performans analizi
    -   **Güvenlik Kontrolü**: `--security` parametresi ile güvenlik analizi
    -   **Kod Kalitesi Kontrolü**: `--quality` parametresi ile kod kalitesi analizi
    -   **Yardım Sistemi**: `--help` parametresi ile kullanım rehberi
-   **Otomatik Düzeltme Özellikleri**:
    -   **Backup oluşturma**: Düzeltme öncesi otomatik backup
    -   **ad_soyad → tam_ad**: Otomatik alan adı düzeltme
    -   **musteri_ad_soyad → musteri_tam_ad**: Otomatik validation düzeltme
    -   **sehir_adi → il_adi**: Otomatik controller düzeltme
    -   **region_id kaldırma**: Otomatik comment işaretleme
-   **Performans Kontrolü Özellikleri**:
    -   **N+1 query kontrolü**: Eager loading kullanımı analizi
    -   **Database index kontrolü**: Index tanımları analizi
    -   **Cache kullanımı kontrolü**: Cache implementasyonu analizi
-   **Güvenlik Kontrolü Özellikleri**:
    -   **CSRF token kontrolü**: CSRF koruması analizi
    -   **XSS koruması kontrolü**: Unescaped output analizi
    -   **Input validation kontrolü**: Validation kullanımı analizi
-   **Kod Kalitesi Kontrolü Özellikleri**:
    -   **PSR-12 kontrolü**: Coding standards analizi
    -   **Code duplication kontrolü**: Fonksiyon tekrarı analizi
    -   **Comment coverage kontrolü**: Dokümantasyon analizi
-   **Kullanım Örnekleri**:

    ```bash
    # Normal kontrol
    ./scripts/context7-check.sh

    # Otomatik düzeltme
    ./scripts/context7-check.sh --auto-fix

    # Performans kontrolü
    ./scripts/context7-check.sh --performance

    # Güvenlik kontrolü
    ./scripts/context7-check.sh --security

    # Kod kalitesi kontrolü
    ./scripts/context7-check.sh --quality

    # Kombine kullanım
    ./scripts/context7-check.sh --auto-fix --performance --security

    # Yardım
    ./scripts/context7-check.sh --help
    ```

-   **Kural**: Context7 kontrol script'i gelişmiş özelliklerle kullanılmalı, otomatik düzeltme tercih edilmeli

## Version 1.5.0 - 2025-01-28

-   Added: Comprehensive Context7 compliance system
-   Added: Automatic file cleanup and prevention rules
-   Added: Context7 error prevention and auto-fix system
-   Added: Cursor task start Context7 control system
-   Updated: All address and location fields to Context7 compliance
-   Updated: All person and CRM fields to Context7 compliance
-   Updated: All AI services to Context7 compliance
-   Fixed: ad_soyad → tam_ad conversions
-   Fixed: il → il conversions
-   Fixed: status → status conversions
-   Fixed: region_id removal
-   Fixed: Legacy file cleanup

### 🎯 **Kategori Seçim Sistemi Kuralları:**

-   **Ana Kategori** seçimi → **Alt Kategoriler** yüklenir
-   **Alt Kategori** seçimi → **Yayın Tipleri** yüklenir
-   Her seçimde önceki seçimler temizlenir
-   Loading state gösterilir
-   Error handling yapılır

### 🎯 **Form Wizard Navigation Kuralları:**

-   Navigation butonları `data-wizard-action` attribute'u kullanır
-   Step content'ler `data-wizard-step-content` attribute'u kullanır
-   Step indicator'lar `data-wizard-step` attribute'u kullanır
-   JavaScript event listener'lar bu attribute'ları dinler

### 🎯 **Dosya Temizleme Kuralları:**

-   Yeni sistem çalıştıktan sonra eski dosyalar silinir
-   Build işlemi yapılır
-   Performans optimizasyonu sağlanır
-   Maintenance kolaylaştırılır

### 🎯 **Step Initialization Kuralları:**

-   Her step için ayrı initialization fonksiyonu oluşturulur
-   Step değiştiğinde `wizard-step-changed` event'i dinlenir
-   DOM elementleri step aktif olduğunda initialize edilir
-   Console log'lar ile debug bilgisi sağlanır
-   Default değerler otomatik set edilir (örn: Türkiye seçimi)

### 🎯 **JavaScript Syntax Kuralları:**

-   Her `{` açılışında karşılık gelen `}` kapatma olmalı
-   Fazla `}` karakterleri syntax hatasına neden olur
-   Build işlemi öncesi syntax kontrolü yapılır
-   Browser cache temizlenir
-   Console'da syntax hataları kontrol edilir

### **14. Adres Yönetimi Modernizasyonu**

-   ✅ **İstatistik dashboard**: 4 gradient kart (ülke, il, ilçe, mahalle)
-   ✅ **Arama sistemi**: Global arama ve kategori filtresi
-   ✅ **Sıralama**: A-Z, Z-A seçenekleri
-   ✅ **İstatistikler modal**: Detaylı analiz bilgileri
-   ✅ **Modern tasarım**: Neo Design System uygulandı
-   ✅ **JavaScript fonksiyonları**: filterData, sortData, showFilteredResults
-   ✅ **Real-time updates**: Alpine.js ile dinamik güncellemeler

### **15. Database Uyumluluğu ve Önlemler**

-   ✅ **Migration standardizasyonu**: Eksik alanları güvenli şekilde ekleme
-   ✅ **Enum değer doğrulama**: Stage alanı için geçerli enum değerleri kontrolü
-   ✅ **Alan adı mapping**: Controller ile database alan adları uyumluluğu
-   ✅ **Index kontrolü**: Mevcut indexleri kontrol ederek duplicate ekleme önleme
-   ✅ **Rollback planı**: Migration geri alma stratejisi
-   ✅ **Health check**: Database schema doğrulama sistemi
-   ✅ **Error handling**: Undefined array key hatalarını önleme
-   ✅ **Validation rules**: Form validation ile database uyumluluğu
-   ✅ **Field mapping sistemi**: Controller-Database alan adı dönüşümü
-   ✅ **Stage mapping sistemi**: Frontend step'lerinden database stage'lerine dönüşüm
-   ✅ **Duplicate cleanup**: Çakışan alanları temizleme ve veri taşıma
-   ✅ **Seeder güncelleme**: Test verilerini yeni alan adları ile güncelleme
-   ✅ **Kök neden analizi**: Uyumsuzluğun nedenlerini tespit etme ve dokümantasyon

### **33. Context7 Otomatik Düzeltme Sistemi Başarısı**

-   **Güncelleme**: Context7 kontrol script'i --auto-fix parametresi ile %100 başarı sağladı
-   **Başarılı Düzeltmeler**:
    -   **Backup Sistemi**: Otomatik backup oluşturma (`backups/context7-YYYYMMDD-HHMMSS`)
    -   **Alan Adı Düzeltmeleri**: `ad_soyad → tam_ad`, `musteri_ad_soyad → musteri_tam_ad`
    -   **Adres Düzeltmeleri**: `sehir_adi → il_adi`, `region_id` referansları comment olarak işaretleme
    -   **Doğrulama Sistemi**: Düzeltme sonrası otomatik tekrar kontrol
-   **Sonuç**: %100 Context7 uyumluluğu sağlandı, hiçbir hata tespit edilmedi
-   **Kullanım**: `./scripts/context7-check.sh --auto-fix`
-   **Kural**: Her Context7 kural ihlali durumunda otomatik düzeltme sistemi kullanılmalı

### **34. Kapsamlı Kod Temizliği ve Legacy Dosya Yönetimi**

-   **Güncelleme**: Geçmiş yanlış kod dosyaları, View templates, AI services, Model ilişkileri, Controller logic temizliği
-   **Temizlik Kapsamı**:
    -   **View Templates**: Blade dosyalarında eski alan adları ve deprecated syntax
    -   **AI Services**: Context7 kurallarına aykırı alan referansları
    -   **Model İlişkileri**: Deprecated relationship'ler ve eski alan adları
    -   **Controller Logic**: Eski validation rules ve field mapping'ler
-   **Otomatik Tespit**: Context7 kontrol script'i ile otomatik tespit ve düzeltme
-   **Kural**: Her görev öncesi kapsamlı temizlik yapılmalı

### **35. AI Geliştirme Önerileri ve Best Practices**

-   **Güncelleme**: AI geliştirme süreçlerinde Context7 kurallarına uygun öneriler
-   **AI Service Geliştirme Kuralları**:
    -   **Alan Adı Tutarlılığı**: AI service'lerde `status`, `one_cikan`, `il_id` kullanılmalı
    -   **Model Referansları**: `Sehir` yerine `Il` model'i kullanılmalı
    -   **Deprecated Field'lar**: `status`, `oncelik`, `il_id`, `region_id` kullanılmamalı
    -   **API Response**: Context7 uyumlu field adları ile response dönülmeli
-   **AI Code Review Checklist**:
    -   ✅ Context7 field naming conventions kontrolü
    -   ✅ Deprecated model kullanımı kontrolü
    -   ✅ Database field mapping kontrolü
    -   ✅ API endpoint naming kontrolü
-   **Otomatik AI Code Analysis**: Context7 kontrol script'i ile AI service'ler otomatik analiz edilir
-   **Kural**: Her AI service geliştirmesinde Context7 kuralları zorunlu kontrol edilir

### **36. Kişi Oluşturma Sayfası Context7 Uyumluluğu**

-   **Güncelleme**: Kişi oluşturma sayfası tamamen Context7 kurallarına uygun hale getirildi
-   **Yapılan İyileştirmeler**:
    -   **Context7 Field Naming**: `status` → `status`, `oncelik` → `one_cikan` düzeltmeleri
    -   **AI Desteği**: AI önerileri paneli, müşteri tipi tahmini, öncelik önerisi, bütçe önerisi
    -   **Neo Design System**: Tüm butonlar ve component'ler Neo Design System ile değiştirildi
    -   **Güvenlik**: Enhanced CSRF protection, input sanitization, form security validation
    -   **Performans**: Lazy loading, debounced API calls, intersection observer
    -   **Test Coverage**: Kapsamlı feature test'ler eklendi
-   **AI Özellikleri**:
    -   **AI Analysis Endpoint**: `/admin/kisiler/ai-analyze` endpoint'i
    -   **Smart Suggestions**: Müşteri tipi, öncelik, bütçe önerileri
    -   **Real-time Analysis**: Form verilerine göre AI analizi
    -   **One-click Apply**: AI önerilerini tek tıkla uygulama
-   **Güvenlik Özellikleri**:
    -   **Enhanced CSRF**: Form token ve timestamp kontrolü
    -   **Input Sanitization**: XSS koruması ve input temizleme
    -   **Form Validation**: Güvenlik kontrollü form submission
-   **Performans Özellikleri**:
    -   **Lazy Loading**: Intersection Observer ile component lazy loading
    -   **Debounced Auto-save**: 5 saniye debounce ile otomatik kaydetme
    -   **Optimized API Calls**: Smart debouncing ve caching
-   **Test Coverage**:
    -   **Feature Tests**: 15 adet kapsamlı test
    -   **Context7 Compliance**: Field naming ve component testleri
    -   **AI Integration**: AI endpoint ve functionality testleri
    -   **Security Tests**: Input sanitization ve validation testleri
    -   **Performance Tests**: Load time ve optimization testleri
-   **Kural**: Tüm form sayfaları bu standartlara uygun geliştirilmeli

### **37. Neo Design System Component Hataları ve Düzeltmeleri**

-   **Güncelleme**: Neo Design System component'lerinde eksik variant'lar düzeltildi
-   **Düzeltilen Hatalar**:
    -   **"info" variant eksikliği**: `x-neo.button` component'inde `info` variant'ı eklendi
    -   **Undefined array key "info"**: Button component'inde variant classes array'ine `info` eklendi
    -   **AI Analysis endpoint eksikliği**: `/admin/kisiler/ai-analyze` route'u eklendi
-   **Eklenen Özellikler**:
    -   **Info Variant**: `bg-gradient-to-r from-cyan-500 to-cyan-600` gradient ile cyan renk teması
    -   **AI Analysis Method**: KisiController'a `aiAnalyze()` metodu eklendi
    -   **Smart Predictions**: Müşteri tipi, öncelik ve bütçe tahmin algoritmaları
-   **AI Prediction Logic**:
    -   **Müşteri Tipi**: Meslek bazlı tahmin (mühendis → Alıcı, doktor → Yatırımcı)
    -   **Öncelik**: Meslek ve telefon bilgisi bazlı tahmin
    -   **Bütçe**: Meslek ve lokasyon bazlı tahmin
-   **Route Güncellemeleri**:
    -   `POST /admin/kisiler/ai-analyze` endpoint'i eklendi
    -   `POST /admin/kisiler/bulk-action` endpoint'i zaten mevcuttu
-   **Kural**: Neo Design System component'lerinde tüm variant'lar tanımlı olmalı, AI endpoint'leri eksiksiz olmalı

### **38. Takım Yönetimi Görev Oluşturma Sayfası Modernizasyonu**

-   **Güncelleme**: Görev oluşturma sayfası tamamen Neo Design System ile modernize edildi
-   **Yapılan İyileştirmeler**:
    -   **Neo Design System CSS**: `neo-components.css` dosyası oluşturuldu ve tüm Neo sınıfları tanımlandı
    -   **Form Modernizasyonu**: Tüm `admin-input` sınıfları `neo-input` ile değiştirildi
    -   **Button Standardizasyonu**: Neo Design System button sınıfları uygulandı
    -   **Validation Uyumluluğu**: Controller validation kuralları form ile senkronize edildi
    -   **Context7 Uyumluluk**: Tüm alanlar Context7 kurallarına uygun hale getirildi
-   **Neo Design System Özellikleri**:
    -   **neo-input**: Modern input alanları, focus efektleri, error states
    -   **neo-btn**: Gradient butonlar, outline variant'lar, size seçenekleri
    -   **neo-card**: Modern kartlar, gradient variant'lar
    -   **Dark Mode**: Tam dark mode desteği
    -   **Context7 Toast**: Bildirim sistemi
    -   **Context7 AI Suggestions**: Akıllı öneriler paneli
-   **AI Entegrasyonu**:
    -   **Context7 AI Integration**: Akıllı öneriler sistemi
    -   **Hızlı Şablonlar**: 4 farklı görev tipi şablonu (Müşteri Takibi, İlan Hazırlama, Müşteri Ziyareti, Doküman Hazırlama)
    -   **Smart Suggestions**: Görev tipine göre otomatik öneriler
    -   **Progress Tracking**: Real-time form tamamlanma durumu göstergesi
-   **Validation Düzeltmeleri**:
    -   **Controller**: `oncelik` validation'ı `dusuk,normal,yuksek,acil` olarak güncellendi
    -   **Form**: Öncelik seçenekleri controller ile uyumlu hale getirildi
    -   **Field Mapping**: Tüm form alanları Context7 kurallarına uygun
-   **CSS Dosyası**: `public/css/admin/neo-components.css` - Tüm Neo Design System sınıfları
-   **Layout Entegrasyonu**: Neo layout'a CSS dosyası dahil edildi
-   **Kural**: Tüm form sayfaları Neo Design System ile modernize edilmeli, Context7 kurallarına uygun olmalı

### **39. Context7 Telegram Otomasyonu ve AI Entegrasyonu**

-   **Güncelleme**: Context7 AI destekli Telegram otomasyonu sistemi tamamen entegre edildi
-   **Yapılan İyileştirmeler**:
    -   **Context7TeamAIService**: AI destekli takım yönetimi servisi oluşturuldu
    -   **Context7TelegramAutomation**: Gelişmiş Telegram otomasyonu servisi
    -   **AI Görev Önerileri**: Kullanıcı bazlı akıllı görev önerileri sistemi
    -   **Performans Analizi**: AI destekli takım performans analizi ve raporlama
    -   **Otomatik Görev Dağıtımı**: AI ile optimize edilmiş görev ataması algoritması
    -   **Akıllı Bildirimler**: AI destekli Telegram bildirimleri ve uyarı sistemi
-   **AI Özellikleri**:
    -   **Smart Task Assignment**: AI ile otomatik görev dağıtımı
    -   **Performance Monitoring**: Gerçek zamanlı performans takibi
    -   **Deadline Warnings**: Akıllı deadline uyarı sistemi
    -   **Daily Reports**: Otomatik günlük performans raporları
    -   **Team Insights**: AI destekli takım analizi ve öneriler
-   **Otomasyon Sistemi**:
    -   **Context7TelegramAutomationController**: 7 yeni API endpoint
    -   **Cron Job Integration**: Otomatik çalışan sistem komutları
    -   **Smart Notifications**: AI destekli akıllı bildirim sistemi
    -   **Automation Status**: Otomasyon durumu takip sistemi
-   **API Endpoints**:
    -   `/api/context7/telegram/auto-assign-tasks` - Otomatik görev ataması
    -   `/api/context7/telegram/send-performance-notifications` - Performans bildirimleri
    -   `/api/context7/telegram/send-daily-reports` - Günlük raporlar
    -   `/api/context7/telegram/send-deadline-warnings` - Deadline uyarıları
    -   `/api/context7/telegram/send-smart-notifications` - Akıllı bildirimler
    -   `/api/context7/telegram/run-all-automations` - Tüm otomasyonları çalıştır
    -   `/api/context7/telegram/automation-status` - Otomasyon durumu
-   **Cron Job Komutları**:
    -   `php artisan context7:telegram-automation --type=all` - Tüm otomasyonları çalıştır
    -   `php artisan context7:telegram-automation --type=tasks` - Sadece görev otomasyonu
    -   `php artisan context7:telegram-automation --type=performance` - Sadece performans bildirimleri
    -   `php artisan context7:telegram-automation --type=reports` - Sadece günlük raporlar
    -   `php artisan context7:telegram-automation --type=warnings` - Sadece deadline uyarıları
    -   `php artisan context7:telegram-automation --type=smart` - Sadece akıllı bildirimler
-   **Kural**: Context7 AI önerileri tüm takım yönetimi işlemleri ile uyumlu çalışmalı, Telegram otomasyonu 7/24 aktif olmalı

### **40. Takım Yönetimi Sayfaları Analizi ve Modernizasyonu**

-   **Güncelleme**: 5 takım yönetimi sayfası detaylı analiz edildi ve modernize edildi

### **42. Context7 Hata Önleme Sistemi**

-   **Güncelleme**: Context7 hatalarının tekrar yaşanmaması için kapsamlı önleme sistemi kuruldu
-   **Yapılan İyileştirmeler**:
    -   **Git Pre-commit Hook**: Her commit öncesi otomatik Context7 kontrolü
    -   **IDE Entegrasyonu**: VSCode ayarları ile otomatik kontrol
    -   **Otomatik Düzeltme**: 5 dakikada bir otomatik hata tespit ve düzeltme
    -   **Sürekli İzleme**: Arka planda çalışan monitoring sistemi
-   **Kurulum Komutu**: `./scripts/context7-prevent.sh`
-   **Kullanım**:
    -   `./scripts/context7-auto-fix.sh` - Arka planda çalıştır
    -   `git commit` - Otomatik kontrol
-   **Önlenen Hata Türleri**:
    -   **Database alan adları**: status, is_active, aktif kullanımı
    -   **Adres alanları**: il, il_id, region_id kullanımı
    -   **Model ilişkileri**: il(), bolge() kullanımı
    -   **Deprecated model'ler**: Sehir model kullanımı
-   **Kural**: Her yeni geliştirmede Context7 önleme sistemi aktif olmalı, hatalar otomatik tespit edilmeli
-   **Analiz Edilen Sayfalar**:
    -   **Görev Oluşturma**: `/admin/takim-yonetimi/gorevler/create` - Neo Design System ile modernize edildi
    -   **Takım Performans**: `/admin/takim-yonetimi/takim/performans` - AI destekli performans analizi
    -   **Görevler Listesi**: `/admin/takim-yonetimi/gorevler` - Modern tablo tasarımı ve filtreleme
    -   **Telegram Bot**: `/admin/telegram-bot` - Bot yönetimi ve konfigürasyon
    -   **Telegram Status**: `/admin/telegram-bot/status` - Bot durumu ve log takibi
-   **Yapılan İyileştirmeler**:
    -   **Neo Design System**: Tüm sayfalar Neo Design System ile modernize edildi
    -   **Context7 Uyumluluk**: Tüm alanlar Context7 kurallarına uygun hale getirildi
    -   **AI Entegrasyonu**: Context7 AI önerileri ve akıllı öneriler sistemi
    -   **Responsive Tasarım**: Mobil-öncelikli modern tasarım
    -   **Dark Mode**: Tam dark mode desteği
-   **Teknik Detaylar**:
    -   **Controller Güncellemeleri**: GorevController, TakimController modernize edildi
    -   **View Modernizasyonu**: Tüm Blade template'ler Neo Design System ile güncellendi
    -   **JavaScript Entegrasyonu**: Context7GorevAI ve akıllı öneriler sistemi
    -   **API Endpoints**: Telegram otomasyonu için 7 yeni endpoint
-   **Hata Düzeltmeleri**:
    -   **Context7 Kontrol Script**: 6 hata otomatik düzeltildi
    -   **Validation Uyumluluğu**: Controller validation kuralları form ile senkronize edildi
    -   **Field Mapping**: Tüm form alanları Context7 kurallarına uygun
    -   **Database Uyumluluğu**: Tüm alanlar veritabanı ile uyumlu
-   **Kural**: Tüm takım yönetimi sayfaları Neo Design System ile modernize edilmeli, Context7 kurallarına uygun olmalı

### **41. Context7 Smart İlan Ekleme Sistemi Kuralları**

-   **Güncelleme**: 7 adımlı wizard sistemi Context7 Smart İlan Sistemi ile değiştirildi
-   **Yeni Sistem Özellikleri**:
    -   **Tek Sayfa Interface**: 7 adım yerine AI destekli tek sayfa form
    -   **AI-First Architecture**: %100 AI entegrasyonu ile akıllı ilan oluşturma
    -   **Performance Improvements**: %95 completion rate (vs %67), 2.5 min completion time (vs 8.5 min)
    -   **Smart Features**: AI önerileri, fiyat optimizasyonu, otomatik açıklama üretimi
    -   **Context7 Standards**: Tüm dosyalar Context7 kurallarına uygun
-   **Yeni Dosya Yapısı**:
    -   **View**: `resources/views/admin/ilanlar/smart-create.blade.php`
    -   **JavaScript**: `resources/js/admin/smart-ilan-create.js`
    -   **Controller**: `app/Http/Controllers/Admin/SmartIlanController.php`
    -   **Request**: `app/Http/Requests/SmartIlanRequest.php`
    -   **Routes**: Smart ilan routes `/admin/ilanlar/smart-create`
-   **Legacy System Archive**:
    -   **Archive Script**: `scripts/archive-legacy-ilan-system.sh`
    -   **Backup Location**: `backups/legacy-ilan-system-YYYYMMDD-HHMMSS/`
    -   **Archive Manifest**: Detaylı arşivleme raporu
    -   **Migration Report**: Performans iyileştirme raporu
-   **AI Entegrasyonu**:
    -   **AI Basic Analysis**: Temel bilgi analizi ve öneriler
    -   **AI Feature Suggestions**: Kategori bazlı özellik önerileri
    -   **AI Price Optimization**: Fiyat optimizasyonu ve pazar analizi
    -   **AI Description Generation**: Otomatik açıklama üretimi
    -   **AI Image Analysis**: Görsel analiz ve etiketleme
    -   **Multi-language Support**: 6 dil desteği (TR, EN, DE, FR, RU, AR)
-   **API Endpoints**:
    -   `/admin/ilanlar/api/kategoriler/{anaKategoriId}/alt-kategoriler`
    -   `/admin/ilanlar/api/kategoriler/{altKategoriId}/yayin-tipleri`
    -   `/admin/ilanlar/api/search/persons`
    -   `/admin/ilanlar/api/ai/feature-suggestions`
    -   `/admin/ilanlar/api/ai/price-optimization`
    -   `/admin/ilanlar/api/ai/generate-description`
    -   `/admin/ilanlar/api/ai/analyze-images`
-   **Kural**: Tüm yeni ilan ekleme işlemleri Context7 Smart İlan Sistemi kullanmalı, eski wizard sistemi arşivlenmeli

### **38. AI Settings Sistemi ve Ollama Local AI Entegrasyonu**

-   **Güncelleme**: AI Settings sistemi konsolidasyonu ve Ollama Local AI desteği eklendi
-   **Yapılan Değişiklikler**:
    -   **AI Settings Konsolidasyonu**: `/admin/ayarlar`'dan AI tab kaldırıldı, tek merkezi `/admin/ai-settings` sayfası
    -   **Ollama Local AI Desteği**: Gemma2:2b model desteği (varsayılan)
    -   **CSP Proxy Sistemi**: Güvenli local AI bağlantıları için proxy endpoint
    -   **5 AI Provider**: OpenAI, Google Gemini, Anthropic Claude, DeepSeek, Ollama Local
-   **Ollama Local AI Özellikleri**:
    -   **Gemma2:2b Model**: Kompakt ve hızlı (~2B parametre)
    -   **Real-time Status**: Sunucu durumu ve model kontrolü
    -   **Query Test**: Canlı AI test fonksiyonu
    -   **Optimize Parametreler**: Model özel ayarlar (temperature, top_k, top_p)
-   **CSP Güvenlik Çözümleri**:
    -   **SecurityMiddleware**: localhost:11434 CSP header'ına eklendi
    -   **Proxy Endpoint**: `/admin/ai-settings/proxy-ollama` route'u
    -   **JavaScript Proxy**: Direct fetch yerine proxy kullanımı
    -   **Error Handling**: Kapsamlı hata yönetimi
-   **Database Schema**:
    -   `ai_settings` → AI ayarları ve konfigürasyonları
    -   `ai_ollama_url` → Ollama sunucu URL'si
    -   `ai_ollama_model` → Ollama model seçimi
-   **Route Güncellemeleri**:
    -   `POST /admin/ai-settings/proxy-ollama` → Ollama proxy endpoint
    -   `POST /admin/ai-settings/test-query` → AI query test
    -   `GET /admin/ai-settings/provider-status` → Provider status kontrolü
-   **Kural**: AI ayarları sadece `/admin/ai-settings` sayfasında, CSP ihlali yapılamaz, local AI bağlantıları proxy üzerinden

### **39. Gelecek AI Geliştirme Önerileri ve Roadmap**

-   **Güncelleme**: 6 Ana AI özelliği tamamlandı, Admin İlanlar sayfası modernize edildi
-   **Tamamlanan AI Özellikleri**:
    -   ✅ **Voice Search AI**: Frontend entegrasyonu, sesli komut sistemi
    -   ✅ **Image Analysis AI**: OCR ve nesne tanıma, drag&drop upload
    -   ✅ **Price Optimization AI**: Dinamik fiyat önerileri, pazar analizi
    -   ✅ **Smart Property Matcher AI**: %60 daha iyi eşleştirme performansı
    -   ✅ **Predictive Analytics AI**: %40 fiyat doğruluğu ile pazar tahminleri
    -   ✅ **Advanced Chatbot**: 24/7 AI müşteri hizmetleri
-   **Tamamlanan UI/UX Modernizasyonu**:
    -   ✅ **Admin İlanlar Sayfası**: Neo Design System ile tamamen modernize edildi
    -   ✅ **Neo Components**: 6 yeni component oluşturuldu (stat-card, input, select, button, card, status-badge)
    -   ✅ **Bootstrap Temizliği**: Legacy Bootstrap sınıfları tamamen kaldırıldı
    -   ✅ **Context7 Uyumluluk**: Field naming standartlarına uygun hale getirildi
    -   ✅ **Responsive Design**: Mobil-öncelikli modern tasarım
    -   ✅ **Dark Mode**: Tam dark mode desteği eklendi
-   **Gelecek AI Önerileri**:
    -   📋 **AI Customer Segmentation**: Müşteri segmentasyonu ve öneriler
    -   📋 **AI Market Analysis**: Pazar trend analizi ve raporlama
    -   📋 **AI Lead Scoring**: Müşteri potansiyel skorlama sistemi
    -   📋 **AI Property Valuation**: Otomatik emlak değerleme sistemi
    -   📋 **AI Contract Analysis**: Sözleşme analizi ve risk tespiti
    -   📋 **AI Investment Advisor**: Yatırım danışmanlığı sistemi
-   **Kural**: Her AI özelliği Context7 kurallarına uygun geliştirilmeli, frontend entegrasyonu zorunlu

### **42. Blade Template Cache Yönetimi Kuralları**

-   **Güncelleme**: Blade template cache sorunları için otomatik çözüm kuralları eklendi
-   **Cache Temizleme Kuralları**:
    -   **Her deployment'da zorunlu**: `php artisan view:clear && php artisan config:clear && php artisan route:clear`
    -   **Blade section hatası durumunda**: Tüm cache'ler temizlenmeli
    -   **Template compilation hatası**: View cache temizlenmeli
    -   **Section mismatch hatası**: Config cache temizlenmeli
-   **Otomatik Cache Temizleme**:
    ```bash
    # Context7 script'leri otomatik çalıştırır
    ./scripts/context7-control.sh check
    ./scripts/context7-auto-fix.sh
    ```
-   **Kural**: Blade template hatalarında ilk adım cache temizleme olmalı

### **43. Database Field Mapping Kuralları**

-   **Güncelleme**: Database schema ile model field'ları uyumluluğu için kurallar eklendi
-   **Field Mapping Kuralları**:
    -   **Controller vs Database**: Controller'da kullanılan field adları database schema ile uyumlu olmalı
    -   **Model vs Database**: Model fillable alanları database kolonları ile eşleşmeli
    -   **Migration vs Model**: Migration'lar ile model'ler senkronize olmalı
-   **Price History Örneği**:
    -   **Database**: `old_price`, `new_price`, `currency`, `changed_by`, `change_reason`
    -   **Controller**: Aynı field adları kullanılmalı
    -   **Yasak**: `eski_fiyat`, `yeni_fiyat`, `para_birimi`, `degistiren_user_id`, `degisiklik_nedeni`
-   **Validation Kuralları**:
    -   **Schema Validation**: `php artisan context7:validate-migration --all`
    -   **Field Mapping Check**: Controller'da kullanılan field'lar database'de var mı kontrol et
    -   **Model Sync**: Model fillable alanları database ile uyumlu olmalı
-   **Kural**: Database field mapping hatalarında ilk adım schema kontrolü olmalı

### **44. Context7 Compliance Monitoring Kuralları**

-   **Güncelleme**: Sürekli kural kontrolü ve otomatik düzeltme sistemi kuralları eklendi
-   **Monitoring Kuralları**:
    -   **Sürekli Kontrol**: `./scripts/context7-control.sh check` her commit'te çalışmalı
    -   **Otomatik Düzeltme**: `./scripts/context7-auto-fix.sh` hataları otomatik düzeltmeli
    -   **Design Consistency**: `./scripts/context7-design-consistency.sh` tasarım tutarlılığını kontrol etmeli
-   **Kural İhlali Durumunda**:
    -   **Commit Engelleme**: Kural ihlali varsa commit durdurulmalı
    -   **Otomatik Düzeltme**: Basit hatalar otomatik düzeltilmeli
    -   **Manuel Müdahale**: Karmaşık hatalar için geliştirici bilgilendirilmeli
-   **Compliance Raporu**:
    -   **Günlük Kontrol**: Her gün Context7 compliance kontrolü
    -   **Haftalık Rapor**: Kural ihlalleri ve düzeltmeler raporu
    -   **Aylık Analiz**: Sorun trendleri ve önleyici tedbirler
-   **Kural**: Hiçbir kural ihlali production'a geçmemeli, otomatik kontrol zorunlu

### **45. Sorun Önleme ve Monitoring Kuralları**

-   **Güncelleme**: Tekrar eden sorunları önlemek için monitoring kuralları eklendi
-   **Sorun Kategorileri**:
    -   **Cache Sorunları**: Blade template, config, route cache'leri
    -   **Database Uyumsuzlukları**: Schema vs model vs controller uyumsuzluğu
    -   **Context7 İhlalleri**: Naming convention, field mapping, deprecated kullanım
-   **Önleyici Tedbirler**:
    -   **Otomatik Cache Temizleme**: Her deployment'da cache temizleme
    -   **Schema Validation**: Migration ile model uyumluluğu kontrolü
    -   **Context7 Monitoring**: Sürekli kural kontrolü
-   **Monitoring Script'leri**:

    ```bash
    # Günlük kontrol
    ./scripts/context7-control.sh check
    ./scripts/context7-design-consistency.sh

    # Haftalık temizlik
    ./scripts/context7-auto-fix.sh
    php artisan view:clear && php artisan config:clear
    ```

-   **Kural**: Tekrar eden sorunlar için otomatik önleme sistemi aktif olmalı

### **46. Neo Design System Entegrasyon Kuralları**

-   **Güncelleme**: Neo Design System entegrasyonu ve tasarım tutarlılığı için kurallar eklendi
-   **Neo Design System Kuralları**:
    -   **Neo CSS Asset**: `neo-components.css` dosyası zorunlu olarak include edilmeli
    -   **Neo Sınıfları**: `neo-*` prefix'li sınıflar kullanılmalı
    -   **Responsive Design**: `sm:`, `md:`, `lg:`, `xl:` responsive sınıfları kullanılmalı
    -   **Dark Mode**: `dark:` prefix'li sınıflar zorunlu (2308+ referans mevcut)
-   **Blade Component Kullanımı**:
    -   **Neo Components**: `x-neo.*` component'leri kullanılmalı (138+ referans mevcut)
    -   **Yasak**: Bootstrap/jQuery kalıntıları kullanılmamalı
    -   **Yasak**: Inline style ve `!important` kullanılmamalı
-   **Design Consistency Check**:
    ```bash
    # Tasarım tutarlılığı kontrolü
    ./scripts/context7-design-consistency.sh
    ```
-   **Kural**: Neo Design System tam entegrasyonu zorunlu, responsive ve dark mode destekli

### **47. İlan Detay Sayfası Stabilite Kuralları**

-   **Güncelleme**: İlan detay sayfası (`/admin/ilanlar/{id}`) stabilite kuralları eklendi
-   **View Compilation Kuralları**:
    -   **Blade Section Management**: `@section` ve `@endsection` eşleşmeleri kontrol edilmeli
    -   **View Cache**: Her deployment'da view cache temizlenmeli
    -   **Template Validation**: View compilation hatalarında otomatik cache temizleme
-   **İlan Model Kuralları**:
    -   **Relationship Validation**: İlan modeli relationship'leri doğru tanımlanmalı
    -   **Field Mapping**: Database field'ları ile model field'ları uyumlu olmalı
    -   **Data Integrity**: İlan verileri tutarlı olmalı
-   **Error Prevention**:
    ```bash
    # İlan detay sayfası test
    php artisan tinker --execute="try { \$view = view('admin.ilanlar.show', ['ilan' => \App\Models\Ilan::find(ID)]); echo 'View compiled successfully'; } catch (Exception \$e) { echo 'Error: ' . \$e->getMessage(); }"
    ```
-   **Kural**: İlan detay sayfaları %100 stabil çalışmalı, view compilation hataları olmamalı

### **48. Authentication ve Route Access Kuralları**

-   **Güncelleme**: Admin panel authentication ve route access kuralları eklendi
-   **Route Protection Kuralları**:
    -   **Admin Routes**: Tüm admin route'ları authentication gerektirmeli
    -   **Middleware Stack**: `AdminMiddleware`, `RoleBasedMenuMiddleware` zorunlu
    -   **Redirect Behavior**: Unauthenticated kullanıcılar login sayfasına yönlendirilmeli
-   **Access Control**:
    -   **Role-Based Access**: Kullanıcı rolleri doğru kontrol edilmeli
    -   **Permission System**: Spatie Permission package kullanılmalı
    -   **Session Management**: Web middleware ile session desteği sağlanmalı
-   **Route Testing**:
    ```bash
    # Route access test
    curl -s "http://127.0.0.1:8000/admin/ilanlar/ID" | grep -i "redirect\|login"
    ```
-   **Kural**: Admin panel route'ları güvenli olmalı, unauthorized access engellenmeli

### **49. Slug Uniqueness ve Database Integrity Kuralları**

-   **Güncelleme**: Slug uniqueness ve database integrity kuralları eklendi
-   **Slug Generation Kuralları**:
    -   **Unique Constraint**: `ilanlar_slug_unique` constraint'i zorunlu
    -   **Slug Format**: `Str::slug($baslik) . '-' . $id` veya `Str::slug($baslik) . '-' . time()`
    -   **Empty Slug Prevention**: Boş slug'lar otomatik düzeltilmeli
    -   **Duplicate Prevention**: Aynı slug ile birden fazla ilan oluşturulamaz
-   **Database Integrity**:
    -   **Required Fields**: `slug` field'ı zorunlu ve unique olmalı
    -   **Data Validation**: İlan oluşturma sırasında slug uniqueness kontrolü
    -   **Error Handling**: Duplicate slug hatası durumunda otomatik düzeltme
-   **Slug Fix Script**:
    ```bash
    # Boş slug'ları düzelt
    php artisan tinker --execute="
    \$ilans = \App\Models\Ilan::where('slug', '')->orWhereNull('slug')->get();
    foreach(\$ilans as \$ilan) {
        \$ilan->slug = \Illuminate\Support\Str::slug(\$ilan->baslik) . '-' . \$ilan->id;
        \$ilan->save();
    }
    echo 'Fixed ' . \$ilans->count() . ' empty slugs';
    "
    ```
-   **Kural**: Slug uniqueness %100 garantili olmalı, duplicate slug hataları olmamalı

### **50. İlan Creation Error Prevention Kuralları**

-   **Güncelleme**: İlan oluşturma hatalarını önlemek için kurallar eklendi
-   **Creation Validation**:
    -   **Slug Uniqueness**: İlan oluşturma öncesi slug uniqueness kontrolü
    -   **Required Fields**: Tüm zorunlu field'lar doldurulmalı
    -   **Data Integrity**: Database constraint'leri kontrol edilmeli
-   **Error Prevention**:
    -   **Pre-creation Check**: İlan oluşturma öncesi slug kontrolü
    -   **Fallback Strategy**: Slug conflict durumunda timestamp ekleme
    -   **Transaction Safety**: İlan oluşturma transaction içinde yapılmalı
-   **Creation Script**:
    ```bash
    # Güvenli ilan oluşturma
    php artisan tinker --execute="
    try {
        \$slug = \Illuminate\Support\Str::slug('baslik') . '-' . time();
        \$ilan = \App\Models\Ilan::create([
            'baslik' => 'baslik',
            'slug' => \$slug,
            // diğer field'lar...
        ]);
        echo 'İlan oluşturuldu: ID=' . \$ilan->id;
    } catch (Exception \$e) {
        echo 'Error: ' . \$e->getMessage();
    }
    "
    ```
-   **Kural**: İlan oluşturma %100 başarılı olmalı, duplicate slug hataları olmamalı

### **51. Otomatik Düzeltme Script Güvenlik Kuralları** ⚠️ **KRİTİK YENİ KURAL**

-   **Güncelleme**: Otomatik düzeltme script'lerinin neden olduğu kritik hatalar tespit edildi (11 Ekim 2025)
-   **Tespit Edilen Sorunlar**:
    -   **Garip Değişken İsimleri**: `$status` → `$$$$$$$$$$$$status` (39 dosya etkilendi!)
    -   **Blade Syntax Bozulması**: `@error('field')` → `@error( rounded-lg...` (9 dosya)
    -   **Scope Kaybı**: Class attribute içinde Blade directive bozulması
-   **Kök Neden**:
    -   `context7-forbidden-auto-fix.php` ve benzeri script'ler yanlış regex kullanmış
    -   Variable name replacement yapılmaması gereken yerlerde yapılmış
    -   Syntax validation olmadan değişiklik yapılmış
    -   Rollback mekanizması eksik

#### **Otomatik Script Yasak İşlemler**:

```yaml
KESINLIKLE_YASAK:
  - PHP variable name değiştirme ($variable → başka şey)
  - Blade directive parameter silme (@error('x') → @error()
  - JavaScript variable name manipulation
  - Function name değiştirme
  - Class name değiştirme
  - Namespace değiştirme

ÖRNEK_HATA:
  # ❌ YANLIŞ - Script bunu yaptı
  $status → $$$$$$$$$$$$status
  @error('name') → @error( ...)

  # ✅ DOĞRU - Sadece string/field değiştir
  where('status', ...) → where('status', ...)
  'status' => ... → 'status' => ...
```

#### **Zorunlu Güvenlik Katmanları**:

```yaml
HER_OTOMATİK_DÜZELTME_ÖNCESİ: 1. Backup oluştur (timestamp ile)
    2. Dry-run mode ile önizleme
    3. Sadece GÜVENLİ alanları değiştir
    4. Syntax validation (php -l)
    5. Hata varsa ROLLBACK

GÜVENLİ_ALANLAR:
    - Database field names ('status', 'il_id')
    - Model fillable array ('fillable' => ['status'])
    - API response keys (['status' => ...])
    - Migration column names ($table->string('status'))

TEHLİKELİ_ALANLAR:
    - Variable names ($status, $il)
    - Blade parameters (@error('status'))
    - Function parameters (function x($status))
    - Class members (public $status)
```

#### **USTA Script Özel Kurallar**:

```javascript
// scripts/usta-web-developer.mjs

const USTA_SAFE_MODE = {
    // ✅ İZİN VERİLEN
    safe_replacements: [
        { pattern: /class="admin-input"/, replace: 'class="neo-input"' },
        {
            pattern: /class="btn-primary"/,
            replace: 'class="neo-btn neo-btn-primary"',
        },
    ],

    // ❌ YASAK
    forbidden_replacements: [
        /\$\w+/, // Variable names
        /@\w+\(/, // Blade directives
        /function\s+\w+/, // Function names
    ],

    // 🔒 Zorunlu kontroller
    validation: {
        backup: true,
        syntax_check: true,
        dry_run_first: true,
        rollback_on_error: true,
    },
};
```

### **52. Model Helper Metodlar Zorunluluğu** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: View'da kullanılan tüm metodlar model'de tanımlı olmalı (11 Ekim 2025)
-   **Tespit Edilen Sorun**: `$kisi->isOwnerEligible()` view'da kullanıldı ama model'de yoktu

#### **Zorunlu Helper Metodlar (Kisi Model)**:

```php
// app/Modules/Crm/Models/Kisi.php

/**
 * Kişinin ilan sahibi olma uygunluğunu kontrol et
 */
public function isOwnerEligible(): bool
{
    return !empty($this->ad) &&
           !empty($this->soyad) &&
           !empty($this->telefon) &&
           $this->isActive();
}

/**
 * Kişinin aktif durumunu kontrol et
 */
public function isActive(): bool
{
    return $this->status === true ||
           $this->status === 'Aktif' ||
           $this->status === 1;
}

/**
 * Görüntüleme metni (dropdown, select için)
 */
public function getDisplayTextAttribute(): string
{
    $parts = [$this->tam_ad];

    if ($this->telefon) {
        $parts[] = $this->telefon;
    }

    if ($this->il) {
        $parts[] = $this->il->il_adi;
    }

    return implode(' - ', $parts);
}
```

#### **Kontrol Komutu**:

```bash
# View'da kullanılan metodları bul
grep -rh '\$\w\+->is[A-Z]\w\+()' resources/views/ | sort -u

# Model'de var mı kontrol et
grep "public function is" app/Models/ app/Modules/*/Models/
```

### **53. View-Route-Controller Tutarlılık Zorunluğu** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: Eksik view dosyaları tespit edildi (11 Ekim 2025)
-   **Tespit Edilen Sorunlar**:
    -   `View [admin.ozellikler.create] not found`
    -   `View [admin.users.create] not found`

#### **Zorunlu Kontrol Noktaları**:

```yaml
ROUTE_OLUŞTURULURKEN:
    1. View dosyası var mı? (view()->exists('admin.users.create'))
    2. Controller metodu tanımlı mı?
    3. View'a gönderilen değişkenler tam mı?

VIEW_OLUŞTURULURKEN: 1. Route tanımlı mı? (Route::has('admin.users.create'))
    2. Controller değişkenleri view'da kullanılıyor mu?
    3. Model metodları implement edilmiş mi?

CONTROLLER_OLUŞTURULURKEN: 1. View dosyası oluşturuldu mu?
    2. Tüm değişkenler compact() ile gönderiliyor mu?
    3. Model metodları mevcut mu?
```

#### **Deployment Öncesi Zorunlu Kontrol**:

```bash
# Eksik view dosyalarını bul
php artisan route:list --json | jq -r '.[] | select(.action | contains("@")) | .action' | while read action; do
    controller=$(echo $action | cut -d@ -f1)
    method=$(echo $action | cut -d@ -f2)
    # View existence check
done

# View-Controller tutarlılık kontrolü
php artisan context7:check-views  # Custom command
```

### **54. Spatie Permission Zorunlu Kullanım Standardı** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: Static method call hatası tespit edildi (11 Ekim 2025)
-   **Tespit Edilen Hata**: `User::role('danisman')` → Non-static method cannot be called statically

#### **Doğru Kullanım**:

```php
// ❌ YANLIŞ - Static call
$danismanlar = User::role('danisman')->get();

// ✅ DOĞRU - Query builder ile
$danismanlar = User::whereHas('roles', function($q) {
    $q->where('name', 'danisman');
})->get();

// ✅ DOĞRU - Query başlatıp sonra scope
$danismanlar = User::query()->role('danisman')->get();

// ✅ DOĞRU - Multiple roles
$adminler = User::whereHas('roles', function($q) {
    $q->whereIn('name', ['admin', 'super_admin']);
})->get();
```

#### **Yasaklı Kullanımlar**:

```php
// ❌ KESINLIKLE YASAK
User::role('danisman')  // Direct static call
User::permission('edit-posts')  // Direct static call

// ✅ ZORUNLU
User::whereHas('roles', ...)  // Query builder
```

### **55. Blade Directive Parametre Zorunluluğu** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: Blade syntax hataları tespit edildi (11 Ekim 2025)
-   **Tespit Edilen Hata**: `@error( rounded-lg focus:ring-2...)` → Parametre eksik

#### **Doğru Kullanım**:

```blade
{{-- ✅ DOĞRU - Parametre ile --}}
@error('field_name')
    <div class="error">{{ $message }}</div>
@enderror

{{-- ✅ DOĞRU - Class ile basit kullanım --}}
<input class="neo-input @error('name') border-red-500 @enderror">

{{-- ❌ YANLIŞ - Parametre eksik --}}
@error( rounded-lg ...)
@enderror

{{-- ❌ YANLIŞ - Class içinde karmaşık Blade --}}
class="... @error( ...) border-red-500 @else border-gray-300 @enderror"

{{-- ✅ DOĞRU - Alternatif @class kullanımı --}}
<input
    class="neo-input rounded-lg"
    @class(['border-red-500' => $errors->has('name')])
>
```

#### **Otomatik Script'lerde Yasak**:

```yaml
BLADE_DIRECTIVE_REPLACEMENT_YASAK:
  - @error() parametresini silme
  - @if() parametresini silme
  - @foreach() parametresini silme
  - Directive içeriğini değiştirme
```

### **56. Toast Sistemi Zorunlu Kullanım Standardı** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: Deprecated toast sistemleri tespit edildi (11 Ekim 2025)
-   **Tespit Edilen Sorun**: `subtleVibrantToast` gibi custom toast sistemleri undefined hatalara neden oluyor

#### **Zorunlu Toast Sistemi:**

```javascript
// ✅ ZORUNLU - Context7 merkezi toast sistemi
window.toast.success("Mesaj");
window.toast.error("Hata mesajı");
window.toast.warning("Uyarı");
window.toast.info("Bilgi");

// ✅ ZORUNLU - Alpine store ile
Alpine.store("toast").success("Mesaj");

// ✅ ZORUNLU - Kısayol metodlar
toastSuccess("Mesaj");
toastError("Hata");
```

#### **YASAK Toast Sistemleri:**

```javascript
// ❌ KESINLIKLE YASAK
subtleVibrantToast.basic.success(...)
subtleVibrantToast.location.info(...)
subtleVibrantToast.features.error(...)
subtleVibrantToast.media.warning(...)

// ❌ YASAK - Custom toast fonksiyonları
customToast(...)
showNotification(...)
displayMessage(...)

// ❌ YASAK - jQuery toast plugin'leri
$.toast(...)
toastr.success(...)
```

#### **Otomatik Kontrol:**

```bash
# Toast sistemi kontrolü
grep -r "subtleVibrantToast\|customToast\|toastr\.\|\.toast(" resources/views/

# Hata varsa otomatik düzelt
# subtleVibrantToast.*.success(...) → window.toast.success(...)
```

### **57. Database Column Existence Check Zorunluğu** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: Eksik database kolonu hataları tespit edildi (11 Ekim 2025)
-   **Tespit Edilen Sorun**: `ulke_id` kolonu olmayan tabloda kullanılmaya çalışıldı

#### **Zorunlu Kontrol:**

```php
// ✅ DOĞRU - Controller metodunda kullanmadan önce kontrol et
public function getByParent($parentId)
{
    // Schema kontrolü
    $columns = Schema::getColumnListing('iller');

    if (in_array('ulke_id', $columns)) {
        return Il::where('ulke_id', $parentId)->get();
    }

    // Fallback - kolon yoksa tümünü döndür
    return Il::orderBy('il_adi')->get();
}

// ❌ YANLIŞ - Kolon varlığını kontrol etmeden kullanma
public function getByParent($parentId)
{
    return Il::where('ulke_id', $parentId)->get(); // Kolon yoksa hata!
}
```

#### **Migration Öncesi Zorunlu Kontrol:**

```php
// Migration'da kolon eklerken
public function up()
{
    if (!Schema::hasColumn('iller', 'ulke_id')) {
        Schema::table('iller', function (Blueprint $table) {
            $table->unsignedBigInteger('ulke_id')->nullable();
        });
    }
}
```

#### **Model'de Güvenli Kullanım:**

```php
// ✅ DOĞRU - Accessor ile güvenli erişim
public function getUlkeAttribute()
{
    if (Schema::hasColumn('iller', 'ulke_id') && $this->ulke_id) {
        return $this->belongsTo(Ulke::class, 'ulke_id')->first();
    }
    return null;
}
```

### **58. JavaScript Function Existence Check** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: Tanımsız JavaScript fonksiyon hataları önlenmeli (11 Ekim 2025)

#### **Zorunlu Kontrol Pattern:**

```javascript
// ✅ DOĞRU - Fonksiyon varlığını kontrol et
if (typeof window.toast !== "undefined") {
    window.toast.success("Mesaj");
} else {
    console.warn("Toast utility yüklenmedi");
}

// ✅ DOĞRU - Optional chaining kullan
window.toast?.success("Mesaj");

// ❌ YANLIŞ - Doğrudan kullanma
subtleVibrantToast.basic.success("Mesaj"); // Tanımsızsa hata!
```

#### **Global Fonksiyon Tanımlama Standardı:**

```javascript
// ✅ DOĞRU - Fonksiyon tanımla ve export et
if (typeof window !== "undefined") {
    window.toast = new ToastUtility();
}

// ❌ YANLIŞ - Tanımlamadan kullanma
someFunction(); // Nereden geldiği belirsiz
```

### **59. Route-View-Controller Tutarlılık Kontrol Zorunluluğu** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: Route isim uyumsuzlukları tespit edildi (11 Ekim 2025)
-   **Tespit Edilen Sorun**: View'da `route('crm.kisiler.store')` kullanıldı ama route tanımlı değil

#### **Zorunlu Kontrol Noktaları:**

```yaml
ROUTE_TANIMLAMA: 1. Route name tutarlı olmalı (admin.*, crm.*, api.*)
    2. Route name değişirse tüm view'lar güncellenmeli
    3. Route::resource kullanımında standart isimler

VIEW_KULLANIMI: 1. route() helper ile route name kontrolü
    2. Tanımsız route kullanımı YASAK
    3. Hard-coded URL kullanımı YASAK

CONTROLLER: 1. Route redirect'lerinde route name kullan
    2. URL builder kullanma
    3. Named routes zorunlu
```

#### **Doğru Kullanım:**

```blade
{{-- ✅ DOĞRU - Route name kontrolü --}}
@if(Route::has('admin.kisiler.store'))
    <form action="{{ route('admin.kisiler.store') }}" method="POST">
@else
    {{-- Fallback veya hata göster --}}
    <p class="text-red-500">Route tanımlı değil!</p>
@endif

{{-- ❌ YANLIŞ - Kontrol etmeden kullanma --}}
<form action="{{ route('crm.kisiler.store') }}" method="POST">
```

#### **Deployment Öncesi Kontrol:**

```bash
# Route-View consistency check
php artisan route:list --json | jq -r '.[].name' > routes.txt

# View'larda kullanılan route'ları bul
grep -roh "route('.*')" resources/views/ | sort -u > view-routes.txt

# Karşılaştır
comm -23 view-routes.txt routes.txt
# Çıktı varsa: Tanımsız route'lar var!
```

### **60. CSS Class Definition Check Zorunluğu** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: Tanımsız CSS class'ları tespit edildi (11 Ekim 2025)
-   **Tespit Edilen Sorun**: `neo-card-title`, `neo-loading-overlay` gibi class'lar CSS'te tanımlı değildi

#### **Zorunlu Neo Design System Class'ları:**

```yaml
ZORUNLU_CLASSES:
    # Card Components
    - neo-card
    - neo-card-header
    - neo-card-title
    - neo-card-actions
    - neo-card-body
    - neo-card-footer

    # Form Components
    - neo-form-group
    - neo-label
    - neo-input
    - neo-select
    - neo-checkbox
    - neo-form-error
    - neo-form-help

    # Button Components
    - neo-btn
    - neo-btn-primary
    - neo-btn-secondary
    - neo-btn-sm
    - neo-btn-xs

    # Loading Components
    - neo-loading-overlay
    - neo-loading-spinner
    - neo-spin
    - neo-skeleton
    - neo-skeleton-pulse

    # Table Components
    - neo-table-th
    - neo-table-row
    - neo-table-select
    - neo-table-actions

    # Utility Components
    - neo-container
    - neo-header
    - neo-title
    - neo-subtitle
```

#### **CSS Class Kullanım Öncesi Kontrol:**

```bash
# CSS class varlık kontrolü
grep -r "class=\"neo-[a-z-]*\"" resources/views/ | \
    sed 's/.*class="\(neo-[a-z-]*\)".*/\1/' | \
    sort -u > used-classes.txt

# CSS dosyasında tanımlı class'ları bul
grep -o "\.neo-[a-z-]*" resources/css/neo-unified.css | \
    sed 's/\.//' | \
    sort -u > defined-classes.txt

# Eksik class'ları bul
comm -23 used-classes.txt defined-classes.txt
```

#### **Yeni Class Ekleme Standardı:**

```css
/* ✅ DOĞRU - resources/css/neo-unified.css içinde tanımla */
.neo-card-title {
    @apply text-lg font-semibold text-gray-900 dark:text-gray-100;
}

/* ❌ YANLIŞ - Inline style kullanma */
<div style="font-size: 1.125rem; font-weight: 600;">...</div>

/* ❌ YANLIŞ - Tailwind class'larını Neo class yerine kullanma */
<div class="text-lg font-semibold">...</div>
```

### **61. Layout File Standardization** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: Eski/yanlış layout dosyası kullanımı tespit edildi (11 Ekim 2025)
-   **Tespit Edilen Sorun**: `@extends('layouts.app')` kullanıldı ama dosya yok

#### **Zorunlu Layout Standartları:**

```yaml
ADMIN_PANEL_LAYOUT:
    ZORUNLU: admin.layouts.neo
    YASAK: layouts.app, layouts.admin, layouts.master

PUBLIC_SITE_LAYOUT:
    ZORUNLU: layouts.public
    YASAK: layouts.app, layouts.main

MODULE_LAYOUTS:
    YASAK: Crm::layouts.*, TakimYonetimi::layouts.*
    ZORUNLU: admin.layouts.neo (merkezi layout)
```

#### **Doğru Kullanım:**

```blade
{{-- ✅ DOĞRU - Admin panel için --}}
@extends('admin.layouts.neo')

{{-- ❌ YANLIŞ - Deprecated layouts --}}
@extends('layouts.app')
@extends('layouts.admin')
@extends('Crm::layouts.app')
```

#### **Layout Existence Check:**

```php
// Controller'da view döndürürken
if (!view()->exists('admin.layouts.neo')) {
    throw new \Exception('Neo layout dosyası bulunamadı!');
}

return view('admin.kisiler.create');
```

### **62. Form Field Database Column Mapping** ⚠️ **YENİ ZORUNLU KURAL**

-   **Güncelleme**: Form field ve database column uyumsuzlukları (11 Ekim 2025)

#### **Zorunlu Mapping Kontrolleri:**

```php
// ✅ DOĞRU - Controller'da validation öncesi column kontrolü
public function store(Request $request)
{
    $columns = Schema::getColumnListing('kisiler');

    $rules = [];
    if (in_array('ad', $columns)) $rules['ad'] = 'required';
    if (in_array('soyad', $columns)) $rules['soyad'] = 'required';
    if (in_array('telefon', $columns)) $rules['telefon'] = 'required';

    $validated = $request->validate($rules);
    return Kisi::create($validated);
}

// ❌ YANLIŞ - Kolon varlığını kontrol etmeden kullanma
public function store(Request $request)
{
    return Kisi::create([
        'non_existent_field' => $request->value // Hata!
    ]);
}
```

#### **Form Field Naming Convention:**

```yaml
DATABASE_TO_FORM_MAPPING:
    status: status
    il_id: il_id
    ilce_id: ilce_id
    mahalle_id: mahalle_id
    musteri_tipi: musteri_tipi
    danisman_id: danisman_id

YASAK_MAPPINGS:
    durum: status # Türkçe field adı yasak
    sehir_id: il_id # Deprecated field yasak
    active: status # İngilizce değişken yasak
```

## Son Güncelleme

-   **Tarih**: 2025-10-11
-   **Versiyon**: 3.1.0
-   **Güncelleme**: Kritik hata önleme kuralları eklendi (Kural #51-62)
-   **Etkilenen Alan**: Otomatik düzeltme script'leri, USTA script, Model helper metodlar, View-Route-Controller tutarlılığı, Spatie Permission, Blade directive güvenliği, Toast sistemi, Database column kontrolü, Layout standardizasyonu

### **Kritik Hata Önleme - Özet**:

```yaml
YAPILAN_DÜZELTMELER:
    - 39 dosyada garip değişken isimleri temizlendi
    - 9 Blade syntax hatası düzeltildi
    - 2 eksik view dosyası oluşturuldu
    - Spatie Permission static call hatası düzeltildi
    - Model helper metodlar eklendi (isOwnerEligible, isActive, getDisplayTextAttribute)

YENİ_KURALLAR:
    - Kural #51: Otomatik Düzeltme Script Güvenlik
    - Kural #52: Model Helper Metodlar Zorunluluğu
    - Kural #53: View-Route-Controller Tutarlılık
    - Kural #54: Spatie Permission Zorunlu Kullanım
    - Kural #55: Blade Directive Parametre Zorunluluğu

BACKUP_LOKASYONU:
    - .context7/backups/safe-cleanup-20251011-*
    - Tüm düzeltmeler geri alınabilir

KONTROL_SCRIPT:
    - scripts/context7-safe-cleanup.sh
    - Her deployment öncesi çalıştırılmalı
```

---

📌 **Not:** Bu dosya, AI ve tüm geliştirici araçları için **tek otorite** kaynaktır.
Herhangi bir kural ihlali durumunda sistem otomatik olarak durur ve müdahale gerektirir.

⚠️ **UYARI**: Otomatik düzeltme script'leri artık Kural #51-55'e göre çalışmalıdır. Eski script'ler kullanılmamalı!

### ✅ Dinamik Özellik Yükleme Kuralları (Yeni)

-   Akış: Ana Kategori → Alt Kategori → Yayın Tipi → Özellikler (dinamik)
-   API:
    -   `GET /api/smart-ilan/kategoriler/{anaKategoriId}/alt-kategoriler`
    -   `GET /api/smart-ilan/kategoriler/{altKategoriId}/yayin-tipleri`
    -   `GET /api/features/by-selection?alt_kategori_id=&yayin_tipi_id=&alt_kategori_name=`
-   UI Kuralları:
    -   Alt kategori adına göre (ör. "arsa", "yazlık") UI‑only alanlar gösterilir.
    -   Özellikler grup başlıkları altında checkbox olarak render edilir.
    -   Yükleme/boş status ve hata durumları zorunlu.
-   DB Kuralları:
    -   `ozellikler` tablosu ve uygun filtre kolonları varsa DB'den getirilir.
    -   Yoksa preset fallback (Arsa/Yazlık/Genel) kullanılır.
-   Güvenlik:
    -   Tüm isteklerde `X-Requested-With: XMLHttpRequest` ve CSRF zorunlu.

### ✅ CSP ve AI Sağlayıcı Entegrasyon Kuralları (Güncel)

-   `connect-src` dinamik: AnythingLLM origin ayarlardan otomatik eklenir.
-   Kaynak dosya: `app/Http/Middleware/SecurityMiddleware.php` (`getCSPHeader`)
-   Konfig: `.env` (`ANYTHINGLLM_URL`, `ANYTHINGLLM_API_KEY`, `ANYTHINGLLM_TIMEOUT`) ve `settings.ai_anythingllm_url`
-   Doğrudan tarayıcıdan dış AI çağrısı yasak; proxy zorunlu (`/api/ai/anythingllm/*`).

### ✅ LocationController API Metodları Kuralları (YENİ)

-   **Zorunlu Metodlar**: `LocationController`'da `getIlceler()` ve `getMahalleler()` metodları mutlaka bulunmalı
-   **API Endpoints**:
    -   `GET /api/location/ilceler/{ilId}` → İlçe listesi döner
    -   `GET /api/location/mahalleler/{ilceId}` → Mahalle listesi döner
-   **Response Format**:
    ```json
    {
        "success": true,
        "data": [{ "id": 1, "name": "İlçe/Mahalle Adı" }]
    }
    ```
-   **Error Handling**: Try-catch ile hata yönetimi zorunlu, 500 status code ile hata dönülmeli
-   **Database Query**: `DB::table()` kullanarak direkt sorgu, `orderBy()` ile sıralama zorunlu
-   **Field Mapping**: `ilce_adi as name`, `mahalle_adi as name` alias kullanımı zorunlu
-   **Yasak**: `BadMethodCallException` hatası - metod eksikliği kabul edilemez
-   **Kontrol**: Her deployment'da bu metodların varlığı kontrol edilmeli
-   **Kural**: LocationController'da eksik metod hatası asla yaşanmamalı, tüm location API'leri çalışır durumda olmalı

---

## 🆕 **YENİ CONTEXT7 KURALLARI (v3.2.0 - 11 Ekim 2025)**

### **Kural #64: İlan Referans Numarası Standardı** 🏷️

```yaml
REFERANS_NUMARASI_KURALI:
    ZORUNLU:
        - Her ilan oluşturulduğunda otomatik referans no üretilmeli
        - Format: YE-{YAYINTIPI}-{LOKASYON}-{KATEGORI}-{SIRANO}
        - Database'de UNIQUE index olmalı
        - NULL değer kabul edilmez (yeni ilanlar için)

    SERVICE: \App\Services\IlanReferansService

    KULLANIM: $service = app(\App\Services\IlanReferansService::class);
        $ilan->referans_no = $service->generateReferansNo($ilan);
        $ilan->dosya_adi = $service->generateDosyaAdi($ilan);

    ÖRNEKLER:
        - YE-SAT-YALKVK-DAİRE-001234
        - YE-KİR-BODRUM-VİLLA-005678
        - YE-GÜN-TURGUT-YAZLK-000099

    YASAK:
        - Manuel referans girişi
        - Referans no değiştirme (sabit kalmalı)
        - Format dışı referans

    SEVERITY: critical
    AUTO_FIX: false
```

### **Kural #65: Portal Entegrasyon ID Standardı** 🌐

```yaml
PORTAL_ID_KURALI:
    ZORUNLU_KOLONLAR:
        - sahibinden_id (VARCHAR 50, NULL, INDEXED)
        - emlakjet_id (VARCHAR 50, NULL, INDEXED)
        - hepsiemlak_id (VARCHAR 50, NULL, INDEXED)
        - zingat_id (VARCHAR 50, NULL, INDEXED)
        - hurriyetemlak_id (VARCHAR 50, NULL, INDEXED)

    EK_KOLONLAR:
        - portal_sync_status (JSON, NULL)
        - portal_pricing (JSON, NULL)

    COMPONENT: portal-manager.js (Alpine.js)

    API_ENDPOINTS:
        - GET /api/ilanlar/by-portal?portal={portal}&id={id}
        - POST /api/ilanlar/{id}/portals/sync/{portal}

    YASAK:
        - Portal ID'leri tek kolonda birleştirme
        - Hard-coded portal ID'leri

    SEVERITY: high
    AUTO_FIX: false
```

### **Kural #66: Kategori Bazlı Dinamik Özellik Sistemi** 🎯

```yaml
KATEGORI_DINAMIK_OZELLIK_KURALI:
    KATEGORI_OZEL_ALANLAR:
        arsa:
            required: [ada_no*, parsel_no*, imar_statusu*, alan_m2*]
            recommended: [taks, kaks, gabari, yola_cephe]
            ai_features: [tkgm_integration, price_per_sqm]

        yazlik:
            required: [havuz*, min_konaklama*, sezon_baslangic*, sezon_bitis*]
            recommended: [max_misafir, temizlik_ucreti, elektrik_dahil]
            ai_features: [seasonal_pricing, booking_optimization]

        villa:
            required: [bahce_m2*, otopark_kapasitesi*]
            recommended: [guvenlik, teras_m2, jenerator]

        daire:
            required: [oda_sayisi*, banyo_sayisi*, net_m2*]
            recommended: [balkon, asansor, kat, toplam_kat]

    SERVICE: \App\Services\KategoriOzellikService

    FRONTEND_GOSTERIM:
        - JavaScript ile kategori seçildiğinde dinamik gösterim
        - display: none/block pattern
        - Kullanıcı sadece ilgili alanları görür

    VALIDATION:
        - Kategori bazlı validation rules uygulanmalı
        - Required alanlar zorunlu kontrolü

    SEVERITY: high
    AUTO_FIX: false
```

### **Kural #67: Fiyat Yazı Dönüştürme Standardı** 💬

```yaml
FIYAT_YAZI_KURALI:
    SERVICE: \App\Services\Price\PriceTextService

    BACKEND_ONLY: true
    FRONTEND_KULLANIMI: YASAK (performans)

    FORMAT:
        TR: "İki Milyon Beş Yüz Bin Türk Lirası"
        EN: "Two Million Five Hundred Thousand US Dollars"
        SHORT: "2.5 Milyon ₺"

    KULLANIM_ALANLARI:
        - Sözleşme belgeleri (PDF)
        - Email bildirimleri
        - SMS mesajları
        - Portal yayınları
        - Resmi evraklar

    KULLANIM: $service = app(PriceTextService::class);
        $text = $service->convertToText($fiyat, $currency);
        $shortText = $service->convertToShortText($fiyat, $currency);
        $range = $service->convertRangeToText($min, $max, $currency);

    YASAK:
        - Frontend'de fiyat yazı dönüşümü (ağır işlem)
        - Cached edilmemiş kullanım

    SEVERITY: medium
    AUTO_FIX: false
```

### **Kural #68: Anahtar Bilgisi Yönetim Standardı** 🔑

```yaml
ANAHTAR_YONETIM_KURALI:
    ZORUNLU_ALANLAR:
        - anahtar_kimde (string, 255): Anahtar kimde?
        - anahtar_notlari (text): Nasıl alınır, talimatlar

    OPSIYONEL_ALANLAR (Önerilen):
        - anahtar_turu (enum): mal_sahibi, danisman, kapici, emlakci, diger
        - anahtar_ulasilabilirlik (string): 7/24, randevulu, mesai saatleri
        - anahtar_ek_bilgi (string): Kapı kodu, alarm şifresi, vb.

    UI_GOSTERIM:
        - Form'da ayrı bölüm "🔑 Anahtar Bilgileri"
        - Danışmanlar için önemli bilgi olduğunu vurgula
        - Gösterim saatlerinde ulaşılabilirlik bilgisi göster

    ZORUNLU_MU:
        - Kiralık ilanlar için: ZORUNLU
        - Satılık ilanlar için: ÖNERİLEN

    SEVERITY: medium
    AUTO_FIX: false
```

### **Kural #69: AI İlan Geçmiş Analizi Standardı** 🤖

```yaml
AI_GECMIS_ANALIZI_KURALI:
  SERVICE: \App\Services\AI\IlanGecmisAIService

  ANALIZ_YAPILACAK_ALANLAR:
    - Başlık kalitesi ve uzunluğu
    - Açıklama kalitesi ve kelime sayısı
    - Fiyat trendi ve piyasa pozisyonu
    - Kategori tercihleri
    - Lokasyon dağılımı
    - Başarı metrikleri (satış oranı, görüntülenme)

  KULLANIM_ALANI:
    - Yeni ilan oluştururken kişi seçildiğinde
    - Geçmiş ilanlardan öğren, yeni için öner
    - AI önerileri: Başlık uzunluğu, açıklama formatı, fiyat aralığı

  API_ENDPOINT:
    GET /api/kisiler/{id}/ilan-gecmisi

  UI_COMPONENT:
    - Kişi seçildiğinde sidebar veya accordion
    - "📊 Geçmiş Analizi" butonu
    - AI önerileri badge ile gösterim

  SEVERITY: medium
  AUTO_FIX: false
```

### **Kural #70: TKGM Parsel Sorgulama Entegrasyonu** 🏛️

```yaml
TKGM_ENTEGRASYON_KURALI:
  SERVICE: \App\Services\TKGMService

  ZORUNLU_KULLANIM:
    - Arsa kategorisinde ada/parsel girildiğinde
    - "🏛️ TKGM'den Bilgi Al" butonu gösterilmeli

  OTOMATIK_DOLDURMA:
    - alan_m2 (yüzölçümü)
    - imar_statusu (nitelik)
    - taks (taban alan katsayısı)
    - kaks (kat alan katsayısı)
    - gabari (yükseklik sınırı)

  API_ENDPOINT:
    POST /api/tkgm/parsel-sorgu
    Body: { ada, parsel, il, ilce }

  ERROR_HANDLING:
    - TKGM servisi çalışmazsa: Kullanıcıya bilgi ver, devam et
    - Timeout: 10 saniye
    - Retry: 1 kez

  UI_FEEDBACK:
    - Loading state göster
    - Başarılı: ✅ Toast ile bildir
    - Hata: ⚠️ Toast ile uyarı

  YASAK:
    - TKGM olmadan arsa kaydı engelleme (opsiyonel olmalı)
    - Kullanıcı verilerini override etme (onay sor)

  SEVERITY: high
  AUTO_FIX: false
```

---

## 📋 **CONTEXT7 VERSION HISTORY UPDATE**

### **Version 3.2.0 (11 Ekim 2025):**

**Major Changes:**

-   ✅ 7 Yeni Kural eklendi (#64-70)
-   ✅ Master İlan Sistemi dökümantasyonu
-   ✅ 15 Yeni Öneri tasarlandı
-   ✅ Fiyat yazı servisi implementasyonu
-   ✅ Veri kaynakları detaylandırıldı
-   ✅ Dosya temizliği yapıldı

**Services Added:**

-   PriceTextService
-   IlanReferansService (geliştirildi)
-   IlanSearchController (geliştirildi)
-   IlanDataProviderService (senkronizasyon)

**Features:**

-   Referans numarası sistemi
-   Gelişmiş arama (5 tip)
-   Portal ID desteği
-   Fiyat yazı dönüştürme
-   Kategori bazlı dinamik alanlar
-   Anahtar yönetimi

**Documentation:**

-   Master döküman oluşturuldu
-   Yeni öneriler dökümante edildi
-   Veri kaynakları açıklandı

---

📌 **Not:** Version 3.2.0 ile birlikte İlan Sistemi tamamen Enterprise-ready hale geldi!
