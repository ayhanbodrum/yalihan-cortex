# Dead Code Temizliği Faz 2 Planı - 2025-11-11

**Tarih:** 2025-11-11 23:59  
**Durum:** 📋 PLANLAMA

---

## 📊 MEVCUT DURUM

### Dead Code Analizi Sonuçları
- **Toplam Kullanılmayan Class:** 119 adet
- **Kategoriler:**
  - Service Provider: ~5 adet
  - Middleware: ~30 adet
  - Handler: ~1 adet
  - Diğer: ~83 adet

---

## ✅ FALSE POSITIVE'LER (Temizlenmeyecek)

### 1. Service Provider'lar (~5 adet)
**Durum:** Config'de kayıtlı, Laravel otomatik yükler

- ✅ `AppServiceProvider` - `config/app.php`'de kayıtlı
- ✅ `EventServiceProvider` - `config/app.php`'de kayıtlı
- ✅ `TelescopeServiceProvider` - `config/app.php`'de kayıtlı
- ✅ `AIServiceProvider` - `config/app.php`'de kayıtlı
- ⚠️ `HorizonServiceProvider` - Kontrol edilmeli

**Aksiyon:** Bu class'lar temizlenmeyecek (false positive)

---

### 2. Middleware'ler (~30 adet)
**Durum:** Kernel.php'de kayıtlı, Laravel otomatik yükler

- ✅ `TrackUserActivity` - Kernel.php'de kayıtlı
- ✅ `SetLocaleFromSession` - Kernel.php'de kayıtlı
- ✅ `Context7AuthMiddleware` - Kernel.php'de kayıtlı
- ✅ `PerformanceOptimizationMiddleware` - Kernel.php'de kayıtlı
- ✅ `VerifyCsrfToken` - Kernel.php'de kayıtlı
- ✅ `RedirectIfAuthenticated` - Kernel.php'de kayıtlı
- ✅ `TrimStrings` - Kernel.php'de kayıtlı
- Ve diğerleri...

**Aksiyon:** Bu class'lar temizlenmeyecek (false positive)

---

### 3. Handler (~1 adet)
**Durum:** Bootstrap'te kayıtlı, Laravel exception handling için gerekli

- ✅ `Handler` - `bootstrap/app.php`'de kayıtlı

**Aksiyon:** Bu class temizlenmeyecek (false positive)

---

## 🔍 GERÇEK DEAD CODE (Temizlenecek)

### 1. Policy'ler (~5 adet)
**Durum:** Kullanılmıyor olabilir

- ⚠️ `IlanPolicy` - Kontrol edilmeli
- Diğer policy'ler kontrol edilmeli

**Aksiyon:** Route ve controller'larda kullanım kontrolü yapılacak

---

### 2. Mail Class'ları (~5 adet)
**Durum:** Kullanılmıyor olabilir

- ⚠️ Mail class'ları kontrol edilmeli

**Aksiyon:** Kod tabanında kullanım kontrolü yapılacak

---

### 3. Diğer Class'lar (~73 adet)
**Durum:** Detaylı analiz gerekiyor

**Aksiyon:** Her class için kullanım kontrolü yapılacak

---

## 📋 TEMİZLİK STRATEJİSİ

### Faz 2A: False Positive Filtreleme (30 dakika)
1. Service Provider'ları filtrele (config/app.php kontrolü)
2. Middleware'leri filtrele (Kernel.php kontrolü)
3. Handler'ı filtrele (bootstrap/app.php kontrolü)

### Faz 2B: Gerçek Dead Code Analizi (1-2 saat)
1. Policy'leri kontrol et (route ve controller kullanımı)
2. Mail class'larını kontrol et (kod tabanında kullanım)
3. Diğer class'ları kontrol et (detaylı analiz)

### Faz 2C: Güvenli Temizlik (1 saat)
1. Archive klasörüne taşı
2. Route ve config kontrolü
3. Test çalıştır

---

## 🎯 HEDEF METRİKLER

| Metrik | Başlangıç | Hedef | İlerleme |
|--------|-----------|-------|----------|
| **Dead Code** | 119 | ~30 | ⏳ Planlama |
| **False Positive** | ~36 | 0 | ⏳ Filtreleme |
| **Gerçek Dead Code** | ~83 | ~30 | ⏳ Analiz |

---

## ✅ SONUÇ

**Dead Code Faz 2 Planı Hazır!** 📋

- ✅ False positive'ler belirlendi (~36 adet)
- ⏳ Gerçek dead code analizi yapılacak (~83 adet)
- ⏳ Güvenli temizlik yapılacak

---

**Son Güncelleme:** 2025-11-11 23:59  
**Durum:** 📋 DEAD CODE FAZ 2 PLANI HAZIR

