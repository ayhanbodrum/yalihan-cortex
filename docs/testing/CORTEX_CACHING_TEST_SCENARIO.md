# 🧪 CortexKnowledgeService Caching Test Senaryosu

**Tarih:** 2025-11-30  
**Versiyon:** 2.1.0  
**Test Tipi:** Manuel Test

---

## 📋 DURUM ÖZETİ

### ✅ YAPILAN İŞLEMLER

1. **✅ Caching (CortexKnowledgeService)** - TAMAMLANDI
   - Cache key üretimi eklendi
   - Cache kontrolü (HIT/MISS) eklendi
   - 24 saatlik TTL yapılandırıldı
   - Logging eklendi

2. **✅ Queue Worker Monitoring** - TAMAMLANDI
   - Dashboard'da queue worker durumu görüntüleniyor
   - Bekleyen işler, işlenen işler, başarısız işler takip ediliyor

### ❌ YAPILMAYAN İŞLEMLER

1. **❌ Telegram Rate Limiting** - YAPILMADI
   - Aynı ilan/talep için 1 saat içinde max 1 bildirim kontrolü yok

2. **❌ Health Check API Endpoint** - YAPILMADI
   - `/api/health` endpoint'leri oluşturulmadı

3. **❌ Queue Worker Alert System** - YAPILMADI
   - Queue worker durdurulduğunda otomatik bildirim yok
   - Sadece dashboard'da görüntüleme var (manuel kontrol gerekiyor)

---

## 🧪 TEST SENARYOSU: CORTEX CACHING

### Ön Koşullar

- [ ] Laravel development sunucusu çalışıyor (`php artisan serve`)
- [ ] Cache driver yapılandırılmış (Redis veya file cache)
- [ ] AnythingLLM servisi erişilebilir (opsiyonel - ilk sorgu için)
- [ ] Admin panel erişimi (`/admin`)

---

### TEST 1: Cache Key Üretimi

**Hedef:** Cache key'in doğru formatlanıp formatlandığını kontrol etmek.

**Adımlar:**

1. **Tinker ile Test:**
   ```bash
   php artisan tinker
   ```

2. **Service'i Yükle:**
   ```php
   $service = app(\App\Services\CortexKnowledgeService::class);
   ```

3. **Test Data Hazırla:**
   ```php
   $testData = [
       'ilce' => 'Bodrum',
       'mahalle' => 'Yalıkavak',
       'ada_no' => '101',
       'parsel_no' => '5',
       'alan_m2' => 500
   ];
   ```

4. **Cache Key Kontrolü:**
   ```php
   // Reflection ile private metodu test et
   $reflection = new ReflectionClass($service);
   $method = $reflection->getMethod('generateCacheKey');
   $method->setAccessible(true);
   $cacheKey = $method->invoke($service, $testData);
   
   echo "Cache Key: " . $cacheKey . "\n";
   // Beklenen: cortex:construction:bodrum:yalikavak:101:5
   ```

**Beklenen Sonuç:**
- Cache key formatı: `cortex:construction:{ilce}:{mahalle}:{ada}:{parsel}`
- Boşluklar temizlenmiş, lowercase yapılmış
- Özel karakterler alt çizgi ile değiştirilmiş

---

### TEST 2: Cache MISS (İlk Sorgu)

**Hedef:** İlk sorguda cache'de veri olmadığını ve API çağrısı yapıldığını kontrol etmek.

**Adımlar:**

1. **Cache'i Temizle:**
   ```bash
   php artisan cache:clear
   ```

2. **Log Dosyasını İzle:**
   ```bash
   tail -f storage/logs/laravel.log | grep "Cortex"
   ```

3. **İlk Sorguyu Yap:**
   ```php
   // Tinker'da
   $result = $service->queryConstructionRights($testData);
   ```

4. **Log Kontrolü:**
   - ✅ `Cortex Cache MISS` mesajı görünmeli
   - ✅ `CortexKnowledgeService: İmar analizi başarılı` mesajı görünmeli
   - ✅ `Cortex Cache MISS - Stored` mesajı görünmeli

5. **Sonuç Kontrolü:**
   ```php
   // Sonuç başarılı olmalı
   isset($result['success']) && $result['success'] === true
   ```

