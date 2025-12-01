# Yalihan Emlak Projesi – Mimari ve Çalışma Mantığı (ChatGPT İçin)

Bu doküman, bu repo içindeki kodu okuyan **ChatGPT / diğer LLM ajanlarının** projeyi daha hızlı anlaması için hazırlanmıştır. Amaç, temel mimariyi, dizin yapısını ve tipik istek akışını netleştirmektir.

## 1. Genel Mimari

- Backend framework: **Laravel (PHP)**
- Frontend / asset katmanı: **Vite + Tailwind + JS/TS** ("resources" ve "public" klasörleri üzerinden)
- Veritabanı: Laravel **Eloquent ORM** ile erişilen ilişkisel veritabanı (MySQL/PostgreSQL vb.)
- Testler: `tests/` altında **PHPUnit** testleri; JS tarafında **Vitest** konfigürasyonu mevcut
- Yardımcı scriptler: `build-assets.sh`, `test-api.sh`, `quick-start.sh` vb. bash scriptleri

Yüksek seviyede sistem:

1. HTTP/CLI istekleri **Laravel uygulamasına** gelir.
2. İstek uygun **route dosyasında** (`routes/*.php`) bir route ile eşleşir.
3. Route ilgili **Controller** metoduna gider (`app/Http/Controllers/...`).
4. Controller, **Request**/Validation, **Service** katmanı, **Model**ler ve gerekirse **Events/Jobs** üzerinden işi yapar.
5. Sonuç, **JSON API cevabı** veya **Blade/Frontend view** olarak döner.

## 2. Dizin Yapısı Özeti

Bu bölümde, ChatGPT'nin kod ararken bilmesi gereken ana klasörler özetlenir.

### Kök dizin (repo root)

Önemli dosya/klasörler:

- `composer.json`, `composer.lock` – PHP bağımlılıkları ve Laravel projesi tanımı.
- `package.json`, `package-lock.json` – Node/Vite/Tailwind ve JS/TS bağımlılıkları.
- `artisan` – Laravel CLI entry point.
- `docker-compose.*.yml` – Elasticsearch, n8n vb. harici servisler için docker tanımları.
- `vite.config.js`, `tailwind.config.js`, `postcss.config.cjs`, `eslint.config.js`, `vitest.config.ts` – frontend build, stil ve test konfigürasyonları.
- Çeşitli analiz dokümanları: `*_ANALIZ_*.md`, `MASTER_PROMPT_YALIHAN_EMLAK_AI.md` vb.

### `app/`

Laravel uygulamasının ana kodu:

- `app/Http` – Controllers, Middleware, Requests vb. HTTP katmanı.
- `app/Models` – Eloquent modelleri (veritabanı tablolarının PHP karşılıkları).
- `app/Services` – İş mantığı/service katmanı. Controller'ların "ince" kalması için ana domain işleri burada yapılır.
- `app/Modules` – Modüler yapı (özelleşmiş domain modülleri). Belirli iş alanları bu klasör altında gruplanmış olabilir.
- `app/Console` – Artisan komutları.
- `app/Jobs` – Queue'ya gönderilen asenkron işler.
- `app/Events`, `app/Listeners` – Event-driven iş akışları.
- `app/Policies` – Yetkilendirme politikaları.
- `app/Rules`, `app/Traits`, `app/Enums`, `app/Support`, `app/Helpers` – Ortak yardımcılar, kurallar, trait'ler vb.

> ChatGPT için not: Bir özelliği anlamak için genelde şu sırayla bakmak mantıklı: **Route → Controller → Service → Model/Events/Jobs**.

### `routes/`

HTTP ve API giriş noktalarının tanımlı olduğu yer:

- `routes/web.php` – Genel web (browser) rotaları.
- `routes/api.php` – Public API rotaları.
- `routes/admin.php`, `routes/api-admin.php`, `routes/admin-ai.php.ARCHIVED` – Admin paneli ve admin API'leri.
- `routes/ai.php`, `routes/ai-advanced.php` – AI/LLM ile ilgili endpoint'ler (ChatGPT entegrasyonunun HTTP giriş noktaları büyük ihtimalle burada).
- `routes/location.php` – Lokasyon/harita/il-ilçe vb. ile ilgili rotalar.
- `routes/blog-admin.php` – Blog yönetimi ile ilgili admin rotaları.
- `routes/auth.php`, `routes/console.php`, `routes/web-clean.php`, `routes/web/` klasörü – kimlik doğrulama, console komutları ve web rotalarının alt kırılımları.

