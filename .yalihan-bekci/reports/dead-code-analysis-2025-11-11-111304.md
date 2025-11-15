# Dead Code Analysis Report - 2025-11-11 11:13:04

## 📊 Özet

| Kategori | Toplam | Kullanılan | Kullanılmayan |
|----------|--------|------------|---------------|
| Class'lar | 454 | 398 | 140 |
| Trait'ler | 5 | 7 | 4 |
| Interface'ler | 0 | 9 | 0 |

**Temizlik Fırsatı:** 144 dosya

## 🗑️ Kullanılmayan Class'lar (İlk 50)

- **NotificationMail** (`app/Mail/NotificationMail.php`)
  - Sebep: Not referenced anywhere

- **AIServiceProvider** (`app/Providers/AIServiceProvider.php`)
  - Sebep: Not referenced anywhere

- **AppServiceProvider** (`app/Providers/AppServiceProvider.php`)
  - Sebep: Not referenced anywhere

- **mevcutsa** (`app/Providers/AppServiceProvider.php`)
  - Sebep: Not referenced anywhere

- **TelescopeServiceProvider** (`app/Providers/TelescopeServiceProvider.php`)
  - Sebep: Not referenced anywhere

- **HorizonServiceProvider** (`app/Providers/HorizonServiceProvider.php`)
  - Sebep: Not referenced anywhere

- **EventServiceProvider** (`app/Providers/EventServiceProvider.php`)
  - Sebep: Not referenced anywhere

- **Handler** (`app/Exceptions/Handler.php`)
  - Sebep: Not referenced anywhere

- **IlanPolicy** (`app/Policies/IlanPolicy.php`)
  - Sebep: Not referenced anywhere

- **TrackUserActivity** (`app/Http/Middleware/TrackUserActivity.php`)
  - Sebep: Not referenced anywhere

- **SetLocaleFromSession** (`app/Http/Middleware/SetLocaleFromSession.php`)
  - Sebep: Not referenced anywhere

- **Context7AuthMiddleware** (`app/Http/Middleware/Context7AuthMiddleware.php`)
  - Sebep: Not referenced anywhere

- **PerformanceOptimizationMiddleware** (`app/Http/Middleware/PerformanceOptimizationMiddleware.php`)
  - Sebep: Not referenced anywhere

- **Context7CacheMiddleware** (`app/Http/Middleware/Context7CacheMiddleware.php`)
  - Sebep: Not referenced anywhere

- **CheckUserRole** (`app/Http/Middleware/CheckUserRole.php`)
  - Sebep: Not referenced anywhere

- **AIRateLimitMiddleware** (`app/Http/Middleware/AIRateLimitMiddleware.php`)
  - Sebep: Not referenced anywhere

- **VerifyCsrfToken** (`app/Http/Middleware/VerifyCsrfToken.php`)
  - Sebep: Not referenced anywhere

- **RedirectIfAuthenticated** (`app/Http/Middleware/RedirectIfAuthenticated.php`)
  - Sebep: Not referenced anywhere

- **TrimStrings** (`app/Http/Middleware/TrimStrings.php`)
  - Sebep: Not referenced anywhere

- **Context7ComplianceMiddleware** (`app/Http/Middleware/Context7ComplianceMiddleware.php`)
  - Sebep: Not referenced anywhere

- **RoleBasedMenuMiddleware** (`app/Http/Middleware/RoleBasedMenuMiddleware.php`)
  - Sebep: Not referenced anywhere

- **RoleMiddleware** (`app/Http/Middleware/RoleMiddleware.php`)
  - Sebep: Not referenced anywhere

- **Authenticate** (`app/Http/Middleware/Authenticate.php`)
  - Sebep: Not referenced anywhere

- **ValidateFormDataCompleteness** (`app/Http/Middleware/ValidateFormDataCompleteness.php`)
  - Sebep: Not referenced anywhere

- **SuperAdminOnly** (`app/Http/Middleware/SuperAdminOnly.php`)
  - Sebep: Not referenced anywhere

