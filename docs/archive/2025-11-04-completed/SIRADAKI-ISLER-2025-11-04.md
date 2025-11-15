# 📋 Sıradaki İşler - Öncelik Listesi

**Tarih:** 4 Kasım 2025  
**Mevcut Durum:** Photo Model tamamlandı ✅  
**Kalan TODO:** 29 (önceki: 39)

---

## 🔴 ÖNCELİK 1: EVENT/SEASON MODEL (YÜKSEKBir ÖNCELİK)

**Dosya:** `app/Http/Controllers/Admin/TakvimController.php`  
**TODO Sayısı:** 6  
**Tahmini Süre:** 3 saat  
**Durum:** %60 tamamlanmış (backend hazır, model eksik)

### Neden Yüksek Öncelik?

```yaml
Sebep:
    - Rezervasyon sistemi yarım kalmış
    - TakvimController'da 6 TODO var
    - Production'da kullanılıyor ama model yok
    - DB::table() kullanılıyor (Photo gibi)

Faydası:
    - Rezervasyon sistemi tamamlanır
    - 6 TODO biter (29 → 23)
    - Eloquent avantajları
    - Airbnb/Booking entegrasyonu hazır olur
```

### Yapılacaklar:

```bash
1. Event Model oluştur (1 saat)
   - php artisan make:model Event -m
   - Migration: title, start, end, ilan_id, type, status
   - Relationships: belongsTo Ilan

2. Season Model oluştur (1 saat)
   - php artisan make:model Season -m
   - Migration: name, start_date, end_date, daily_price
   - Relationships: belongsTo Ilan

3. TakvimController güncelle (1 saat)
   - 6 TODO'yu tamamla
   - Event::create(), update(), delete()
   - Season::create(), update(), delete()

TOPLAM: 3 saat, 6 TODO tamamlanır
```

**Başlarsak:** Takvim sistemi %100 tamamlanır! 🎯

---

## 🟡 ÖNCELİK 2: ROUTES TEMİZLİĞİ (ORTA ÖNCELİK)

**Dosyalar:** `routes/*.php`  
**Tahmini Süre:** 30-45 dakika  
**Risk:** Düşük

### Yapılacaklar:

```bash
1. Unused routes tespiti (15 dk)
   - grep ile kullanılmayan route'ları bul
   - Controller method'larla eşleştir

2. Duplicate routes kontrolü (15 dk)
   - Aynı endpoint'i işaret eden route'lar
   - Modül routes vs main routes çakışması

3. Dead endpoints temizliği (15 dk)
   - 404 dönen route'lar
   - Controller'ı olmayan route'lar
   - Deprecated route'lar

TOPLAM: 45 dakika
```

**Faydası:**

- Daha temiz routing
- Daha hızlı route resolution
- Karışıklık azalır

---

## 🟢 ÖNCELİK 3: PHOTO MODEL TEST (DÜŞÜK ÖNCELİK)

**Süre:** 30 dakika  
**Amaç:** Photo Model'in çalıştığını doğrula

### Test Senaryoları:

```php
1. Photo oluştur ve kaydet
   - Upload test
   - Thumbnail oluşturuldu mu?
   - Optimize edildi mi?

2. Relationships test
   - $ilan->photos çalışıyor mu?
   - $ilan->featuredPhoto çalışıyor mu?

3. Helper methods test
   - incrementViews() çalışıyor mu?
   - setAsFeatured() çalışıyor mu?

4. Bulk actions test
   - Delete, move, feature çalışıyor mu?

5. Soft delete test
   - Soft delete çalışıyor mu?
   - Hard delete dosyaları siliyor mu?
```

**Faydası:**

- Production'a güvenle çıkabilir
- Bug tespiti
- Regression prevention

---

## 🔵 ÖNCELİK 4: COMPONENT MİGRATİON BAŞLANGICI (UZUN VADELİ)

**Süre:** 2-3 saat (ilk adım)  
**Hedef:** Component adoption %5 → %70 (3 ay)

### İlk Adım - Migration Strategy:

```bash
1. Component inventory (30 dk)
   - Hangi component'ler var?
   - Hangisi ne zaman kullanılmalı?
   - Migration önceliği belirle

2. İlk migration target seç (30 dk)
   - Küçük bir sayfa seç (test için)
   - Manuel HTML → Component'e dönüştür
   - Test et

3. Migration script oluştur (1 saat)
   - Otomatik dönüşüm script'i
   - Regex patterns
   - Validation

4. Bulk migration (1 saat)
   - Script'i çalıştır
   - Review yap
   - Commit

TOPLAM: 3 saat (ilk gün)
```

**Faydası:**

- Tutarlı UI/UX
- Kolay bakım
- Dark mode otomatik
- Context7 compliance otomatik

---

## 🟣 ÖNCELİK 5: LOG ROTATION AYARLA (HİZMET İYİLEŞTİRME)

**Süre:** 15 dakika  
**Amaç:** 75 MB log sorununu kalıcı çöz

### Yapılacaklar:

```bash
1. Laravel logging.php güncelle
   - daily rotation
   - max 7 dosya tut
   - auto compression

2. Cron job ekle (opsiyonel)
   - Günlük log temizliği
   - 7 günden eski logları sil

TOPLAM: 15 dakika
```

**config/logging.php:**

```php
'daily' => [
    'driver' => 'daily',
    'path' => storage_path('logs/laravel.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'days' => 7, // Son 7 gün
    'permission' => 0644,
],
```

---

## 📊 ÖNCELİK MATRİSİ

| Öncelik | İş                      | Süre   | Fayda         | TODO Azalması | Zorluk    |
| ------- | ----------------------- | ------ | ------------- | ------------- | --------- |
| 🔴 1    | **Event/Season Model**  | 3 saat | 🔥🔥🔥 Yüksek | -6 TODO       | Orta      |
| 🟡 2    | **Routes Temizliği**    | 45 dk  | 🔥🔥 Orta     | 0             | Düşük     |
| 🟢 3    | **Photo Model Test**    | 30 dk  | 🔥🔥 Orta     | 0             | Düşük     |
| 🔵 4    | **Component Migration** | 3 saat | 🔥🔥🔥 Yüksek | 0             | Yüksek    |
| 🟣 5    | **Log Rotation**        | 15 dk  | 🔥 Düşük      | 0             | Çok Düşük |

---

## 🚀 TAVSİYE EDİLEN SIRALAMA

### Şimdi (Akşam):

```yaml
✅ Log Rotation ayarla (15 dk) → Hızlı win
✅ Routes temizliği (45 dk) → Kolay
✅ Photo Model test (30 dk) → Doğrulama

TOPLAM: 1.5 saat
FAYDA: Hızlı sonuçlar, proje daha stabil
```

### Yarın (5 Kasım):

```yaml
✅ Event/Season Model (3 saat) → 6 TODO biter
✅ Component migration başlat (ilk adım, 2 saat)

TOPLAM: 5 saat
FAYDA: Major features tamamlanır
```

### Bu Hafta (6-10 Kasım):

```yaml
✅ Component migration devam (10 saat)
✅ Diğer TODO'lar (5 saat)
✅ Testing & Documentation (3 saat)

TOPLAM: 18 saat
HEDEF: TODO 29 → <15
```

---

## 💡 BENIM TAVSİYEM

### Senaryo A: "Hızlı Kazanımlar" (1.5 saat)

```yaml
1. Log Rotation (15 dk) ⚡
2. Routes Temizliği (45 dk) 🧹
3. Photo Test (30 dk) ✅

Fayda: Günü güzel kapat, yarın büyük işe başla
```

### Senaryo B: "Büyük Vurgu" (3 saat)

```yaml
1. Event/Season Model (3 saat) 🎯

Fayda: 6 TODO biter, rezervasyon tamamlanır
Risk: Yorucu olabilir
```

### Senaryo C: "Dengeli" (2 saat)

```yaml
1. Log Rotation (15 dk) ⚡
2. Routes Temizliği (45 dk) 🧹
3. Event Model başlangıç (1 saat) 🎯

Fayda: Hem hızlı win, hem progress
```

---

## ❓ SANA SORUM

**Hangisini yapalım?**

1. **A - Hızlı Kazanımlar** (1.5 saat, kolay)
2. **B - Event/Season Model** (3 saat, 6 TODO biter)
3. **C - Dengeli** (2 saat, karışık)
4. **Başka önerin var mı?**
5. **Bugünlük yeter, yarın devam?**

**Senin kararın! 😊**
