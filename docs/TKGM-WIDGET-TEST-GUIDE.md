# TKGM Widget Test Rehberi

**Context7 Standard:** C7-TKGM-WIDGET-TEST-2025-12-03  
**Version:** 1.0.0  
**Status:** ✅ Test Edilebilir

---

## 🎯 Test Senaryosu

### Adım 1: Wizard Form'a Git

1. Admin paneline giriş yap
2. `/admin/ilanlar/create-wizard` sayfasına git
3. **Step 1**'de:
   - Ana Kategori: **Arsa** seç
   - Alt Kategori: **Arsa** seç
   - İl: **Muğla** seç
   - İlçe: **Bodrum** seç
   - Mahalle: Herhangi bir mahalle seç
4. **"İleri"** butonuna tıkla → **Step 2**'ye geç

### Adım 2: TKGM Widget'ı Test Et

1. **Step 2**'de TKGM widget'ı görünür olmalı
2. **Ada No** alanına: `1234` gir
3. **Parsel No** alanına: `5` gir
4. **"🔍 TKGM'den Otomatik Doldur"** butonuna tıkla

### Adım 3: Beklenen Sonuçlar

✅ **Başarılı Durum:**
- Buton "TKGM Sorgulanıyor..." yazısı gösterir
- Loading animasyonu görünür
- TKGM verileri gelir ve gösterilir:
  - Alan (m²)
  - İmar Durumu
  - KAKS
  - TAKS
  - Gabari
  - Koordinatlar
- **"✅ Formu Otomatik Doldur"** butonu görünür

❌ **Hata Durumları:**
- **404 Hatası:** Endpoint bulunamadı → Route kontrolü gerekli
- **422 Hatası:** Validasyon hatası → İl/İlçe/Ada/Parsel kontrolü
- **500 Hatası:** TKGM servisi hatası → Backend log kontrolü

---

## 🔍 Debug Adımları

### 1. Browser Console Kontrolü

```javascript
// Console'da kontrol et:
console.log(window.APIConfig);
console.log(window.APIConfig?.properties?.tkgmLookup);
```

**Beklenen:**
```javascript
// ✅ DOĞRU
window.APIConfig.properties.tkgmLookup
// Sonuç: '/api/properties/tkgm-lookup'
```

### 2. Network Tab Kontrolü

1. Browser DevTools → Network tab
2. **"TKGM'den Otomatik Doldur"** butonuna tıkla
3. `tkgm-lookup` isteğini kontrol et:
   - **URL:** `/api/properties/tkgm-lookup`
   - **Method:** `POST`
   - **Status:** `200 OK` (başarılı) veya `404` (hata)
   - **Request Body:**
     ```json
     {
       "il": "Muğla",
       "ilce": "Bodrum",
       "ada": "1234",
       "parsel": "5"
     }
     ```
   - **Response:**
     ```json
     {
       "success": true,
       "message": "Parsel bilgileri başarıyla alındı",
       "data": {
         "ada_no": "1234",
         "parsel_no": "5",
         "alan_m2": 1500.50,
         "imar_statusu": "İmarlı",
         "kaks": 0.30,
         "taks": 0.25,
         "gabari": 7.50,
         "center_lat": 37.0361,
         "center_lng": 27.4305
       }
     }
     ```

### 3. Route Kontrolü

```bash
# Route'un tanımlı olduğunu kontrol et
php artisan route:list | grep tkgm-lookup
```

**Beklenen:**
```
POST  api/properties/tkgm-lookup  api.properties.tkgm-lookup.web
```

### 4. CSRF Token Kontrolü

```javascript
// Console'da kontrol et:
console.log(document.querySelector('meta[name="csrf-token"]')?.content);
```

**Beklenen:** CSRF token string'i (örn: `abc123...`)

---

## 🐛 Yaygın Hatalar ve Çözümleri

### Hata 1: `404 Not Found`

**Neden:**
- Route tanımlı değil
- Endpoint yanlış
- Middleware sorunu

**Çözüm:**
1. `routes/api.php` dosyasında route'u kontrol et
2. `window.APIConfig.properties.tkgmLookup` değerini kontrol et
3. Route middleware'lerini kontrol et (`web`, `auth`)

### Hata 2: `419 CSRF Token Mismatch`

**Neden:**
- CSRF token eksik veya yanlış
- Session süresi dolmuş

**Çözüm:**
1. Sayfayı yenile
2. `meta[name="csrf-token"]` tag'ini kontrol et
3. Login olup tekrar dene

### Hata 3: `422 Validation Error`

**Neden:**
- İl/İlçe/Ada/Parsel eksik veya yanlış format

**Çözüm:**
1. İl ve İlçe seçildiğinden emin ol
2. Ada ve Parsel numaralarını kontrol et
3. Form alanlarının dolu olduğunu kontrol et

### Hata 4: `500 Internal Server Error`

**Neden:**
- TKGM servisi hatası
- Database bağlantı sorunu
- WikiMapia servisi hatası

**Çözüm:**
1. Backend log'ları kontrol et: `storage/logs/laravel.log`
2. TKGM servisinin çalıştığını kontrol et
3. Database bağlantısını kontrol et

---

## ✅ Test Checklist

- [ ] Wizard form açılıyor
- [ ] Step 1'de Arsa seçiliyor
- [ ] Step 2'ye geçiliyor
- [ ] TKGM widget görünüyor
- [ ] Ada ve Parsel alanları doldurulabiliyor
- [ ] İl ve İlçe seçili
- [ ] "TKGM'den Otomatik Doldur" butonu aktif
- [ ] Butona tıklanınca loading gösteriliyor
- [ ] API isteği gönderiliyor (Network tab)
- [ ] Response başarılı geliyor (200 OK)
- [ ] TKGM verileri gösteriliyor
- [ ] "Formu Otomatik Doldur" butonu görünüyor
- [ ] Form doldurulduğunda alanlar güncelleniyor

---

## 📊 Test Sonuçları

**Test Tarihi:** 2025-12-03  
**Test Eden:** AI Assistant  
**Durum:** ⏳ Test Edilecek

### Test Adımları:

1. ✅ Widget dosyası kontrol edildi
2. ✅ Endpoint doğru (`/api/properties/tkgm-lookup`)
3. ✅ Route tanımlı (`api.properties.tkgm-lookup.web`)
4. ✅ Error handling iyileştirildi
5. ⏳ Gerçek test yapılacak

---

## 🔧 İyileştirmeler

### Yapılan İyileştirmeler:

1. ✅ **404 Error Handling:** HTTP status kodları kontrol ediliyor
2. ✅ **422 Validation Error:** Detaylı hata mesajları gösteriliyor
3. ✅ **500 Server Error:** Kullanıcı dostu hata mesajları
4. ✅ **Console Logging:** Debug için console.error eklendi
5. ✅ **API Config:** Merkezi config sistemi kullanılıyor

---

**Last Updated:** 2025-12-03  
**Maintainer:** Context7 System

