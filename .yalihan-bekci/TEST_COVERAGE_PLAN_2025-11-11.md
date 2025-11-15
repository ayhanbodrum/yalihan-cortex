# Test Coverage Plan - 2025-11-11

**Tarih:** 2025-11-11 21:15  
**Durum:** 📋 PLAN HAZIR  
**Mevcut Coverage:** ~%5 (1 test dosyası)  
**Hedef Coverage:** %30+

---

## 📊 MEVCUT DURUM

### Test Dosyaları
1. ✅ `tests/Feature/Context7ComplianceTest.php` - Context7 compliance testleri
2. ✅ `tests/Feature/Admin/AiMonitorEndpointsTest.php` - AI monitor endpoint testleri

### Test Yapısı
- ✅ PHPUnit yapılandırması mevcut (`phpunit.xml`)
- ✅ TestCase base class mevcut
- ✅ Feature test suite mevcut
- ⚠️ Unit test suite mevcut ama boş

---

## 🎯 HEDEF TEST ALANLARI

### 1. API Controller Tests (Öncelik: YÜKSEK)

#### Refactor Edilen Controller'lar
- [ ] `AIController` - 15 metod
- [ ] `AkilliCevreAnaliziController` - 4 metod
- [ ] `AdvancedAIController` - 5 metod
- [ ] `BookingRequestController` - 3 metod
- [ ] `ImageAIController` - 4 metod
- [ ] `TKGMController` - 4 metod
- [ ] `UnifiedSearchController` - 4 metod

**Toplam:** 39 metod için test gerekli

---

### 2. Service Tests (Öncelik: YÜKSEK)

#### Yeni Oluşturulan Service'ler
- [ ] `ResponseService` - Standardized response methods
- [ ] `StatisticsService` - Statistics generation
- [ ] `QRCodeService` - QR code generation
- [ ] `AIService` - AI operations

#### Mevcut Service'ler
- [ ] `IlanBulkService` - Bulk operations
- [ ] `IlanPhotoService` - Photo management
- [ ] `IlanExportService` - Export functionality

---

### 3. Trait Tests (Öncelik: ORTA)

#### Yeni Oluşturulan Trait'ler
- [ ] `ValidatesApiRequests` - API request validation
- [ ] `Filterable` - Filtering, searching, sorting

---

### 4. Model Tests (Öncelik: ORTA)

#### Kritik Model'ler
- [ ] `Ilan` - Main listing model
- [ ] `IlanKategori` - Category model
- [ ] `User` - User model
- [ ] `Talep` - Request model

---

### 5. Context7 Compliance Tests (Öncelik: YÜKSEK)

#### Mevcut Test Genişletilecek
- [x] `Context7ComplianceTest` - Mevcut
- [ ] Context7 migration tests
- [ ] Context7 model tests
- [ ] Context7 route tests

---

## 📋 TEST PLANI

### Phase 1: API Controller Tests (Hedef: %15 coverage)

**Dosyalar:**
1. `tests/Feature/Api/AIControllerTest.php`
2. `tests/Feature/Api/AkilliCevreAnaliziControllerTest.php`
3. `tests/Feature/Api/AdvancedAIControllerTest.php`
4. `tests/Feature/Api/BookingRequestControllerTest.php`
5. `tests/Feature/Api/ImageAIControllerTest.php`
6. `tests/Feature/Api/TKGMControllerTest.php`
7. `tests/Feature/Api/UnifiedSearchControllerTest.php`

**Test Senaryoları:**
- ✅ Success responses (200)
- ✅ Validation errors (422)
- ✅ Not found errors (404)
- ✅ Unauthorized errors (401)
- ✅ Response format consistency
- ✅ Validation rules

---

### Phase 2: Service Tests (Hedef: %10 coverage)

**Dosyalar:**
1. `tests/Unit/Services/ResponseServiceTest.php`
2. `tests/Unit/Services/StatisticsServiceTest.php`
3. `tests/Unit/Services/QRCodeServiceTest.php`
4. `tests/Unit/Services/AIServiceTest.php`

**Test Senaryoları:**
- ✅ Method return types
- ✅ Error handling
- ✅ Cache behavior
- ✅ Service dependencies

---

### Phase 3: Trait Tests (Hedef: %5 coverage)

**Dosyalar:**
1. `tests/Unit/Traits/ValidatesApiRequestsTest.php`
2. `tests/Unit/Traits/FilterableTest.php`

**Test Senaryoları:**
- ✅ Trait methods functionality
- ✅ Integration with models
- ✅ Edge cases

---

## 🎯 ÖNCELİKLENDİRME

### 🔴 YÜKSEK ÖNCELİK (Hemen Başla)
1. API Controller Tests (7 dosya)
2. ResponseService Tests (1 dosya)
3. Context7 Compliance Tests (genişletme)

### 🟡 ORTA ÖNCELİK (Bu Hafta)
4. Service Tests (3 dosya)
5. Trait Tests (2 dosya)

### 🟢 DÜŞÜK ÖNCELİK (Gelecek Hafta)
6. Model Tests (4 dosya)
7. Integration Tests

---

## 📊 HEDEF METRİKLER

| Kategori | Mevcut | Hedef | Artış |
|----------|--------|-------|-------|
| **Feature Tests** | 2 | 15 | +13 |
| **Unit Tests** | 0 | 10 | +10 |
| **Total Tests** | 2 | 25 | +23 |
| **Coverage** | ~%5 | %30+ | +%25 |

---

## 🚀 BAŞLANGIÇ ADIMLARI

### 1. Test Infrastructure Hazırlık
```bash
# Test database oluştur
php artisan migrate --database=testing

# Test coverage raporu oluştur
php artisan test --coverage
```

### 2. İlk Test Dosyası: ResponseServiceTest
- En basit ve kritik service
- Bağımlılık yok
- Hızlı yazılabilir

### 3. İkinci Test Dosyası: AIControllerTest
- Yeni refactor edilen controller
- ResponseService kullanıyor
- Kritik business logic

---

## 📝 TEST YAZMA STANDARTLARI

### Naming Convention
```php
// Feature Tests
class AIControllerTest extends TestCase
{
    public function test_analyze_returns_success_response()
    {
        // Test implementation
    }
}

// Unit Tests
class ResponseServiceTest extends TestCase
{
    public function test_success_method_returns_json_response()
    {
        // Test implementation
    }
}
```

### Test Structure
```php
public function test_method_name()
{
    // Arrange
    $data = [...];
    
    // Act
    $result = $service->method($data);
    
    // Assert
    $this->assertTrue($result->success);
}
```

---

## ✅ BAŞARI KRİTERLERİ

1. ✅ **%30+ Coverage:** Tüm kod tabanında %30+ test coverage
2. ✅ **API Tests:** Tüm refactor edilen API controller'lar için test
3. ✅ **Service Tests:** Tüm yeni service'ler için test
4. ✅ **Trait Tests:** Tüm yeni trait'ler için test
5. ✅ **CI/CD Integration:** Test'ler CI/CD pipeline'da çalışıyor

---

## 📈 İLERLEME TAKİBİ

### Tamamlanan
- [x] Test planı oluşturuldu
- [x] Test yapısı analiz edildi
- [x] Önceliklendirme yapıldı

### Devam Eden
- [ ] ResponseServiceTest yazılıyor
- [ ] AIControllerTest yazılıyor

### Bekleyen
- [ ] Diğer API controller testleri
- [ ] Service testleri
- [ ] Trait testleri

---

**Son Güncelleme:** 2025-11-11 21:15  
**Durum:** 📋 TEST COVERAGE PLAN HAZIR

