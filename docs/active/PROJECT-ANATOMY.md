# Proje Anatomisi ve Modül Haritası

Bu belge, Yalıhan Emlak Warp projesindeki ana modülleri, ilgili dosya/katalog yapılarını ve temel sorumlulukları özetler. Yalıhan Bekçi ve Context7 kontrolleri için referans olarak kullanılır.

## 1. Mimari Genel Bakış
- **Framework:** Laravel 10 + Blade (Tailwind + Alpine.js) frontend.
- **Veri Katmanı:** MySQL. Modeller `app/Models` altında (ilan, talep, kişi, yazlık, rezervasyon vb.).
- **Servis Katmanı:** Context7 standartlarına göre `app/Services` altında gruplanmış (PropertyFeedService, AIService, Location servisleri vb.).
- **Arayüz:** Yönetim paneli `resources/views/admin`, frontend `resources/views/frontend`, bileşenler `resources/views/components`.
- **Otomasyon:** Yalıhan Bekçi raporları `yalihan-bekci/`, Context7 otorite dosyaları `.context7/`.

## 2. Ana Modüller
| Modül | Ana Controller/Servisler | Görünümler | Sorumluluk |
| --- | --- | --- | --- |
| **İlan Yönetimi** | `Admin/IlanController`, `PropertyFeedService`, `IlanService` | `admin/ilanlar/*`, `frontend/ilanlar/*`, `components/yaliihan/property-card.blade.php` | İlan CRUD, yayınlama, frontend kart ve modallar |
| **Talep Yönetimi** | `Admin/TalepController`, `TalepAnalizController`, `TalepPortfolyoController` | `admin/talepler/*`, `admin/talep-portfolyo/*` | Taleplerin kaydı, analiz ve eşleştirmeler |
| **Danışman Yönetimi** | `Admin/DanismanController`, `UserController`, `DanismanStatsService` | `admin/danisman/*`, `frontend/danismanlar/*` | Danışman CRUD, statüler, sanal danışman kartı |
| **Yazlık Kiralama** | `Admin/YazlikKiralamaController`, `YazlikFiyatlandirma`, `YazlikRezervasyon` servisleri | `admin/ilanlar` yazlık bölümleri, `components/yaliihan/type-fields.blade.php` | Sezonluk fiyat ve rezervasyon yönetimi |
| **Harita & Lokasyon** | `Api/LocationController`, `Location/NearbyService`, `Context7LocationService` | `components/yaliihan/map-component.blade.php`, harita modalları | Google Maps/Türkiye API entegrasyonu, lokasyon seçimleri |
| **AI & Sanal Danışman** | `Api/AIController`, `AIService`, `Context7LiveSearch` | `admin/components/ai-widget.blade.php`, `frontend/danismanlar`, `/ai/explore` | AI tabanlı analiz, içerik üretimi, sanal danışman |
| **Raporlama & Analytics** | `Admin/AnalyticsController`, `DashboardController`, `reports/` | `admin/analytics/*`, `admin/dashboard/*` | Panel metrikleri, arama performansı, Bekçi tetikleyicileri |

## 3. Servis ve Yardımcı Katmanı
- `app/Services/Frontend/PropertyFeedService.php`: Frontend ilan kartları ve `/api/frontend/properties` uçları için merkezi veri kaynağı.
- `app/Services/AIService.php`: OpenAI, Gemini, Claude, DeepSeek, Ollama sağlayıcılarını yöneten ana servis.
- `app/Services/Location/` ve `app/Services/Context7/`: Lokasyon izolasyonu, Context7 kurallarını uygulayan yardımcılar.
- `scripts/`: Kod kalitesi/Context7 denetimi (`comprehensive-code-check.php`, `find-incomplete-code.php`).

## 4. Frontend Yapısı
- **Layoutlar:** `layouts/frontend.blade.php`, `admin/layouts/sidebar.blade.php` (Tailwind + dark mode).
- **Bileşenler:** `components/yaliihan/*` (hero, property-card, map, contact, modal vs.).
- **Modallar:** `yaliihan-home-clean.blade.php` içindeki `virtualTour`, `gallery`, `map`, `propertyDetail` modalları global JS ile yönetilir.
- **JS Standartları:** Vanilla JS + Alpine. `public/js/context7-live-search.js` gibi hafif scriptler.

