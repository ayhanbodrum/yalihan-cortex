# Dead Code Temizlik Planı - 2025-11-11

**Tarih:** 2025-11-11 23:59  
**Durum:** 🔄 PLAN HAZIR

---

## 📊 GÜNCEL DURUM

### Dead Code: -1537 adet
- **Öncelik:** ORTA 🟢
- **Kategori:** Kullanılmayan class'lar, metodlar, trait'ler

---

## ✅ ÖNCEKİ TEMİZLİKLER

### Tamamlanan Temizlikler:

1. **Orphaned Controller'lar (28 adet)** ✅
   - Archive'e taşındı: `archive/dead-code-20251111/controllers/`
   - Route kontrolü yapıldı
   - Güvenli temizlik yapıldı

2. **Güvenli Dosyalar (5 adet)** ✅
   - `KisiRequest.php`
   - `PriceRequest.php`
   - `NeoSelect.php`
   - `LocationSelector.php`
   - `Label.php`
   - Archive'e taşındı: `archive/dead-code-safe-20251111/`

3. **Policy Dosyaları (1 adet)** ✅
   - `IlanPolicy.php`
   - Archive'e taşındı: `archive/dead-code-20251111/policies/`

**Toplam:** ~34 dosya archive'e taşındı

---

## 📋 KALAN DEAD CODE (-1537 adet)

### Kategoriler:

1. **Kullanılmayan Class'lar**
   - Model'ler
   - Controller'lar
   - Service'ler
   - Helper'lar

2. **Kullanılmayan Metodlar**
   - Private metodlar
   - Protected metodlar
   - Public metodlar (kullanılmayan)

3. **Kullanılmayan Trait'ler**
   - Özel trait'ler
   - Utility trait'ler

---

## 🎯 TEMİZLİK STRATEJİSİ

### Güvenli Temizlik (Öncelik: YÜKSEK)

1. **Kullanılmayan Controller'lar**
   - Route kontrolü yapılmalı
   - Archive'e taşınmalı
   - Test edilmeli

2. **Kullanılmayan Trait'ler**
   - Kullanım kontrolü yapılmalı
   - Archive'e taşınmalı

3. **Kullanılmayan Service'ler**
   - Dependency kontrolü yapılmalı
   - Archive'e taşınmalı

### Dikkatli Olunması Gerekenler (Öncelik: ORTA)

1. **Service Provider'lar**
   - Middleware kayıtları
   - Route kayıtları
   - Event listener kayıtları
   - **DİKKAT:** Silmeden önce mutlaka kontrol edilmeli

2. **Event Listener'lar**
   - Event kayıtları kontrol edilmeli
   - **DİKKAT:** Silmeden önce mutlaka kontrol edilmeli

3. **Command'lar**
   - Artisan komutları
   - **DİKKAT:** Silmeden önce mutlaka kontrol edilmeli

4. **Model'ler**
   - Database ilişkileri kontrol edilmeli
   - **DİKKAT:** Silmeden önce mutlaka kontrol edilmeli

---

## 📋 SONRAKI ADIMLAR

### 1. Güvenli Dead Code Temizliği (Öncelik: YÜKSEK)
- Küçük batch'ler halinde temizlik
- Her batch'te test yapılmalı
- Archive'e taşıma

### 2. Orphaned Code Temizliği (Öncelik: YÜKSEK)
- 9 adet orphaned controller
- Route kontrolü
- Archive'e taşıma

### 3. Script İyileştirmesi (Öncelik: ORTA)
- Dead code analyzer iyileştirmesi
- False positive filtreleme
- Daha doğru tespit

---

## 🎯 HEDEF

- ✅ Güvenli dead code temizliği
- ✅ Archive'e taşıma (silme değil)
- ✅ Test edilebilirlik
- ✅ Geri dönüş imkanı

---

**Son Güncelleme:** 2025-11-11 23:59  
**Durum:** 🔄 DEAD CODE TEMİZLİK PLANI HAZIR