> ChatGPT için rota arama stratejisi: Bir endpoint path'i varsa önce uygun route dosyasını (`web.php`, `api.php`, `ai.php`, `admin.php` vb.) bul, oradan Controller'a geç.

### `ai/`

- `ai/` – Projenin AI/LLM entegrasyonu ile ilgili kodu ve yapılandırmaları barındırır.
- `ai/prompts/` – Model davranışını yöneten prompt şablonları ve metinleri.

Bu klasör, `routes/ai*.php` dosyalarındaki endpoint'lerin kullandığı **AI servisleri**, yardımcılar veya prompt setlerini içerebilir.

> ChatGPT için ana nokta: Eğer görev AI ile ilgiliyse, **hem `routes/ai*.php` dosyalarına hem de `ai/` + `docs/ai*` klasörlerine bak.**

### `docs/`

Dokümantasyon ve kurallar:

- `docs/index.md` – Genel giriş/özet.
- `docs/analysis`, `docs/usage`, `docs/features`, `docs/roadmaps` – Analizler, kullanım senaryoları, özellik planları ve yol haritaları.
- `docs/modules` – Modüllere özel dokümantasyon.
- `docs/rules`, `docs/technical` – Kod standartları, teknik kurallar.
- `docs/ai`, `docs/ai-training` – AI/LLM eğitim ve kullanımına dair dökümanlar.
- `docs/admin`, `docs/api`, `docs/integrations` – Admin, API ve entegrasyonlarla ilgili detaylar.

> ChatGPT için: Bir modül veya feature'ın arka planını/niyetini anlamak için ilgili `docs/*` alt klasörünü mutlaka kontrol et.

### Diğer önemli klasörler

- `resources/` – Blade view'lar, frontend JS/TS, CSS, komponentler.
- `public/` – Public web root (Laravel'in `public/index.php` giriş dosyası, derlenmiş asset'ler vb.).
- `database/` – Migrations, seed'ler ve veritabanı ile ilgili betikler.
- `db/` – Ek SQL dosyaları, yedekler veya yardımcı scriptler.
- `storage/` – Loglar, cache, uploaded dosyalar.
- `tests/` – Feature ve Unit testleri.
- `src/` – PHP/TS tarafında bağımsız kütüphane/utility kodları (varsa).
- `mcp-servers/` – Model Context Protocol (MCP) sunucuları için kod/konfigürasyon.
- `yalihan-bekci/`, `.yalihan-bekci` – Özel guard/denetim araçları ya da projeye özgü CLI/otomasyonlar.

## 3. Tipik HTTP İstek Akışı

1. Kullanıcı tarayıcıdan veya frontend'ten bir URL'ye istek atar.
2. İstek **web sunucusundan** (Nginx/Apache) `public/index.php` dosyasına yönlenir.
3. `bootstrap/app.php` Laravel uygulamasını boot eder, kernel (`app/Http/Kernel.php`) devreye girer.
4. İstek ilgili route dosyasında (`routes/web.php`, `routes/api.php`, `routes/ai.php` vb.) tanımlı bir route ile eşleşir.
5. Route, bir **Controller@method**'a gider.
6. Controller:
   - Gerekirse Request sınıfları ile **validation** yapar.
   - İş mantığını **Service** ve/veya **Model** sınıflarına delege eder.
   - Event dispatch edebilir veya Job dispatch ederek kuyruğa iş atabilir.
7. Sonuç, JSON response veya view/redirect olarak döner.

### 3.1 AI Endpoint Akış Özeti

Bu projede AI ile ilgili temel HTTP endpoint'leri `routes/ai.php` içinde tanımlıdır. Özet akış:

- `POST /ai/chat` → `AIController@chat`
  - Kullanım: Genel AI chatbot
  - Oran sınırlama: `throttle:30,1` (dakikada 30 istek)
- `POST /ai/predict-price` → `AIController@predictPrice`
  - Kullanım: İlan fiyat tahmini
  - Oran sınırlama: `throttle:10,1`
- `POST /ai/generate-description` → `AIController@generateDescription`
  - Kullanım: İlan açıklaması oluşturma
  - Oran sınırlama: `throttle:20,1`
- `POST /ai/analyze-request` → `AIController@analyzeRequest`
  - Kullanım: Talep analizi ve uygun ilan eşleştirme
  - Oran sınırlama: `throttle:15,1`
- `GET /ai/status`
  - Kullanım: AI sağlayıcı durumu (admin)
  - Middleware: `auth:sanctum`, `role:admin`
