## Faz 1: Doğrulama (Read-only)
- `/admin/auth-test` çıktısını kontrol et (routes/admin.php:155–162) ve oturum durumunu raporla.
- `http://127.0.0.1:8000/telescope/requests` üzerinde son kayıtları filtrele: 302 yönlendirme, exception ve deprecation.
- Route listesi doğrulaması: `admin.property_types.*` (index/show/field_dependencies) aktif mi.
- `config/session.php` politikalarını gözden geçir: `same_site`, `secure`, `domain`.

## Faz 2: Uyum Düzeltmeleri
- PHP–Sanctum uyumu: Geliştirme ortamında PHP’yi 8.3’e pinle veya orta vadede Laravel 11 + Sanctum 4.x yükseltme planı çıkar (Horizon ve diğer bağımlılıklarla birlikte).
- Telescope gürültüsünü azalt: Deprecation kaynaklarını not et, gereksiz watcher’ları dev ortamda kapatmayı değerlendir (isteğe bağlı).

## Faz 3: AI Öneri Yüzeyleri
- Dashboard AI önerileri: AIPromptManager + AnalyticsService ile fiyat/metin/kategori-özellik önerilerini besle; öneri listesi + “Uygula/Görmezden Gel”.
- PTM Field Dependencies: Eksik/çakışan ilişkiler için AI öneri kartı ekle; `toggle_*` ve `bulk_save` aksiyonlarına bağla.
- Talep–İlan eşleştirme: Hibrit arama + semantik puanlı öneri listesi.
- MCP bağlamı: `docs/active/CONTEXT7-MASTER-GUIDE.md`, `CONTEXT7-RULES-DETAILED.md`, `docs/technical/context7-mcp-integration.md`, `docs/ai/AI_PROMPTS_CONTEXT7_REVIEW.md` referanslı RAG.

## Faz 4: Test
- Login sonrası `/admin/dashboard/index` ve menü gezintisi smoke test.
- Telescope tekrar kontrol: Requests/Exceptions/Logs.
- Changelog kaydı: yapılan işlemleri LoggingService ile işleyip raporla.

## Çıktılar
- Oturum durumu raporu
- Telescope hata analizi ve aksiyon listesi
- AI öneri panelleri aktif ve uygulanabilir aksiyonlarla doğrulanmış