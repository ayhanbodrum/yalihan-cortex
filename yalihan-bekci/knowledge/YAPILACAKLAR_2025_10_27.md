# 📋 Yapılacaklar Listesi - 27 Ekim 2025

**Durum:** 🔄 Devam Ediyor  
**Son Güncelleme:** 27 Ekim 2025 12:15

---

## ✅ TAMAMLANAN İŞLER

### 1. 📅 Takvim Entegrasyon Sistemi (TAMAMLANDI - 27 Ekim)
- ✅ 2 migration oluşturuldu
- ✅ 2 model oluşturuldu
- ✅ CalendarSyncService servisi eklendi
- ✅ Ilan modeline yeni ilişkiler eklendi
- ✅ Dokümantasyon oluşturuldu
- ✅ MCP sistemine öğretildi

### 2. 🏗️ İlan Modülleri Analizi (TAMAMLANDI - 27 Ekim)
- ✅ Detaylı sistem analiz raporu
- ✅ Tutarlılık sorunları tespit edildi
- ✅ Özet rapor oluşturuldu
- ✅ Dokümantasyon tamamlandı

---

## 🔄 YAPILACAK İŞLER (Öncelik Sırasına Göre)

### 1. 🔥 Model Duplikasyonu Çözümü

**Durum:** ⚠️ Riskli (20 dosya etkileniyor)

**Sorun:**
- İki farklı Ilan modeli var: `app/Models/Ilan.php` ve `app/Modules/Emlak/Models/Ilan.php`
- 20 dosya eski model'i kullanıyor
- Modeller farklı namespace ve yapıda

**Çözüm Seçenekleri:**

#### Seçenek A: Eski Model'i Koru (ÖNERİLEN)
- ✅ Risk düşük
- ✅ 20 dosyada değişiklik yapmaya gerek yok
- ⚠️ İki model birlikte kalır
- 📝 Not: Sadece dokümantasyon güncelle

#### Seçenek B: Tümü Yeni Model'e Geçir
- ❌ Risk yüksek (20 dosya değişecek)
- ❌ Test gereksinimi yüksek
- ⏱️ Süre: 4-6 saat
- 📝 Her dosyayı tek tek test et

**Öneri:** Şu an için Seçenek A'ya devam et. Değişiklik riskli.

---

### 2. 🔥 Kategori Field Standardizasyonu

**Durum:** ⚠️ Orta Risk

**Sorun:**
Model'de hem yeni hem legacy field'lar var:
```php
✅ 'ana_kategori_id' (yeni)
✅ 'alt_kategori_id' (yeni)
✅ 'yayin_tipi_id' (yeni)
⚠️ 'yayinlama_tipi' (legacy - deprecated)
```

**Çözüm:**
```sql
-- 1. Legacy field'ları kaldır (migration)
ALTER TABLE ilanlar DROP COLUMN kategori_id IF EXISTS;
ALTER TABLE ilanlar DROP COLUMN parent_kategori_id IF EXISTS;
ALTER TABLE ilanlar DROP COLUMN yayinlama_tipi;

-- 2. Model'den legacy field'ları kaldır
-- app/Models/Ilan.php -> fillable array'den sil

-- 3. Seed verileri güncelle
```

**Süre:** 2 saat  
**Risk:** Orta

---

### 3. ⚠️ Yazlık Detay Tablosu

**Durum:** ⚠️ Orta Risk

**Sorun:**
Yazlık özel alanlar ana `ilanlar` tablosunda karışıyor:
```php
// Şu an ana tabloda:
'havuz', 'havuz_var', 'sezon_baslangic', 'sezon_bitis',
'min_konaklama', 'gunluk_fiyat', 'temizlik_ucreti', etc.
```

**Çözüm:**
```sql
CREATE TABLE yazlik_details (
    id BIGINT PRIMARY KEY,
    ilan_id BIGINT UNIQUE,
    havuz BOOLEAN,
    havuz_turu VARCHAR(50),
    sezon_baslangic DATE,
    sezon_bitis DATE,
    min_konaklama INT,
    -- ... diğer yazlık özel alanları
    FOREIGN KEY (ilan_id) REFERENCES ilanlar(id)
);
```

**Süre:** 3-4 saat  
**Risk:** Orta (veri taşıma gerekli)

---

### 4. 📅 Takvim Entegrasyon API Endpoint'leri

**Durum:** 🆕 Yeni Görev

**Yapılacaklar:**
```php
// Controller oluştur
php artisan make:controller Admin/CalendarSyncController

// API Endpoint'leri:
GET  /api/admin/calendars/{ilan}/syncs
POST /api/admin/calendars/{ilan}/manual-sync
GET  /api/admin/doluluk/{ilan}/calendar
POST /api/admin/doluluk/{ilan}/block
```

**Süre:** 4 saat

---

### 5. 📊 Yazlık Doluluk Raporlama Sistemi

**Durum:** 🆕 Yeni Görev

**Yapılacaklar:**
- Doluluk oranı hesaplama
- Aylık/Sezonluk raporlar
- Rezervasyon istatistikleri
- Gelir analizi

**Süre:** 3-4 saat

---

## 🎯 BUGÜN YAPILACAKLAR (Öncelik)

1. ✅ Takvim entegrasyon sistemi (TAMAMLANDI)
2. ⏭️ Model duplikasyonu analizi (YAPILDI - Seçenek A seçildi)
3. ⏳ Kategori field standardizasyonu migration (BAŞLANACAK)
4. ⏸️ Yazlık detay tablosu (İleri tarih)

---

## 📝 NOTLAR

### Model Duplikasyonu Hakkında
- Eski model 20 dosya tarafından kullanılıyor
- Değişiklik riskli ve zaman alıcı
- Şu an için iki model birlikte çalışıyor
- İleride tek model'e geçiş için migration planı yapılabilir

### Kategori Standardizasyonu
- Legacy field'lar deprecated olarak işaretlendi
- Geçiş için migration hazırlanmalı
- Seed veriler güncellenmeli

### Yazlık Detay Tablosu
- Normal ilan vs Yazlık ayrımı net değil
- Ayrı tablo ile yapı netleşir
- Veri taşıma script'i gerekecek

---

**Sonraki Adım:** Kategori field standardizasyonu migration'ını oluştur
