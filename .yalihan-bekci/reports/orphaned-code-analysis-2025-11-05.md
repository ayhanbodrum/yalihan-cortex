# Orphaned Code Analizi - 2025-11-05

## Gerçekten Orphaned Olan Controller'lar

### Admin Controllers (Route'da YOK):
1. **ArsaCalculationController** - Route yok, ama kullanılabilir (API endpoint olarak)
2. **YalihanBekciController** - Route yok, ama monitoring için gerekli olabilir
3. **PerformanceController** - Route yok, IlanController içinde performance route'ları var
4. **PriceController** - Route yok, IlanController içinde price route'ları var
5. **MusteriController** - Redirect var (KisiController'a), ama kendisi kullanılmıyor

### API Controllers (Route'da YOK):
1. **Context7* Controllers** - Route'da yok, eski Context7 API'leri
2. **CurrencyController** - Route'da yok
3. **HybridSearchController** - Route'da yok
4. **LanguageController** - Route'da yok
5. **ListingSearchController** - Route'da yok
6. **LiveSearchController** - Route'da yok
7. **NearbyPlacesController** - Route'da yok
8. **PersonController** - Route'da yok
9. **PropertyValuationController** - Route'da yok

## Öneriler

### 1. Route Ekle (Kullanılabilir controller'lar):
- ArsaCalculationController → API endpoint olarak eklenebilir
- YalihanBekciController → Monitoring dashboard için route eklenebilir

### 2. Sil/Kaldır (Kullanılmayan controller'lar):
- MusteriController → Zaten redirect var, silinebilir veya deprecated olarak işaretlenebilir
- Context7* Controllers → Eski API'ler, deprecated olarak işaretlenebilir
- Diğer kullanılmayan API controller'lar

### 3. Konsolide Et (Fonksiyonellik başka yerde):
- PerformanceController → IlanController içinde var
- PriceController → IlanController içinde var

