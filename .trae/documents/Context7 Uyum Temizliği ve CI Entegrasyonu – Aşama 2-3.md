## Hedefler

* Kalan Context7/Bekçi ihlallerini azaltmak (özellikle CSS/Blade ve controller validasyonları).

* Tüm UI bileşenlerinde Tailwind + zorunlu transition/dark mode kullanımı.

* Route adlandırmalarını `admin.*` standardına hizalamak.

* CI’de otomatik Context7 denetimi (Markdown/JSON rapor) ve bloklama kuralları.

## Kapsam

* Blade dosyaları: `btn-*`, `form-control`, `card-*`, `row/col-*`, Bootstrap progress/alert/grids → Tailwind’e dönüşüm.

* Controller’lar: `order` validasyon anahtarlarının temizlenmesi, `display_order` ve `status` standardı.

* Route kullanımları: `route('crm.*')` geçişlerinin `route('admin.*')` veya `route('admin.crm.*')` ile güncellenmesi.

* Scanner konfigürasyonu: migration rename metinlerinden kaynaklı sahte pozitiflerin filtrelenmesi.

* CI entegrasyonu: GitHub Actions ile JSON rapora dayalı fail koşulları.

## Faz 1: Tarama ve Envanter (Salt Okuma)

* CSS/Blade taraması: `btn-*`, `form-control`, `card-*`, `row/col-*`, `alert-*`, `progress-*` örneklerini listele.

* Route taraması: `route('crm.*')` kullanımlarını tespit et.

* Controller taraması: `['order' => ...]` ve `enabled/is_active/aktif` anahtarlarını tespit et.

* Rapor: Kapsam listesi + dosya/konum bazlı öneri çıktısı.

## Faz 2: Blade/Tailwind Dönüşümü

* Ortak bileşenleri güçlendir: `components/admin/button`, `components/context7/card`, `components/context7/live-search`, `form-builder`.

* Dönüşüm kuralları:

  * `btn-*` → Tailwind buton seti (primary/secondary/danger/ghost) + `transition-all`, `focus:ring`, `dark:`.

  * `form-control` → Tailwind input/select/textarea kalıpları.

  * `card-*`/Bootstrap grid (`row/col-*`) → Tailwind `grid/flex` + `rounded/border/shadow`.

  * `alert-*` → Tailwind uyarı kutuları (`bg-*-50 + border + text` + dark varyantları).

  * `progress/progress-bar` → Tailwind bar (`h-2.5` + `rounded-full` + `bg-blue-600` genişlik inline).

* Dosya bazlı uygulanacak: `resources/views/admin/*` ve `resources/views/components/*` (öncelik: raporda listelenen dosyalar).

## Faz 3: Controller/Validasyon Standardizasyonu

* Tüm validasyonlarda `order` anahtarını kaldır; `display_order` kullan.

* Boolean alanlarda yalnızca `status` kullan; backward mapping kaldır (gereken yerlerde kontrollü geçiş).

* PSR-12 ve Laravel 11 uyumu koru.

## Faz 4: Route Standardizasyonu

* View ve controller içi `route('crm.*')` kullanımlarını `route('admin.crm.*')` veya doğrudan `route('admin.*')` ile güncelle.

* Yan menülerde ve partial’larda alias’ları korurken yeni standardı öncelikle kullan.

## Faz 5: Scanner Konfigürasyonu (Sahte Pozitifleri Azaltma)

* Migration rename dosyaları için kural dışlamaları ekle:

  * Örnek exclude: `database/migrations/*rename_order*` veya satır bazlı `CHANGE \\` pattern’leri.

* PHP scanner’ın `exclude` alanına path/tabanlı filtre ekle; kritik yerine info seviyesine çek.

## Faz 6: CI/CD Entegrasyonu

* `.github/workflows/context7-check.yml`: JSON raporu üret, Critical varsa job fail.

* Pre-commit hook: hızlı tarama (`scripts/context7-full-scan.sh`) ve bloklama.

* Warp workflows: “Context7: Compliance Check” ve “Context7: Auto-fix Violations”.

## Faz 7: Doğrulama ve Raporlama

* Tam taramayı yeniden çalıştır; Markdown (`.context7/daily-check-YYYYMMDD.md`) ve JSON (`.context7/ci-report.json`) raporlarını güncelle.

* İhlal sayısı ve kategori bazında özet paylaş.

## Notlar ve Riskler

* Migration rename metinleri gereği `order` kelimesi geçebilir; scanner exclude ile yönetilecek.

* Büyük Blade dönüşümleri adım adım yapılacak, her adımda tarama ile doğrulanacak.

## Onay

* Onay verirsen yukarıdaki fazları sırayla uygulayıp, her faz sonunda tarama ve rapor güncellemesi ile ilerleyeceğim.