- **TrustProxies** (`app/Http/Middleware/TrustProxies.php`)
  - Sebep: Not referenced anywhere

- **ApiRateLimitMiddleware** (`app/Http/Middleware/ApiRateLimitMiddleware.php`)
  - Sebep: Not referenced anywhere

- **ValidateSignature** (`app/Http/Middleware/ValidateSignature.php`)
  - Sebep: Not referenced anywhere

- **EnsureFeatureManagePermission** (`app/Http/Middleware/EnsureFeatureManagePermission.php`)
  - Sebep: Not referenced anywhere

- **CheckRole** (`app/Http/Middleware/CheckRole.php`)
  - Sebep: Not referenced anywhere

- **AdminMiddleware** (`app/Http/Middleware/AdminMiddleware.php`)
  - Sebep: Not referenced anywhere

- **SecurityMiddleware** (`app/Http/Middleware/SecurityMiddleware.php`)
  - Sebep: Not referenced anywhere

- **PreventRequestsDuringMaintenance** (`app/Http/Middleware/PreventRequestsDuringMaintenance.php`)
  - Sebep: Not referenced anywhere

- **EncryptCookies** (`app/Http/Middleware/EncryptCookies.php`)
  - Sebep: Not referenced anywhere

- **RateLimitMiddleware** (`app/Http/Middleware/RateLimitMiddleware.php`)
  - Sebep: Not referenced anywhere

- **KisiRequest** (`app/Http/Requests/Admin/KisiRequest.php`)
  - Sebep: Not referenced anywhere

- **HomeController** (`app/Http/Controllers/Frontend/HomeController.php`)
  - Sebep: Orphaned controller (no route)

- **PreferenceController** (`app/Http/Controllers/Frontend/PreferenceController.php`)
  - Sebep: Orphaned controller (no route)

- **AdminController** (`app/Http/Controllers/Admin/AdminController.php`)
  - Sebep: Orphaned controller (no route)

- **KategoriOzellikApiController** (`app/Http/Controllers/Admin/KategoriOzellikApiController.php`)
  - Sebep: Orphaned controller (no route)

- **TalepRaporController** (`app/Http/Controllers/Admin/TalepRaporController.php`)
  - Sebep: Orphaned controller (no route)

- **PerformanceController** (`app/Http/Controllers/Admin/PerformanceController.php`)
  - Sebep: Orphaned controller (no route)

- **PriceController** (`app/Http/Controllers/Admin/PriceController.php`)
  - Sebep: Orphaned controller (no route)

- **AdvancedAIController** (`app/Http/Controllers/Api/AdvancedAIController.php`)
  - Sebep: Orphaned controller (no route)

- **SmartFieldController** (`app/Http/Controllers/Api/SmartFieldController.php`)
  - Sebep: Orphaned controller (no route)

- **PropertyValuationController** (`app/Http/Controllers/Api/PropertyValuationController.php`)
  - Sebep: Orphaned controller (no route)

- **Context7ProjeController** (`app/Http/Controllers/Api/Context7ProjeController.php`)
  - Sebep: Orphaned controller (no route)

- **CurrencyController** (`app/Http/Controllers/Api/CurrencyController.php`)
  - Sebep: Orphaned controller (no route)

- **PersonController** (`app/Http/Controllers/Api/PersonController.php`)
  - Sebep: Orphaned controller (no route)

- **NearbyPlacesController** (`app/Http/Controllers/Api/NearbyPlacesController.php`)
  - Sebep: Orphaned controller (no route)

## 🗑️ Kullanılmayan Trait'ler

- **SearchableTrait** (`app/Traits/SearchableTrait.php`)
  - Sebep: Not used in any class

- **HasActiveScope** (`app/Traits/HasActiveScope.php`)
  - Sebep: Not used in any class

- **public** (`app/Models/BlogTag.php`)
  - Sebep: Not used in any class

- **HasRoles** (`app/Modules/Auth/Traits/HasRoles.php`)
  - Sebep: Not used in any class