**Beklenen Sonuç:**
- İlk sorgu: 30-60 saniye sürmeli (API çağrısı)
- Log'da "Cache MISS" mesajları görünmeli
- Sonuç cache'lenmeli

---

### TEST 3: Cache HIT (İkinci Sorgu)

**Hedef:** Aynı sorgunun cache'den döndüğünü ve hızlı olduğunu kontrol etmek.

**Adımlar:**

1. **İkinci Sorguyu Yap (Aynı Data):**
   ```php
   // Tinker'da (aynı $testData ile)
   $startTime = microtime(true);
   $result2 = $service->queryConstructionRights($testData);
   $endTime = microtime(true);
   $duration = ($endTime - $startTime) * 1000; // milisaniye
   
   echo "Response Time: " . round($duration, 2) . " ms\n";
   ```

2. **Log Kontrolü:**
   - ✅ `Cortex Cache HIT` mesajı görünmeli
   - ❌ `Cortex Cache MISS` mesajı görünmemeli
   - ❌ API çağrısı yapılmamalı

3. **Sonuç Karşılaştırması:**
   ```php
   // İki sonuç aynı olmalı
   $result === $result2
   ```

**Beklenen Sonuç:**
- İkinci sorgu: ~0.01-0.1 saniye (cache'den)
- Log'da "Cache HIT" mesajı görünmeli
- Sonuçlar aynı olmalı

---

### TEST 4: Cache TTL (24 Saat)

**Hedef:** Cache'in 24 saat boyunca geçerli olduğunu kontrol etmek.

**Adımlar:**

1. **Cache'i Kontrol Et:**
   ```php
   // Tinker'da
   use Illuminate\Support\Facades\Cache;
   
   $cacheKey = "cortex:construction:bodrum:yalikavak:101:5";
   $cached = Cache::get($cacheKey);
   
   if ($cached) {
       echo "Cache'de veri var\n";
       // TTL bilgisini kontrol et (Laravel cache driver'a göre değişir)
   } else {
       echo "Cache'de veri yok\n";
   }
   ```

2. **Manuel TTL Testi (Opsiyonel):**
   ```php
   // Cache'i manuel olarak 1 saniye TTL ile kaydet
   Cache::put($cacheKey, $testResult, now()->addSeconds(1));
   
   // 1 saniye bekle
   sleep(2);
   
   // Cache'den oku
   $cached = Cache::get($cacheKey);
   // null olmalı (TTL dolmuş)
   ```

**Beklenen Sonuç:**
- Cache 24 saat boyunca geçerli olmalı
- TTL dolduktan sonra cache temizlenmeli

---

### TEST 5: Farklı Ada/Parsel (Cache MISS)

**Hedef:** Farklı ada/parsel için cache MISS olduğunu kontrol etmek.

**Adımlar:**

1. **Farklı Data ile Sorgu:**
   ```php
   // Tinker'da
   $testData2 = [
       'ilce' => 'Bodrum',
       'mahalle' => 'Yalıkavak',
       'ada_no' => '102',  // Farklı ada
       'parsel_no' => '10', // Farklı parsel
       'alan_m2' => 600
   ];
   
   $result3 = $service->queryConstructionRights($testData2);
   ```

2. **Log Kontrolü:**
   - ✅ `Cortex Cache MISS` mesajı görünmeli (farklı key)
   - ✅ API çağrısı yapılmalı

**Beklenen Sonuç:**
- Farklı ada/parsel için cache MISS olmalı
- Yeni cache key oluşturulmalı

---

### TEST 6: API Endpoint Testi (İlan Formu)

**Hedef:** Gerçek kullanım senaryosunda caching'in çalıştığını kontrol etmek.

**Adımlar:**

1. **İlan Oluşturma Sayfasına Git:**
   - URL: `/admin/ilanlar/create`
   - Kategori: **Arsa** seç

2. **Arsa Bilgilerini Gir:**
   - **Ada No:** `101`
   - **Parsel No:** `5`
   - **Alan (m²):** `500`
   - **İlçe:** Bodrum
   - **Mahalle:** Yalıkavak

3. **İlk Cortex Analizi:**
   - "Cortex İmar & İnşaat Analizi" kartında **"Analizi Başlat"** butonuna tıkla
   - Network tab'ında API çağrısını izle
   - Response time: ~30-60 saniye olmalı

4. **İkinci Cortex Analizi (Aynı Bilgilerle):**
   - Sayfayı yenilemeden tekrar **"Analizi Başlat"** butonuna tıkla
   - Network tab'ında API çağrısı olmamalı (cache'den dönmeli)
   - Response time: ~0.01-0.1 saniye olmalı

