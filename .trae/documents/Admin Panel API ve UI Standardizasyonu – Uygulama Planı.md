## Aşama 1: API Arama Uçları
- `/api/kisiler/search` ve `/api/admin/users/search` uçlarını `routes/api-admin.php` altında tanımla
- Controller’lar: `PeopleController@search`, `AdminUserController@search` — `q` parametresiyle isim/e‑posta/telefon eşleşmesi, JSON çıktı (`[{id,name,email,phone}]`)
- Güvenlik: `auth`/`throttle` middleware, input validasyonu (min 2 karakter), hata durumlarında 4xx/5xx JSON

## Aşama 2: jQuery Geçiş Temizliği
- Proje genelinde jQuery kullanımlarını tarayıp native çözümlerle değiştir (fetch, classList, event)
- Asset pipeline’dan jQuery importlarını kaldır, bundle ve konsol uyarılarını yeniden kontrol et

## Aşama 3: Leaflet.draw Uyumlandırması
- NPM paketini yerel import ile yükle; init sırasını Leaflet sonrasına sabitle
- Toolbar özelleştirmesi ve CSP uyumu koru; `L.Control.Draw` mevcutluğunu garanti et

## Aşama 4: Validasyon UI Konsolidasyonu
- Ortak Blade hata bileşeni: `role="alert"`, `aria-live`, `aria-invalid`, `aria-describedby`
- Create/Edit tüm formlarda aynı örüntüyü uygula; gizli/disabled `required` için submit guard’ı projeye yay

## Aşama 5: Alan Bağımlılıkları Standardizasyonu
- `FieldDependenciesManager.getFeatureMap()` submit’te gizli input yazımı için ortak yardımcı ekle
- “Yayın tipi seçilmedi” uyarısını sadece bağlama göre göster; servis katmanı filtreleriyle tutarlılığı doğrula

## Aşama 6: Menü ve Live Search A11y Yayılımı
- Üst menü dropdown’larda `role="menu"/"menuitem"`, `aria-haspopup/expanded`, `Escape` kapanışı tüm sayfalara yay
- Live search boş sonuç/ağ hatası için `aria-live` mesajları; zengin kart şablonu tüm arama alanlarında

## Aşama 7: Test ve Performans
- Feature testleri: arama uçları, kategori/özellik CRUD, bağımlı alan görünürlüğü
- Responsive snapshot ve temel metrikler (Navigation/Resource Timing)
- Debounce (200–300ms) ve `motion-reduce` kontrolleriyle reflow uyarılarının azaltılması

## Teslimatlar ve Uyum
- Konsolide validasyon, jQuery’siz canlı arama, uyumlu Leaflet.draw toolbar
- Standart field-dependencies submit örüntüsü ve genişletilmiş testler
- Context7/Yalıhan Bekçi uyum raporu ve changelog girişleri; CSS çakışma denetimleri

Onayınızla Aşama 1’den başlamak üzere adım adım uygulayıp her aşama sonunda doğrulama raporu paylaşacağım.