# Sonraki Adımlar - Detaylı Plan - 2025-11-11

**Tarih:** 2025-11-11 23:59  
**Durum:** 📋 PLAN HAZIR

---

## ✅ BUGÜN TAMAMLANAN İŞLER

### Tamamlanan Görevler (15/15)

- ✅ Yüksek öncelikli: 2/2
- ✅ Orta öncelikli: 2/2
- ✅ Düşük öncelikli: 2/2
- ✅ Ek işler: 3
- ✅ Test coverage: 1

---

## 📋 SONRAKI ADIMLAR (Öncelik Sırasına Göre)

### 1. 🟡 Test Coverage Artırma (Devam)

#### A. FilterableTest Düzeltmesi ✅

- **Durum:** TAMAMLANDI
- **Yapılan:** Factory → DB::table, assertion'lar düzeltildi
- **Sonuç:** Test çalışır hale getirildi

#### B. Model Testleri (Öncelik: YÜKSEK)

- **Hedef:** Kritik model'ler için test
- **Dosyalar:**
    - `tests/Unit/Models/IlanTest.php`
    - `tests/Unit/Models/IlanKategoriTest.php`
    - `tests/Unit/Models/UserTest.php`
- **Süre:** 2-3 saat
- **Kapsam:**
    - Model relationships
    - Model scopes
    - Model accessors/mutators
    - Model validations

#### C. Controller Testleri (Öncelik: ORTA)

- **Hedef:** Refactor edilen controller'lar için test
- **Dosyalar:**
    - `tests/Feature/Admin/IlanControllerTest.php`
    - `tests/Feature/Api/AIControllerTest.php`
- **Süre:** 3-4 saat
- **Kapsam:**
    - API endpoint testleri
    - Response format testleri
    - Validation testleri

---

### 2. 🟡 Dead Code Temizliği (Devam)

#### A. Policy Kontrolü

- **Durum:** IlanPolicy kullanılmıyor görünüyor
- **Aksiyon:** AuthServiceProvider'da kayıt kontrolü
- **Süre:** 30 dakika

#### B. Kalan Güvenli Dead Code

- **Durum:** ~10 adet güvenli dead code kaldı
- **Aksiyon:** Archive'e taşı
- **Süre:** 1 saat

---

### 3. 🟢 Performance İyileştirmeleri (Opsiyonel)

#### A. Kalan N+1 Sorunları

- **Durum:** 18 gerçek N+1 sorunu düzeltildi
- **Kalan:** False positive'ler ve özel durumlar
- **Süre:** 2-3 saat

#### B. Cache Mekanizmaları

- **Hedef:** Daha fazla cache kullanımı
- **Süre:** 2-3 saat

---

### 4. 🟢 Code Duplication (Opsiyonel)

#### A. Diğer Controller'lar

- **Durum:** 5 controller refactor edildi
- **Kalan:** Özel durumlar içeren controller'lar
- **Süre:** 2-3 saat

---

## 🎯 ÖNERİLEN SIRA

### Bugün (Hızlı İşler)

1. ✅ FilterableTest düzeltmesi (TAMAMLANDI)
2. 📋 Model testleri başlat (IlanTest)

### Yarın (Orta Öncelik)

3. Model testleri tamamla
4. Controller testleri başlat
5. Dead Code temizliği (Policy kontrolü)

### Gelecek (Düşük Öncelik)

6. Performance iyileştirmeleri
7. Code Duplication (devam)

---

## 📊 HEDEF METRİKLER

| Metrik                   | Mevcut | Hedef | İlerleme |
| ------------------------ | ------ | ----- | -------- |
| **Test Dosyası**         | 4      | 10    | ⏳ %40   |
| **Test Metodu**          | ~22    | ~50   | ⏳ %44   |
| **Coverage**             | ~%5    | %30+  | ⏳ %17   |
| **Dead Code Temizlendi** | 33     | 50    | ⏳ %66   |

---

## 💡 ÖNERİLER

### Hızlı Kazanımlar (Bugün)

- ✅ FilterableTest düzeltmesi → Test çalışır hale geldi
- 📋 Model testleri → Coverage artışı

### Orta Vadeli (Bu Hafta)

- Controller testleri → API endpoint güvenilirliği
- Dead Code temizliği → Daha temiz kod

### Uzun Vadeli (Gelecek)

- Performance iyileştirmeleri → Daha hızlı sistem
- Code Duplication → Daha standart kod

---

**Son Güncelleme:** 2025-11-11 23:59  
**Durum:** 📋 SONRAKI ADIMLAR PLANLANDI