5. **Log Kontrolü:**
   ```bash
   tail -f storage/logs/laravel.log | grep "Cortex"
   ```
   - İlk sorgu: `Cortex Cache MISS`
   - İkinci sorgu: `Cortex Cache HIT`

**Beklenen Sonuç:**
- İlk sorgu: API çağrısı yapılır, yavaş
- İkinci sorgu: Cache'den döner, hızlı

---

## ✅ TEST SONUÇLARI ŞABLONU

```
Test Tarihi: _______________
Test Eden: _______________

[ ] TEST 1: Cache Key Üretimi
    Durum: ✅ Başarılı / ❌ Başarısız
    Cache Key: ________________________________
    Notlar: ________________________________

[ ] TEST 2: Cache MISS (İlk Sorgu)
    Durum: ✅ Başarılı / ❌ Başarısız
    Response Time: ___________ saniye
    Log Mesajları: ✅ Görüldü / ❌ Görülmedi
    Notlar: ________________________________

[ ] TEST 3: Cache HIT (İkinci Sorgu)
    Durum: ✅ Başarılı / ❌ Başarısız
    Response Time: ___________ ms
    Log Mesajları: ✅ Görüldü / ❌ Görülmedi
    Notlar: ________________________________

[ ] TEST 4: Cache TTL (24 Saat)
    Durum: ✅ Başarılı / ❌ Başarısız
    Notlar: ________________________________

[ ] TEST 5: Farklı Ada/Parsel (Cache MISS)
    Durum: ✅ Başarılı / ❌ Başarısız
    Notlar: ________________________________

[ ] TEST 6: API Endpoint Testi (İlan Formu)
    Durum: ✅ Başarılı / ❌ Başarısız
    İlk Sorgu Response Time: ___________ saniye
    İkinci Sorgu Response Time: ___________ ms
    Notlar: ________________________________
```

---

## 🚨 SORUN GİDERME

### Cache Çalışmıyor

**Sorun:** Cache HIT olmuyor, her seferinde API çağrısı yapılıyor.

**Çözüm:**
1. Cache driver kontrolü:
   ```bash
   php artisan tinker
   >>> config('cache.default')
   ```

2. Cache temizleme:
   ```bash
   php artisan cache:clear
   ```

3. Cache key formatını kontrol et:
   ```php
   // Tinker'da
   $service = app(\App\Services\CortexKnowledgeService::class);
   $reflection = new ReflectionClass($service);
   $method = $reflection->getMethod('generateCacheKey');
   $method->setAccessible(true);
   $key = $method->invoke($service, $testData);
   echo $key;
   ```

### Cache Key Formatı Yanlış

**Sorun:** Cache key'de boşluklar veya özel karakterler var.

**Çözüm:**
- `normalizeCacheKeyPart()` metodunu kontrol et
- Boşluklar alt çizgi ile değiştirilmeli
- Lowercase yapılmalı

### Log Mesajları Görünmüyor

**Sorun:** Log'da "Cortex Cache HIT/MISS" mesajları yok.

**Çözüm:**
1. Log seviyesini kontrol et:
   ```php
   // config/logging.php
   'level' => 'debug' // veya 'info'
   ```

2. Log kanalını kontrol et:
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## 📊 PERFORMANS METRİKLERİ

### Beklenen İyileştirmeler

- **İlk Sorgu:** 30-60 saniye (API çağrısı)
- **Cache HIT:** 0.01-0.1 saniye (cache'den)
- **Performans Artışı:** %95+ response time azalması
- **Maliyet Azalması:** %80+ AnythingLLM API çağrısı azalması

### Ölçüm Komutları

```bash
# Response time ölçümü
php artisan tinker
>>> $start = microtime(true);
>>> $result = $service->queryConstructionRights($testData);
>>> $duration = (microtime(true) - $start) * 1000;
>>> echo "Response Time: " . round($duration, 2) . " ms\n";
```

---

**Son Güncelleme:** 2025-11-30  
**Hazırlayan:** Yalıhan Cortex Testing Team  
**Durum:** ✅ Test Senaryosu Hazır

