## Aşama 1: jQuery Geçiş Temizliği
- Envanter: Projede jQuery kullanımını tarayıp listele (özellikle `resources/js/admin/**`, Blade inline scriptler)
- Değişiklikler: jQuery bağımlı parçaları native DOM/fetch ile değiştir; kullanılmayan importları kaldır
- Doğrulama: Konsol uyarıları ve bundle boyutu kontrolü; live search ve pagination/native event’ler sorunsuz

## Aşama 2: Leaflet.draw Uyumu
- Init sırası: Leaflet yüklendikten sonra `L.Control.Draw`’ı garanti edecek init bloğu
- Toolbar: Özelleştirme ve CSP uyumunu sürüm uyumlu hâle getir; dark/light temada kontrast tutarlılığı
- Doğrulama: Harita üzerinde çizim/edit/sil araçları görsel ve fonksiyonel test; konsolda uyarı yok

## Aşama 3: Validasyon UI Konsolidasyonu
- Ortak hata bileşeni: `role="alert"`, `aria-live`, `aria-invalid`, `aria-describedby` kullanımı için Blade partial
- Uygulama: Create/Edit tüm formlarda aynı örüntüye geçiş (kategori/özellik/lokasyon vb.)
- Guard: Gizli/disabled `required` alanlar için submit guard’ı projeye yay
- Doğrulama: Boş/yanlış girişlerde görünür hata; ekran okuyucu ile erişim testi

## Aşama 4: Alan Bağımlılıkları Standardizasyonu
- Submit örüntüsü: `FieldDependenciesManager.getFeatureMap()` ile gizli input yazımı ortak yardımcı fonksiyon
- Uyarılar: “Yayın tipi seçilmedi” yalnızca bağlama göre; servis filtreleri ile render tutarlılığı
- Doğrulama: Farklı kategori/yayın tipi kombinasyonlarında required/recommended blokları doğru ve eksiksiz

## Aşama 5: Menü ve Live Search A11y Yayılımı
- Menü: `role="menu"/"menuitem"`, `aria-haspopup/expanded`, `Escape` kapanışı tüm admin dropdown’larına
- Live search: Boş/ağ hatası için `aria-live` mesajları ve zengin kart şablonu; tüm arama alanlarında
- Doğrulama: Klavye navigasyonu ve ekran okuyucu testi; sonuç seçimi gizli id’yi dolduruyor

## Aşama 6: Test ve Performans
- Feature testleri: CRUD ve dinamik akış senaryoları; arama uçları ve submit örüntüsü
- Responsive snapshot ve Navigation/Resource Timing ölçümleri
- Debounce (200–300ms) ve `prefers-reduced-motion` denetimleri ile reflow uyarılarının azaltılması

## Uyum ve Kayıt (Context7 & Yalıhan Bekçi)
- Erişilebilirlik, güvenlik (CSP/CSRF/log maskeleme) ve modüler servis kurallarına uygunluk
- Changelog girişleri ve CSS çakışma denetimleri; jQuery kaldırma raporu

Onayınızla bu adımları sırayla uygulayıp her aşama sonunda doğrulama raporlarını paylaşacağım.