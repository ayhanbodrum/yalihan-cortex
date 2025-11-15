## Amaç
- http://127.0.0.1:8000/telescope/requests üzerinde görülen hataların kaynağını tespit etmek, nedenlerini açıklamak ve Context7/Yalıhan Bekçi kurallarına uygun çözüm planını uygulamak.

## Ön Bulgular
- PHP 8.4.7 çalışıyor; Sanctum 3.2 kullanılıyor (`composer.json:24`). PHP 8.4 ile `HasApiTokens::createToken()` için nullable parametre uyarısı veriyor (deprecation). Bu uyarılar Telescope’ta görünüyor.
- Route isim standardizasyonu sonrası bazı view’larda eski isimler hâlâ kullanılıyor ve Telescope’ta 404/exception olarak görünmesi muhtemel:
  - `resources/views/admin/layouts/sidebar.blade.php:49–55, 100–105` → `admin.property-type-manager.*` kullanımı
  - `resources/views/admin/components/ai-monitor-widget.blade.php:101` → `admin.property-type-manager.index`
  - `resources/views/components/context7/sidebar.blade.php` ve PTM sayfalarında kontrol edilmesi gereken linkler mevcut
- Telescope konfigürasyonu doğru (`config/telescope.php`); erişim `App\Providers\TelescopeServiceProvider` ile yetkilendiriliyor.

## Plan
### Faz 1: Triage ve Doğrulama (Read-only)
1. Telescope’ta hata türlerini sınıflandır: Exception, Failed Request, Logs, Deprecations.
2. Route listesi doğrulaması: `admin.property_types.*` tüm alt isimler aktif mi (show, field_dependencies, index)?
3. Etkilenen view’ları tarayıp eski route kullanımını envanterle çıkar.

### Faz 2: Route İsim Standardizasyonu Tamamlama
1. Eski `admin.property-type-manager.*` referanslarını `admin.property_types.*` ile değiştir:
   - `resources/views/admin/layouts/sidebar.blade.php:49–55, 100–105`
   - `resources/views/admin/components/ai-monitor-widget.blade.php:101`
   - `resources/views/components/context7/sidebar.blade.php` (varsa PTM linkleri)
2. Blade’lerde `Route::has(...)` kontrolünü koru; menü active state’leri `request()->routeIs('admin.property_types.*')` ile güncelle.

### Faz 3: Sanctum Deprecation Çözümü
1. Çözüm A (tercih): Sanctum’ı PHP 8.4 uyumlu son sürüme güncelle (minor/patch). Composer ile `laravel/sanctum` sürüm aralığını yükselt.
2. Alternatif: Geçici olarak PHP 8.3.x’e pinlemek (geliştirme ortamı) veya uyarıyı bastırmak yerine kaynak uyumluluğunu sağlamak.
3. Doğrulama: Telescope’ta deprecation kayıtları kaybolmalı.

### Faz 4: AI Öneri Yüzeyleri ve MCP Bağlamı
1. Dashboard “AI Önerileri” panelini veriyle besle (AIPromptManager + AnalyticsService). Öneriler: fiyat optimizasyonu, metin üretimi, kategori/özellik tutarlılığı.
2. PTM Field Dependencies sayfasına “Eksik/Çakışan İlişkiler” AI öneri kartları ekle.
3. Talep–İlan eşleştirme için hibrit arama + semantik puanlı öneri listesi.
4. MCP entegrasyonu ile bağlam kaynaklarını ekle (docs’dan RAG):
   - `docs/active/CONTEXT7-MASTER-GUIDE.md`
   - `docs/active/CONTEXT7-RULES-DETAILED.md`
   - `docs/technical/context7-mcp-integration.md`
   - `docs/ai/AI_PROMPTS_CONTEXT7_REVIEW.md`

### Faz 5: Test ve Doğrulama
1. Telescope’ta Requests/Exceptions/Logs sekmelerinde tekrar kontrol.
2. Admin menü ve PTM sayfalarında gezinme smoke testleri.
3. Changelog kayıtları: yapılan her işlem LoggingService ile işlenir.

### Kabul Kriterleri
- Telescope’ta deprecation ve route kaynaklı hatalar giderilmiş.
- Menüler ve PTM linkleri yeni isimlerle sorunsuz çalışıyor.
- AI öneri panelleri veri üretiyor ve uygulanabilir aksiyonlar veriyor.

### Notlar ve Güvenlik
- CSP/HSTS korunur, .env ve key’ler commitlenmez.
- Blade strict ve PSR-12 uyum sürdürülür.

Onay verirseniz Faz 2’den başlayarak uygulamaya geçer, ardından Faz 3 ve Faz 4’ü tamamlayıp Telescope üzerinden doğrularım.