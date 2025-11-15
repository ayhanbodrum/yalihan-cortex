## Kapsam ve Hedefler
- jQuery bağımlılığını minimuma indir, vanilla/Vue Composition API örüntülerine taşı.
- Leaflet.draw yükleme, toolbar ve a11y uyumunu garanti et; yanlış uyarıları kaldır.
- Create/Edit formlarında validasyon UI’ını tek örüntüye konsolide et; FormRequest mesajlarıyla birebir senkronize et.
- Üst menü dropdown a11y kurallarını tüm admin sayfalarına yay.
- CRUD/dinamik akış Feature testleri, responsive snapshot ve debounce/motion‑reduce denetimlerini CI’a bağla.

## Global İlkeler
- PSR‑12, Blade strict ve Vue Composition API kullan.
- Modüler yapı: değişiklikler ilgili modül dizinlerine; changelog her adımda güncellenir.
- Güvenlik: CSRF, CSP, PII maskesi; `.env` ve key yönetimi kurallarına uy.
- CSS çakışmaları: scoped stiller/BEM ve lint ile izle.

## Aşama 1: jQuery Geçiş Temizliği
- Envanter hedefi: `$.ajax`, `$(document).ready`, `$(...).on`, `$('...').animate` kullanımlarını kaldır; Select2/Bootstrap jQuery eklentileri geçici olarak korunur.
- Dönüşüm ilkeleri:
  - İstekler: `$.ajax` → `fetch` + CSRF header (`meta[name="csrf-token"]`).
  - Olaylar: `$(document).on('change', ...)` → `addEventListener` ve event delegation.
  - Hazır olma: `$(document).ready(...)` → `DOMContentLoaded`.
  - Animasyon: `$('html, body').animate` → `scrollIntoView` ve motion‑reduce kontrolü.
- Dosya odakları: `public/js/admin/location-helper.js`, `public/js/admin/location-map-helper.js`, `public/js/modules/ilan-form.js`, `public/js/address-select.js`, `resources/views/admin/*/*.blade.php` içinde inline jQuery.
- Kabul kriterleri:
  - Admin/Frontend sayfalarında Select2/Bootstrap dışı jQuery çağrıları kaldırılır; fonksiyonel regresyon yok.
  - CSRF, hata ve loading durumları fetch akışında korunur.
  - Changelog ve CSS çakışma kontrol raporu güncellenir.

## Aşama 2: Leaflet.draw Uyum Testi
- Yükleme sırası: Leaflet CSS/JS → Leaflet.draw CSS/JS (loader) → Map init → Draw init.
- Loader denetimi: `public/js/leaflet-draw-loader.js` onload ve CSP sprite yolları; yanlış uyarı kaldırılmış durumda teyit.
- Toolbar a11y: `aria-label`, `tabindex` ve `:focus-visible`; custom kontrol örneği `public/js/leaflet-integration.js:315`.
- Init doğrulama: `public/test-harita-tools.html:166,168` ve `public/js/advanced-leaflet-integration.js` yükleme/toolbar akışı.
- Kabul kriterleri:
  - `L.Control.Draw` tutarlı şekilde mevcut; CSP uyarısı yok.
  - Toolbar tuşları klavye erişilebilir; ekran okuyucu etiketleri doğru.
  - Changelog ve uyumluluk notları güncel.

## Aşama 3: Validasyon UI Konsolidasyonu
- Servisleşme: Sayfa içi `ValidationManager` tanımlarını tek servis haline getir (`resources/js/admin/services/ValidationManager.js`).
- Blade bileşen standardı: `components/admin/{input,select,textarea}.blade.php` ve `components/neo-*` için `@error`, `aria-invalid`, `aria-describedby` ve tek hata render bileşeni.
- FormRequest senkronu: `app/Http/Requests/Admin/*Request.php` kurallar/mesajlar ile alan adları birebir eşleştirilir.
- i18n hazırlığı: Türkçe sabit mesajlar `__('validation.key')` anahtarlarına taşınmaya hazır şablon.
- Kabul kriterleri:
  - Create/Edit tüm formlar aynı validasyon deneyimini sunar.
  - ARIA ve hata renderı tek örüntüde; FormRequest mesajları ekranda doğru lokalize edilir.
  - Changelog’da konsolidasyon referansları ve etkilenmiş formlar listelenir.

## Aşama 4: Üst Menü Dropdown A11y Yayılımı
- Bileşenler: `resources/views/components/admin/dropdown.blade.php`, `resources/views/components/neo/dropdown*.blade.php`.
- Kurallar: `role="menu"`, öğelerde `role="menuitem"`, `aria-expanded`, `aria-controls`, `aria-labelledby`, Escape ile kapanış, açılışta ilk öğeye odak, kapanışta tetik düğmesine odak iadesi, focus‑trap ve ArrowUp/Down/Home/End/Enter/Space desteği.
- Kabul kriterleri:
  - Tüm admin sayfalarında dropdown’lar klavye ile tam kullanılabilir.
  - A11y denetimi (axe/lighthouse) uyarısız; CSS focus stilleri tutarlı.
  - Changelog ve a11y kontrol raporu güncel.

## Aşama 5: Test ve Performans Genişletmeleri
- Backend Feature: CRUD/dinamik akış senaryoları (`tests/Feature/Admin/*` genişleme), FieldDependencies submit/gizli input doğrulaması.
- JS Unit: Vitest kurulumu; debounce/throttle ve motion‑reduce davranış testleri.
- E2E: Playwright ile viewport matrisi (mobil/tablet/desktop) ve responsive snapshot.
- CI: PHP ve JS test komutları, görsel diff raporu, coverage ≥ %70.
- Kabul kriterleri:
  - Test paketleri yeşil; coverage eşiği karşılanır.
  - Motion‑reduce ve debounce testleri deterministik.
  - Changelog’da yeni test setleri ve CI adımları.

## Risk ve Geri Alma
- jQuery kaldırma riskleri: Select2/Bootstrap entegrasyonları korunur; sorun olursa sayfa bazlı jQuery shim geri yüklenir.
- Leaflet CSP: Sprite yolları yerel barındırma ile garanti; loader kapatma bayrağı ile rollback.
- Validasyon: Servisleşme sonrası form başlatmalarında veri bağları; eski sayfalar için adapter.

## Planlanan Sıralama ve Zamanlama
- Sıra: jQuery temizliği → Leaflet.draw uyumu → validasyon konsolidasyonu → a11y yayılımı → test/performans.
- Zaman: Her aşama bağımsız olarak birleştirilebilir; modül bazlı rollout ve changelog güncellemesiyle ilerlenir.

## Teslimatlar
- jQuery temizliği PR seti ve etki analizi.
- Leaflet.draw a11y/CSP uyum raporu ve referans entegrasyon.
- Validasyon servis/bileşen kütüphanesi ve FormRequest senkronizasyon listesi.
- Dropdown a11y standart bileşenleri.
- Test paketleri (Feature/Unit/Playwright/Vitest) ve CI entegrasyonu.

Onay sonrası sırayla uygulamaya başlayacağım; her aşama sonunda changelog ve kısa doğrulama raporu paylaşacağım.