## 5. Context7 × Yalıhan Bekçi Entegrasyonu
- `.context7/authority.json` + alt standartlar (FORM_DESIGN, TAILWIND-TRANSITION, HARITA_SISTEMI, ROUTE_NAMING…).
- `.yalihan-bekci/knowledge/*.json`: Bekçi’nin öğrendiği kurallar (mahalle_id standardı, Tailwind transition zorunluluğu vb.).
- `yalihan-bekci/reports/`: Güncel raporlar `reports/summary/`, eski raporlar `reports/archive/YYYY-MM/` altında tutulmalı.
- `docs/yalihan-bekci/CONTEXT7-INTEGRATION-GUIDE.md`: Bekçi’nin yetkinlikleri ve sorumluluk matrisi.

## 6. Dokümantasyon Haritası
- **Aktif Rehberler:** `docs/active/*` (Context7 master guide, API referansları, bu dosya).
- **Yol Haritası:** `docs/roadmaps/` (2025 planları, sprintler).
- **Arşiv:** `docs/archive/` (tarih bazlı raporlar, eski rehberler).
- **Bekçi & Context7:** `docs/yalihan-bekci/` (entegrasyon rehberi, ileride rapor özetleri).

## 7. Gelecek Adımlar
- Modüller için test kapsamı ve TODO durumunu Bekçi raporlarına dahil et.
- Bu dokümanı yeni servis/entegrasyon eklendikçe güncelle.
- MD dağınıklığını azaltmak için kökteki raporları arşive taşı, yalnızca aktif rehber/roadmap dosyaları bırak.

---
Bu özet, projeye yeni katılan ekip üyeleri ve Yalıhan Bekçi otomasyonları için hızlı bir referans sağlar. Her büyük refactor veya yeni modül eklenişinde güncellenmelidir.
# Proje Anatomisi ve Modül Haritası

Bu belge, Yalıhan Emlak Warp projesindeki ana modülleri, ilgili dosya/katalog yapılarını ve temel sorumlulukları özetler. Yalıhan Bekçi ve Context7 kontrolleri için referans olarak kullanılır.

## 1. Mimari Genel Bakış
- **Framework:** Laravel 10 + Blade (Tailwind + Alpine.js) frontend.
- **Veri Katmanı:** MySQL. Modeller `app/Models` altında (ilan, talep, kişi, yazlık, rezervasyon vb.).
- **Servis Katmanı:** Context7 standartlarına göre `app/Services` altında gruplanmış (PropertyFeedService, AIService, Location servisleri vb.).
- **Arayüz:** Yönetim paneli `resources/views/admin`, frontend `resources/views/frontend`, bileşenler `resources/views/components`.
- **Otomasyon:** Yalıhan Bekçi raporları `yalihan-bekci/`, Context7 otorite dosyaları `.context7/`.