- `GET /ai/stats`
  - Kullanım: AI kullanım istatistikleri için gelecekte kullanılacak admin endpoint (şu an placeholder)
  - Middleware: `auth:sanctum`, `role:admin`

Bu endpoint'ler genellikle aşağıdaki sırayı izler: **Route → `AIController` → `AIService` / ilgili servisler → Prompt/Model çağrısı**.

## 4. AI / ChatGPT Entegrasyonunun Yeri

ChatGPT veya diğer LLM tabanlı özellikler için odaklanılması gereken yerler:

- **Routes**: `routes/ai.php`, `routes/ai-advanced.php`, `routes/admin-ai.php.ARCHIVED` – AI ile ilgili HTTP giriş noktaları.
- **AI kodu**: `ai/` klasörü (özellikle `ai/prompts/`) – prompt tanımları, AI servis katmanı, model çağrıları vb.
- **Dokümantasyon**:
  - Kök dizindeki `MASTER_PROMPT_YALIHAN_EMLAK_AI.md`
  - `docs/ai`, `docs/ai-training` altındaki dosyalar

### 4.1 AI Provider ve Konfigürasyon

- Varsayılan sağlayıcı: `config('ai.default_provider')` ile belirlenir.
- Desteklenen sağlayıcılar (örnek):
  - `openai` → `config('ai.openai.api_key')`
  - `claude` → `config('ai.claude.api_key')`
  - `google` → `config('ai.google.api_key')`
  - `deepseek` → `config('ai.deepseek.api_key')`
- Sağlayıcıların mevcut olup olmadığı `/ai/status` endpoint'i üzerinden kontrol edilir.
  - Response içinde `available_providers`, `cache_enabled`, `timestamp` alanları bulunur.

> ChatGPT için not: Bir model hatası veya sağlayıcı problemi analiz edilecekse önce `/ai/status` koduna ve `config/ai.php` içeriklerine bak.

### 4.2 AI Eğitim ve Kılavuz Dokümanları Haritası

- Başlangıç ve özet:
  - `docs/ai-training/00-BASLA-BURADAN.md`
  - `docs/ai-training/AI-TRAINING-SUMMARY.md`
- Kullanım senaryoları ve örnekler:
  - `docs/ai-training/05-USE-CASES-AND-SCENARIOS.md`
  - `docs/ai/AI_KULLANIM_ORNEKLERI.md`
- Prompt ve copiloting rehberi:
  - `docs/ai/COPILOT_PROMPTS_GUIDE.md`
  - `docs/ai/prompts/` altındaki dosyalar
- Embedding ve entegrasyon:
  - `docs/ai-training/BASARILI-EMBEDDING-ORNEGI.md`
  - `docs/ai-training/09-OLLAMA-INTEGRATION.md`
- Operasyon ve deployment:
  - `docs/ai-training/DEPLOYMENT-GE                                                                 

Yeni bir backend özelliği eklerken tipik akış:

1. Uygun route dosyasında (`routes/web.php`, `routes/api.php`, `routes/ai.php` vb.) yeni rotayı tanımla.
2. Rotayı yeni veya mevcut bir **Controller** metoduna yönlendir.
3. Controller içinde iş mantığını mümkün olduğunca **Service** katmanına taşı.
4. Gerekirse yeni **Model**, migration veya **Module** oluştur.
5. İlgili testleri `tests/` altında yaz.
6. Frontend etkileniyorsa, `resources/` altındaki view veya JS/TS kodunu güncelle ve Vite/Tailwind pipeline'ını kullan.

## 6. Bu Dokümanı ChatGPT Nasıl Kullanmalı?

- Bir sınıf/metot ararken önce hangi **katmanda** olması gerektiğini düşün (route, controller, service, model, view) ve yukarıdaki dizin haritasını kullan.
- İş kurallarını anlamak için yalnızca koda değil, mutlaka `docs/` altındaki ilgili analiz ve kural dokümanlarına da bak.
- AI ile ilgili senaryolarda, `ai/` ve `routes/ai*.php` dosyalarını **birlikte** değerlendir; ayrıca `MASTER_PROMPT_YALIHAN_EMLAK_AI.md` ve `docs/ai*` dosyalarını referans al.

Bu dosya, `aiegitim/` klasörü altına ChatGPT/LLM eğitim ve rehberliği için yerleştirilmiştir. Gerekli oldukça proje genişledikçe güncellenmelidir.