# Copilot talimatlari — EmlakPro (Laravel 10, Context7, AI-First)

Bu depo Laravel 10 monolit + modüler mimari (app/Modules/\*\*), Neo Design System (Tailwind + Alpine.js) ve Context7 standartlarıyla yönetilir. Amaç: AI ajanlarının hızlı, güvenli ve kurallara uygun katkı yapması.

## Büyük resim (mimari ve veri akışı)

- Katmanlar: Blade/Alpine.js UI → HTTP Controllers → Services → Eloquent Modeller → MySQL. AI ve Context7 entegrasyonları Services ve API katmanında.
- Modüller: `app/Modules/*` içinde domain’e göre ayrılmış (örn. `Talep`, `Auth`, `Emlak`). Rotalar `routes/*.php` altında (web, api, admin, ai, location).
- Context7: Kod ↔ DB ↔ Dokümantasyon tutarlılığını zorunlu kılar. Drift tespiti ve kural senkronu GitHub Actions ile otomatik (.github/workflows/context7-\*.yml).
- Arama/analiz: Elasticsearch isteğe bağlı (Türkçe analyzer’lar). Docker compose dosyası mevcut.

## Günlük geliştirici iş akışları

- MySQL (MAMP) ve Laravel sunucu: VS Code Tasks kullanın
    - “Start MySQL (MAMP)” → “Start Laravel Server” (veya `./laravel-tasks.sh serve` → 8002 portu)
    - “Laravel: Run Migrations” ve “Laravel: Clear Cache” görevleri hazır.
- Migrasyon/seed/test: Composer scriptleri ve yardımcı script
    - `composer run app:migrate-seed`, test: `vendor/bin/phpunit` veya `composer run app:test`.
- Asset build: Vite (dev: `npm run dev`, prod: `npm run build`).
- MCP Server (AI bellek/entegrasyon): `./start-mcp-server.sh` (port 4000). Sağlık testi için “Test MCP Server” görevi.
- Yedek/temizlik: `./backup-database.sh`, `./deep-cleanup.sh`.

## Proje-özel kurallar (Context7 ve UI)

- Alan adları ve ilişkiler:
    - il → il, il_id → il_id; status → status; oncelik → one_cikan; is_published, aktif_mi vb. tutarlılık şarttır.
    - Eloquent: Relationship’ler `with()` ile yüklenir; Accessor’lar yüklenmez. Örn: `Ilan::with(['kategori','ozellikler'])`, fakat `with('formatted_title')` YANLIŞ; `$ilan->formatted_title` DOĞRU. Kaynak: `instructions/ai-model-kurallari.instructions.md`.
- Blade ve JS:
    - Neo Design System sınıfları (`neo-*`) kullanın; legacy `btn-*`, `card-*` yasaktır.
    - Blade’de null coalescing zorunlu: `{{ $var->field ?? '—' }}`.
    - JS: Alpine.js tercih; Vue/React/jQuery eklemeyin (projede React bağımlılığı bulunsa da UI’da kullanılmaz).
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
- Modüller: `app/Modules/**`
- AI/Context7 kuralları: `docs/context7-rules.md`, `docs/context7-master.md`, `instructions/ai-model-kurallari.instructions.md`
- Yardımcı scriptler: `laravel-tasks.sh`, `start-mcp-server.sh`
- Arama: `docker-compose.elasticsearch.yml`, `config/elasticsearch.php`

## Ne yapmalı / Ne yapmamalı

- Yap: Context7 adlandırma kurallarına uy, throttling ekle, eager load kullan, Neo sınıflarıyla UI güncelle.
- Yapma: Yeni tablo/kolon yaratma, legacy sınıf/alan adları kullanma, secrets’ı commit etme, `with()` ile accessor yükleme.

Güncellemenin eksik/yanlış olabileceği yer var mı? Özellikle “Modüller” altında çalıştığınız alanlar ve özel komutlar için not eklememi ister misiniz?
