# İlan Ekleme Test Rehberi

## Test Senaryosu: Bodrum Yalıkavak Tarla

### Test Verileri:
- **Lokasyon:** Bodrum, Yalıkavak
- **Kategori:** Arsa (Tarla)
- **Ada:** 702
- **Parsel:** 67
- **Fiyat:** 700.000 TL

---

## Adım Adım Test Süreci

### STEP 1: Temel Bilgiler

1. **Ana Kategori:** Arsa seç
2. **Alt Kategori:** Tarla/Arsa seç
3. **Yayın Tipi:** Satılık seç
4. **İl:** Muğla seç
5. **İlçe:** Bodrum seç
6. **Mahalle:** Yalıkavak seç (dropdown'dan)
7. **Başlık:** AI ile üret veya manuel gir
   - Örnek: "Bodrum Yalıkavak Satılık Tarla"
8. **Fiyat:** `700000` (formatlanmış: 700.000)
9. **Para Birimi:** TRY
10. **Adres:** Haritadan seç veya manuel gir

**✅ Kontrol:**
- Tüm zorunlu alanlar dolu mu?
- Fiyat formatı doğru mu? (700.000)
- Fiyat yazıyla gösteriliyor mu? ("Yedi Yüz Bin Türk Lirası")

**"İleri" butonuna tıkla → Step 2**

---

### STEP 2: Detaylar (Arsa)

1. **TKGM Widget:**
   - Ada No: `702`
   - Parsel No: `67`
   - **"TKGM'den Otomatik Doldur" butonuna tıkla**

2. **Beklenen TKGM Verileri:**
   - Alan: X m²
   - İmar Durumu: İmarlı/İmarsız
   - KAKS: X
   - TAKS: X
   - Gabari: X m
   - Koordinatlar: X, Y

3. **"Formu Otomatik Doldur" butonuna tıkla**

4. **Kontrol:**
   - ✅ Alan (m²) dolduruldu mu?
   - ✅ İmar Durumu seçildi mi?
   - ✅ KAKS dolduruldu mu?
   - ✅ TAKS dolduruldu mu?
   - ✅ Gabari dolduruldu mu?
   - ✅ Ada No dolduruldu mu? (702)
   - ✅ Parsel No dolduruldu mu? (67)
   - ✅ Harita güncellendi mi? (Koordinatlar gösteriliyor mu?)

5. **Altyapı:**
   - Elektrik (varsa işaretle)
   - Su (varsa işaretle)
   - Doğalgaz (varsa işaretle)

6. **Yola Cephe:**
   - Yola Cephe Var (varsa işaretle)

**"İleri" butonuna tıkla → Step 3**

---

### STEP 3: Ek Bilgiler

1. **Açıklama:** İlan açıklaması (opsiyonel)
2. **İlan Sahibi:** Seç veya yeni oluştur
3. **Danışman:** Otomatik (giriş yapan kullanıcı)
4. **Durum:** Taslak (varsayılan) veya Aktif

**"✅ Yayınla" butonuna tıkla**

---

## Beklenen Sonuçlar

### ✅ Başarılı Kayıt:
- İlan başarıyla oluşturuldu mesajı
- İlanlar listesine yönlendirme
- Yeni ilan listede görünüyor

### ❌ Olası Hatalar:

1. **Validation Hataları:**
   - "Baslik gereklidir" → Başlık girilmemiş
   - "Fiyat gereklidir" → Fiyat girilmemiş
   - "Ana kategori gereklidir" → Kategori seçilmemiş
   - "İlan sahibi gereklidir" → İlan sahibi seçilmemiş

2. **TKGM Hataları:**
   - "TKGM endpoint bulunamadı" → Route hatası (404)
   - "TKGM verisi bulunamadı" → Ada/Parsel yanlış veya TKGM'de yok

3. **Form Submit Hataları:**
   - "Bir hata oluştu" → Backend hatası (500)
   - Validation errors → Form alanları eksik/yanlış

---

## Debug Checklist

### Frontend Kontrolleri:
- [ ] Console'da JavaScript hatası var mı?
- [ ] Network tab'de API istekleri başarılı mı? (200 OK)
- [ ] FormData'da tüm alanlar gönderiliyor mu?
- [ ] CSRF token gönderiliyor mu?

### Backend Kontrolleri:
- [ ] Route tanımlı mı? (`/admin/ilanlar/store`)
- [ ] Validation rules doğru mu?
- [ ] Arsa alanları kaydediliyor mu? (ada_no, parsel_no, alan_m2, vb.)
- [ ] Database'de kayıt oluştu mu?

### Database Kontrolleri:
```sql
SELECT * FROM ilanlar WHERE ada_no = '702' AND parsel_no = '67' ORDER BY created_at DESC LIMIT 1;
```

---

## Test Sonuçları

### Başarılı Test:
- ✅ Tüm adımlar tamamlandı
- ✅ İlan başarıyla kaydedildi
- ✅ TKGM verileri doğru kaydedildi
- ✅ Harita koordinatları doğru

### Hata Varsa:
1. **Hata mesajını kaydet**
2. **Console log'larını kaydet**
3. **Network tab'deki failed request'leri kaydet**
4. **Backend log'larını kontrol et** (`storage/logs/laravel.log`)

---

## Sonraki Adımlar

1. İlan detay sayfasını kontrol et
2. TKGM verilerinin doğru göründüğünü kontrol et
3. Harita entegrasyonunu kontrol et
4. İlan listesinde göründüğünü kontrol et

