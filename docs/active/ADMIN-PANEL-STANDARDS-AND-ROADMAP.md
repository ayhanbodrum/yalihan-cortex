# Admin Panel İyileştirme ve Doğrulama Yol Haritası

## Sürüm: v2025.11.0 (Yalıhan Bekçi)

- jQuery tarama ve bloklama entegre edildi (pre-commit ve CI).
- List Paginate servisi Vanilla JS ile modernize edildi (fetch + AbortController + debounce + hafif DOM).
- Erişilebilirlik: `role="navigation"`, `aria-current="page"`, `role="status"`, `aria-live="polite"` eklendi.
- Per-page seçici uyumu: `select[data-per-page-select]` ve `select[data-per-page]` birlikte destekleniyor.

## Kaldırılanlar ve Silme Politikası

- Kaldırılanlar (Kod modernizasyonu):
  - List Paginate jQuery bağımlılıkları kaldırıldı; Vanilla JS task yapısına geçirildi.
  - `public/js/admin/csrf-handler.js` jQuery bağımlılığı kaldırıldı; vanilla JS ile global CSRF header ve form token ekleme sağlandı.
  - `public/js/address-select.js` jQuery API’leri kaldırıldı; native DOM ve isteğe bağlı Select2 tetikleme ile uyumlu.
  - `public/js/admin/location-helper.js` jQuery seçici ve tetiklemeleri kaldırıldı; Select2 inicializasyonu DOM üzerinden yapılır.
  - `public/js/admin/location-map-helper.js` jQuery tetiklemeleri kaldırıldı; Select2 opsiyonel tetikleme ve native olaylar kullanılır.
  - `public/js/modules/ilan-form.js` jQuery tetiklemeleri kaldırıldı; tetiklemeler `triggerChanged` ile yönetilir.
  - `resources/views/vendor/admin-theme/layouts/app.blade.php` içindeki `$.ajaxSetup` kaldırıldı; global CSRF handler script eklendi.
  - `resources/views/admin/layouts/admin.blade.php` içindeki jQuery CDN kaldırıldı; global CSRF handler script eklendi.

- Planlanan Silmeler (refaktör tamamlandıktan sonra):
  - `public/js/admin/location-helper.js` → `select2` bağımlılığı; native çözüme geçildikten sonra silinecek.
  - `public/js/admin/location-map-helper.js` → `select2` bağımlılığı; native çözüme geçildikten sonra silinecek.
  - `public/js/modules/ilan-form.js` → `select2` bağımlılığı; native çözüme geçildikten sonra silinecek.
  - `resources/views/admin/layouts/admin.blade.php` ve ilişkili layoutlarda jQuery/`$.ajax` parçaları kaldırılacak (dosya silinmez, içerik modernize edilir).

Not: Silme işlemleri, ilgili ekranların vanilla JS muadilleri devreye alındıktan sonra uygulanır; pre-commit ve CI taramaları yeni jQuery eklemelerini engeller.

## jQuery_Legacy Kullanım Tespiti ve CI Entegrasyon Planı

- Tarama komutu: `npm run scan:jquery` (staged dosyaları tarar).
- CI iş akışı: `.github/workflows/jquery-scan.yml` push/PR’de tüm `resources/**` kapsamını tarar.
- Pre-commit engelleyici: `.husky/pre-commit` jQuery tespit edilirse commit’i durdurur.
- Kapsam: `resources/js`, `resources/views` ve blade dosyaları; `vendor`, `public`, `storage` hariç tutulur.

## jQuery Temizliği ve List Paginate Servisi Modernizasyon Planı

- Amaç: jQuery bağımlılıklarını kaldırmak, native API’lere geçmek, reflow ve bundle optimizasyonu.
- List Paginate modernizasyonu:
  - `fetch` + `AbortController` ile ağ çağrıları kontrolü, iptal mekanizması.
  - 250 ms debounce ile arama ve per-page değişimleri.
  - `DocumentFragment` ile minimal reflow ve hızlı render.
  - Otomatik başlatma: `[data-meta][data-list-id][data-list-endpoint]` desteği ve grid/tablo seçim mantığı.