## 2. Ana Modüller
| Modül | Ana Controller/Servisler | Görünümler | Sorumluluk |
| --- | --- | --- | --- |
| **İlan Yönetimi** | `Admin/IlanController`, `PropertyFeedService`, `IlanService` | `admin/ilanlar/*`, `frontend/ilanlar/*`, `components/yaliihan/property-card.blade.php` | İlan CRUD, yayınlama, frontend kart ve modallar |
| **Talep Yönetimi** | `Admin/TalepController`, `TalepAnalizController`, `TalepPortfolyoController` | `admin/talepler/*`, `admin/talep-portfolyo/*` | Taleplerin kaydı, analiz ve eşleştirmeler |
| **Danışman Yönetimi** | `Admin/DanismanController`, `UserController`, `DanismanStatsService` | `admin/danisman/*`, `frontend/danismanlar/*` | Danışman CRUD, statüler, sanal danışman kartı |
| **Yazlık Kiralama** | `Admin/YazlikKiralamaController`, `YazlikFiyatlandirma`, `YazlikRezervasyon` servisleri | `admin/ilanlar` yazlık bölümleri, `components/yaliihan/type-fields.blade.php` | Sezonluk fiyat ve rezervasyon yönetimi |
| **Harita & Lokasyon** | `Api/LocationController`, `Location/NearbyService`, `Context7LocationService` | `components/yaliihan/map-component.blade.php`, harita modalları | Google Maps/Türkiye API entegrasyonu, lokasyon seçimleri |
| **AI & Sanal Danışman** | `Api/AIController`, `AIService`, `Context7LiveSearch` | `admin/components/ai-widget.blade.php`, `frontend/danismanlar`, `/ai/explore` | AI tabanlı analiz, içerik üretimi, sanal danışman |
| **Raporlama & Analytics** | `Admin/AnalyticsController`, `DashboardController`, `reports/` | `admin/analytics/*`, `admin/dashboard/*` | Panel metrikleri, arama performansı, Bekçi tetikleyicileri |

## 3. Servis ve Yardımcı Katmanı
- `app/Services/Frontend/PropertyFeedService.php`: Frontend ilan kartları ve `/api/frontend/properties` uçları için merkezi veri kaynağı.
- `app/Services/AIService.php`: OpenAI, Gemini, Claude, DeepSeek, Ollama sağlayıcılarını yöneten ana servis.
- `app/Services/Location/` ve `app/Services/Context7/`: Lokasyon izolasyonu, Context7 kurallarını uygulayan yardımcılar.
- `scripts/`: Kod kalitesi/Context7 denetimi (`comprehensive-code-check.php`, `find-incomplete-code.php`).

## 4. Frontend Yapısı
- **Layoutlar:** `layouts/frontend.blade.php`, `admin/layouts/sidebar.blade.php` (Tailwind + dark mode).
- **Bileşenler:** `components/yaliihan/*` (hero, property-card, map, contact, modal vs.).
- **Modallar:** `yaliihan-home-clean.blade.php` içindeki `virtualTour`, `gallery`, `map`, `propertyDetail` modalları global JS ile yönetilir.
- **JS Standartları:** Vanilla JS + Alpine. `public/js/context7-live-search.js` gibi hafif scriptler.

## 5. Context7 × Yalıhan Bekçi Entegrasyonu
- `.context7/authority.json` + alt standartlar (FORM_DESIGN, TAILWIND-TRANSITION, HARITA_SISTEMI, ROUTE_NAMING…).
- `.yalihan-bekci/knowledge/*.json`: Bekçi’nin öğrendiği kurallar (mahalle_id standardı, Tailwind transition zorunluluğu vb.).
- `yalihan-bekci/reports/`: Güncel raporlar `reports/summary/`, eski raporlar `reports/archive/YYYY-MM/` altında tutulmalı.
- `docs/yalihan-bekci/CONTEXT7-INTEGRATION-GUIDE.md`: Bekçi’nin yetkinlikleri ve sorumluluk matrisi.

## 6. Dokümantasyon Haritası
- **Aktif Rehberler:** `docs/active/*` (Context7 master guide, API referansları, bu dosya).
- **Yol Haritası:** `docs/roadmaps/` (2025 planları, sprintler).
- **Arşiv:** `docs/archive/` (tarih bazlı raporlar, eski rehberler).
- **Bekçi & Context7:** `docs/yalihan-bekci/` (entegrasyon rehberi, ileride rapor özetleri).

## 7. Gelecek Adımlar
- Modüller için test kapsamı ve TODO durumunu Bekçi raporlarına dahil et.
- Bu dokümanı yeni servis/entegrasyon eklendikçe güncelle.
- MD dağınıklığını azaltmak için kökteki raporları arşive taşı, yalnızca aktif rehber/roadmap dosyaları bırak.

---
Bu özet, projeye yeni katılan ekip üyeleri ve Yalıhan Bekçi otomasyonları için hızlı bir referans sağlar. Her büyük refactor veya yeni modül eklenişinde güncellenmelidir.
