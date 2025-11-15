## Aşama 1: jQuery Geçişini Tamamla
- Live Search ve diğer küçük yardımcıların native fetch ile çalıştığını doğrula
- Kademeli olarak jQuery bağımlılıklarını kaldır; kullanılmayan importları temizle
- Bundle boyutu ve konsol uyarılarını yeniden kontrol et

## Aşama 2: Leaflet.draw Uyumlandırması
- L.Control.Draw init sırası ve sürüm eşleşmesini doğrula
- Toolbar özelleştirmesini sürüm-uyumlu hale getir; CSP uyumunu koru
- Harita araçları (zoom/locate/draw) için a11y ve kısayolları gözden geçir

## Aşama 3: Validasyon UI Konsolidasyonu
- Tüm Create/Edit formlarda görünür hata bloklarını tek örüntüye taşı
- `aria-invalid`, `aria-describedby`, `role="alert"`, `aria-live` bağlarını standardize et
- Gizli/disabled `required` alanlar için submit guard’ı projeye yay

## Aşama 4: Alan Bağımlılıkları Standardizasyonu
- FieldDependenciesManager `featureMap` submit gizli input yazımını tüm dinamik formlarda bir örüntüye getir
- "Yayın tipi seçilmedi" uyarısını sadece yayın tipine bağlı alanlar gerektiğinde göster
- Servis katmanındaki yayın tipi filtreleriyle UI render tutarlılığını doğrula

## Aşama 5: Menü ve Live Search A11y Genişletmesi
- Üst menü dropdown’larda `role="menu"/"menuitem"`, `aria-haspopup/expanded` ve `Escape` kapanışı projeye yay
- Live search’te boş sonuç/ağ hatası durumlarında `aria-live` mesajları ve zengin kart şablonunu tüm arama alanlarına uygula

## Aşama 6: AI Panel İyileştirmeleri
- Readiness progress bar eşiklerini konfigüre et (konut %70, arsa %80, işyeri %75)
- Uygulanan öneriler için mini diff satırları ve "Geri Al" stack görünümü ekle
- Mikro kopyayı sadeleştir, kritik eksikleri kısa ve net göster

## Aşama 7: Test ve Performans
- CRUD ve dinamik akışlar için Feature testlerini genişlet (kategori—özellik ilişkileri, bağımlı alan görünürlüğü)
- Responsive snapshot testleri ve temel performans ölçümleri (Navigation/Resource Timing) ekle
- Debounce (200–300ms) ve `motion-reduce` denetimleriyle reflow uyarılarını azalt

## Teslimatlar
- Konsolide validasyon bileşenleri ve a11y bağları
- jQuery’siz canlı arama ve Leaflet.draw uyumlu toolbar
- Standart field-dependencies submit örüntüsü
- Genişletilmiş Feature testleri ve raporlanan metrikler

## Uyum ve Kayıt
- Context7 ve Yalıhan Bekçi kurallarına uygunluk kontrol listesi
- Changelog girişleri ve CSS çakışma denetimleri

Bu planı onaylarsanız, Aşama 1’den başlayarak sırasıyla uygulayıp her aşamayı doğrulama raporlarıyla tamamlayacağım.