# Copilot talimatlari — EmlakPro (Laravel 10, Context7, AI-First)

Bu depo Laravel 10 monolit + modüler mimari (app/Modules/\*\*), Tailwind CSS + Alpine.js ve Context7 standartlarıyla yönetilir. Amaç: AI ajanlarının hızlı, güvenli ve kurallara uygun katkı yapması.

## Büyük resim (mimari ve veri akışı)

- Katmanlar: Blade/Alpine.js UI → HTTP Controllers → Services → Eloquent Modeller → MySQL. AI ve Context7 entegrasyonları Services ve API katmanında.
- Modüller: `app/Modules/*` içinde domain'e göre ayrılmış (Admin, Auth, Emlak, Talep, ArsaModulu, Analitik, CRMSatis, Finans, TakimYonetimi). Rotalar `routes/*.php` altında (web, api, admin, ai, location).
- Context7 Dual System: 1) Upstash Context7 MCP (library docs), 2) Yalıhan Bekçi Context7 (project rules). "Context7 kullan" her iki sistemi aktive eder.
- CSS Framework: **SADECE Tailwind CSS** - Neo Design System yasak, Bootstrap yasak. Tüm styling utility classes ile.
- Arama/analiz: Elasticsearch isteğe bağlı (Türkçe analyzer'lar). Docker compose dosyası mevcut.

## Günlük geliştirici iş akışları

- MySQL (MAMP) ve Laravel sunucu: VS Code Tasks kullanın
    - "Start MySQL (MAMP)" → "Start Laravel Server" (port 8002)
    - "Laravel: Run Migrations" ve "Laravel: Clear Cache" görevleri hazır.
    - Context7 validation: "Context7: Validate All" ve "Context7: Auto Fix" görevleri.
- Migrasyon/seed/test: Composer scriptleri ve yardımcı script
    - `composer run app:migrate-seed`, test: `vendor/bin/phpunit` veya `composer run app:test`.
    - Migration auto-fix: `./scripts/fix-migrations.sh` (VS Code task mevcut).
- Asset build: Vite (dev: `npm run dev`, prod: `npm run build`). **Sadece Tailwind CSS kullan**.
- MCP Server (AI bellek/entegrasyon): `./scripts/services/start-mcp-server.sh` (port 4000). VS Code "Test MCP Server" görevi.
- Context7 Commands: `php artisan context7:validate-migration --all`, `php artisan context7:validate-migration --auto-fix`.
- Yedek/temizlik: `./backup-database.sh`, `./deep-cleanup.sh`.

## Proje-özel kurallar (Context7 ve UI)

- Alan adları ve ilişkiler:
    - il → il, il_id → il_id; status → status; oncelik → one_cikan; is_published, aktif_mi vb. tutarlılık şarttır.
    - Eloquent: Relationship’ler `with()` ile yüklenir; Accessor’lar yüklenmez. Örn: `Ilan::with(['kategori','ozellikler'])`, fakat `with('formatted_title')` YANLIŞ; `$ilan->formatted_title` DOĞRU. Kaynak: `instructions/ai-model-kurallari.instructions.md`.
- Blade ve JS:
    - **SADECE Tailwind CSS** kullanın; Neo Design System (`neo-*`), Bootstrap (`btn-*`, `card-*`) yasaktır.
    - **Her etkileşimli elemana transition zorunlu**: `transition-all duration-200 ease-in-out hover:scale-105 active:scale-95`.
    - **Dark mode zorunlu**: `bg-white dark:bg-gray-800 text-gray-900 dark:text-white`.
    - Blade'de null coalescing zorunlu: `{{ $var->field ?? '—' }}`.
    - JS: Alpine.js tercih; Vue/React/jQuery eklemeyin (projede React bağımlılığı bulunsa da UI'da kullanılmaz).
- Güvenlik ve hız:
    - API’lerde Sanctum, web AJAX’ta CSRF. AI endpoint’lerinde throttle mevcut; yeni endpoint eklerken rate limit ekleyin.
    - N+1 yok; eager load + uygun indexler (lokasyon için `(il_id, ilce_id, mahalle_id)`).
- Şema ve dokümantasyon:
    - AI ajanları yeni tablo/kolon oluşturmaz. Şema kaynağı: `docs/context7-master.md`. Değişiklik gerekiyorsa migration + doküman güncellemesi İNSAN onayıyla yapılır.

## Entegrasyonlar ve konfigler

- AI sağlayıcıları: `config/ai.php` (default provider/model, OpenAI/DeepSeek/Gemini/Claude/Ollama anahtarları env’den). Gizli anahtarları asla koda gömmeyin.
- Context7: `config/context7.php` ve `config/context7.json` (özellik bayrakları, cache, rate limit). Kurallar/doküman: `docs/context7-*.md`.
- Elasticsearch: `docker-compose.elasticsearch.yml`, `config/elasticsearch.php` (Türkçe analyzer, index mapping `ilanlar`).

## Rota ve katman örüntüleri

- Web (CSRF): `routes/web.php` altında admin/panel görünüm ve bazı AI web endpoint’leri.
- API (Sanctum): `routes/api.php` içinde `context7/*`, `locations/*`, `ai/*` grupları. Yeni API eklerken uygun prefix, middleware ve throttle uygulayın.
- Modüler servisler: İş mantığını Controller yerine Service sınıflarına koyun (örn. `app/Modules/Talep/Services/*`).

## Dosya/klasör rehberi (başlıca)

- Rotalar: `routes/web.php`, `routes/api.php`, `routes/ai.php`, `routes/location.php`
- Modüller: `app/Modules/**` (Admin, Auth, Emlak, Talep, ArsaModulu, Analitik, CRMSatis, Finans, TakimYonetimi)
- AI/Context7 kuralları: `docs/context7-rules.md`, `docs/context7-master.md`, `.warp/rules/context7-compliance.md`
- Yardımcı scriptler: `scripts/services/start-mcp-server.sh`, `scripts/fix-migrations.sh`
- Context7 config: `config/context7.php`, `config/context7.json`
- VS Code tasks: `.vscode/tasks.json` (Context7 validation, migration auto-fix)
- Arama: `docker-compose.elasticsearch.yml`, `config/elasticsearch.php`

## Ne yapmalı / Ne yapmamalı

- Yap: Context7 adlandırma kurallarına uy, throttling ekle, eager load kullan, Tailwind CSS utility classes kullan.
- Yapma: Yeni tablo/kolon yaratma, legacy sınıf/alan adları kullanma, secrets'ı commit etme, `with()` ile accessor yükleme, Bootstrap/Neo Design System kullanma.

## Context7 Dual System Usage

"Context7 kullan" komutu ile **HER İKİ sistem** otomatik aktive olur:

1. **Upstash Context7 MCP** (Library docs): `resolve-library-id`, `get-library-docs`
2. **Yalıhan Bekçi Context7** (Project rules): `get_context7_rules`, `validate`, `check_pattern`

Örnek: Laravel migration oluştururken hem Laravel API docs hem project compliance kuralları uygulanır.