## Sıradaki Adımlar – jQuery Temizliği, Leaflet.draw Uyumu, Validasyon Konsolidasyonu

- jQuery temizliği öncelikli dosyalar:
  - `resources/views/**` içinde jQuery/`$.ajax` kullanılan layout ve sayfalar.
  - `public/js/**` altında select2 ve ready blokları; Vanilla JS muadilleri ile değişim.
  - `public/js/admin/csrf-handler.js` → token ekleme ve form submit işlemleri native olarak yazılacak.
- Leaflet.draw uyumu:
  - Harita bileşenlerinde native event yönetimi ve performans optimizasyonu.
  - Toolbar A11Y: role/aria-label ve klavye erişimi (Enter/Space) eklendi.
- Validasyon konsolidasyonu:
  - Tutarlı hata/validasyon bileşenleri (`aria-invalid`, `aria-describedby`) ve blok bazlı UI.
  - FormValidator: hata mesajları `role="status" aria-live="polite"`; odak ilk hataya taşınır.
  - Backend ile mesaj sözleşmesi uyumu; tek tip component.

## Admin Panel API ve UI Standardizasyonu – Uygulama Planı

- Modüler yapı: her modül kendi migration/seeder’larını içerir; sürümler `YYYY_MM_DD_HHMMSS` formatında.
- Servis ve Event tabanlı iletişim; bağımlılıklar `ServiceProvider`’larda.
- Güvenlik: `.env` commitlenmez; API key’ler config dosyalarında tutulmaz; hassas veriler encrypt edilir.
- Erişilebilirlik: navigasyon ve durum rolleri; klavye ve fokus yönetimi zorunlu.
- UI: Admin Panel yan menüler `AdminMenu` trait ile; CSS çakışmalarının otomatik kontrolleri ve changelog zorunluluğu.

## Doğrulama ve Ölçümler

- Pre-commit: lint-staged + Context7 + jQuery taraması; başarısızlıkta commit durur.
- CI: jQuery taraması; ilerleme raporları PR üzerinde görünür.
- Performans: reflow uyarıları azaltımı; istek iptali ve debounce etkisi gözlemlenir.
 - Erişilebilirlik: Leaflet.draw toolbar erişilebilirlik denetimleri eklendi ve doğrulandı.

## Ek Notlar

- Uyumluluk: `window.ListPaginate.init(...)` korunmuştur; yeni `window.ListPaginate.create()` ile task yaklaşımı desteklenir.
- Meta sözleşmesi: `current_page`, `per_page`, `total`, `last_page` alanları backend yanıtıyla uyumlu olmalıdır.

## AI Entegrasyon Standartları ve Yol Haritası

- API uçları (admin):
  - `POST /api/admin/ai/chat` (metin tabanlı sohbet)
  - `POST /api/admin/ai/price/predict` (fiyat tahmini)
  - `POST /api/admin/ai/suggest-features` (özellik önerileri)
  - `GET /api/admin/ai/analytics` (genel metrikler)
- Servis sözleşmesi: yanıtlar `ResponseService::success` ile standartlaştırılır; meta olarak `provider`, `response_time`, `timestamp` döndürülür.
- Frontend kayıt: `resources/js/admin/ai-register.js` içinde provider `backend` olarak kayıtlıdır; `AIOrchestrator` ile çağrılar yönetilir.

## Ölçüm Özetleri ve Raporlama

- List Paginate ölçümleri: `render/paginate/total` süreleri toplanır ve özetler `yalihan-bekci/reports/YYYY-MM/` altında yazdırılır.
- AI çağrıları: yanıt süresi ortalaması, başarı/hatap oranları normalize edilir; `GET /api/admin/ai/analytics` uçundan okunur.