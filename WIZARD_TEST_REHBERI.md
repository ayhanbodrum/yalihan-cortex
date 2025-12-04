# 🧪 Wizard Form Test Rehberi - Arsa Ekleme

**Tarih:** 2025-12-03  
**Mod:** Arsa Ekleme  
**Test Yöntemi:** Adım Adım Manuel Test

---

## 🎯 TEST BAŞLANGICI

### Test URL:
```
http://127.0.0.1:8000/admin/ilanlar/create-wizard
```

### Test Senaryosu:
Arsa kategorisi seçilerek, tüm adımlar doldurulacak ve form submit edilecek.

---

## ✅ ADIM 1: TEMEL BİLGİLER TESTİ

### 1.1 Kategori Seçimi

**Test:**
1. ✅ Ana Kategori dropdown'ı aç
2. ✅ "Arsa" veya arsa içeren kategoriyi seç
3. ✅ Alt Kategori otomatik yüklendi mi? (Kontrol et)
4. ✅ Alt Kategori'den "Arsa" seç
5. ✅ Yayın Tipi otomatik yüklendi mi? (Kontrol et)
6. ✅ Yayın Tipi'nden "Satılık" seç

**Beklenen Sonuç:**
- Cascade dropdown'lar çalışmalı
- Her seçim sonrası bir sonraki dropdown aktif olmalı
- Loading state gösterilmeli

**Sorun Tespiti:**
- [ ] Alt kategori yüklenmiyor
- [ ] Yayın tipi yüklenmiyor
- [ ] Loading state yok
- [ ] Hata mesajı var: `_________________`

---

### 1.2 Başlık

**Test:**
1. ✅ Başlık input'una tıkla
2. ✅ "AI ile Başlık Üret" butonuna tıkla
3. ✅ AI başlık üretimi çalışıyor mu?
4. ✅ Alternatif başlıklar gösteriliyor mu?
5. ✅ Bir başlık seç veya manuel gir

**Beklenen Sonuç:**
- AI başlık üretimi çalışmalı (fallback dahil)
- Alternatif başlıklar gösterilmeli
- Başlık seçilebilmeli

**Sorun Tespiti:**
- [ ] AI başlık üretimi çalışmıyor
- [ ] 500 hatası alıyorum
- [ ] Alternatif başlıklar gösterilmiyor
- [ ] Hata mesajı var: `_________________`

---

### 1.3 Fiyat ve Para Birimi

**Test:**
1. ✅ Fiyat input'una `5000000` yaz
2. ✅ Otomatik formatlanıyor mu? (`5.000.000`)
3. ✅ Yazıyla gösterim görünüyor mu? ("Beş Milyon Türk Lirası")
4. ✅ Para birimini değiştir (USD)
5. ✅ Yazıyla gösterim güncellendi mi?

**Beklenen Sonuç:**
- Fiyat otomatik formatlanmalı
- Yazıyla gösterim real-time güncellenmeli
- Para birimi değiştiğinde yazıyla gösterim güncellenmeli

**Sorun Tespiti:**
- [ ] Formatlama çalışmıyor
- [ ] Yazıyla gösterim çalışmıyor
- [ ] API hatası var
- [ ] Hata mesajı var: `_________________`

---

### 1.4 Lokasyon

**Test:**
1. ✅ İl dropdown'ından bir il seç (örn: Muğla)
2. ✅ İlçe otomatik yüklendi mi?
3. ✅ İlçe seç (örn: Bodrum)
4. ✅ Mahalle otomatik yüklendi mi?
5. ✅ Mahalle seç
6. ✅ Adres textarea'sına adres yaz

**Beklenen Sonuç:**
- Cascade dropdown'lar çalışmalı
- Her seçim sonrası bir sonraki dropdown aktif olmalı
- Loading state gösterilmeli

**Sorun Tespiti:**
- [ ] İlçe yüklenmiyor
- [ ] Mahalle yüklenmiyor
- [ ] 404 hatası alıyorum
- [ ] Hata mesajı var: `_________________`

---

### 1.5 Validation ve İleri Butonu

**Test:**
1. ✅ Zorunlu alanları boş bırak
2. ✅ "İleri" butonuna tıkla
3. ✅ Hata mesajları gösteriliyor mu?
4. ✅ Tüm zorunlu alanları doldur
5. ✅ "İleri" butonuna tekrar tıkla
6. ✅ Adım 2'ye geçildi mi?

**Beklenen Sonuç:**
- Validation çalışmalı
- Hata mesajları gösterilmeli
- Tüm alanlar doluysa Adım 2'ye geçilmeli

**Sorun Tespiti:**
- [ ] Validation çalışmıyor
- [ ] Hata mesajları gösterilmiyor
- [ ] Adım 2'ye geçilmiyor
- [ ] Hata mesajı var: `_________________`

---

## ✅ ADIM 2: DETAYLAR (TKGM) TESTİ

### 2.1 Kategori Kontrolü

**Test:**
1. ✅ Adım 2'de TKGM widget görünüyor mu?
2. ✅ Arsa kategorisi seçiliyse TKGM widget görünmeli

**Beklenen Sonuç:**
- Arsa seçildiyse TKGM widget görünmeli
- Konut seçildiyse TKGM widget gizlenmeli

**Sorun Tespiti:**
- [ ] TKGM widget görünmüyor
- [ ] Yanlış kategori kontrolü
- [ ] Hata mesajı var: `_________________`

---

### 2.2 TKGM Widget - Ada/Parsel

**Test:**
1. ✅ Ada No input'una bir değer gir (örn: 1234)
2. ✅ Parsel No input'una bir değer gir (örn: 5)
3. ✅ "TKGM'den Otomatik Doldur" butonu aktif oldu mu?
4. ✅ İl ve İlçe seçili mi? (Adım 1'den geliyor)

**Beklenen Sonuç:**
- Ada ve Parsel girildiğinde buton aktif olmalı
- İl ve İlçe seçili olmalı

**Sorun Tespiti:**
- [ ] Buton aktif olmuyor
- [ ] İl/İlçe bilgisi eksik
- [ ] Hata mesajı var: `_________________`

---

### 2.3 TKGM Sorgulama

**Test:**
1. ✅ "TKGM'den Otomatik Doldur" butonuna tıkla
2. ✅ Loading state gösteriliyor mu?
3. ✅ TKGM sorgulama başarılı mı?
4. ✅ TKGM sonuçları gösteriliyor mu?
5. ✅ "Formu Otomatik Doldur" butonuna tıkla
6. ✅ Form alanları dolduruldu mu?

**Beklenen Sonuç:**
- TKGM sorgulama çalışmalı
- Sonuçlar gösterilmeli
- Form otomatik doldurulmalı

**Sorun Tespiti:**
- [ ] TKGM sorgulama çalışmıyor
- [ ] 500 hatası alıyorum
- [ ] Sonuçlar gösterilmiyor
- [ ] Form doldurulmuyor
- [ ] Hata mesajı var: `_________________`

---

### 2.4 TKGM Sonuçları Kontrolü

**Test:**
1. ✅ Alan (m²) gösteriliyor mu?
2. ✅ İmar Durumu gösteriliyor mu?
3. ✅ KAKS gösteriliyor mu?
4. ✅ TAKS gösteriliyor mu?
5. ✅ Gabari gösteriliyor mu?
6. ✅ Koordinatlar gösteriliyor mu?

**Beklenen Sonuç:**
- Tüm TKGM bilgileri gösterilmeli
- Null check'ler çalışmalı (N/A gösterilmeli)

**Sorun Tespiti:**
- [ ] Bilgiler gösterilmiyor
- [ ] TypeError hatası var
- [ ] Hata mesajı var: `_________________`

---

### 2.5 Validation ve İleri Butonu

**Test:**
1. ✅ Ada No ve Parsel No zorunlu mu?
2. ✅ Boş bırakıp "İleri" butonuna tıkla
3. ✅ Hata mesajları gösteriliyor mu?
4. ✅ Tüm alanları doldur
5. ✅ "İleri" butonuna tıkla
6. ✅ Adım 3'e geçildi mi?

**Beklenen Sonuç:**
- Validation çalışmalı
- Hata mesajları gösterilmeli
- Tüm alanlar doluysa Adım 3'e geçilmeli

**Sorun Tespiti:**
- [ ] Validation çalışmıyor
- [ ] Hata mesajları gösterilmiyor
- [ ] Adım 3'e geçilmiyor
- [ ] Hata mesajı var: `_________________`

---

## ✅ ADIM 3: EK BİLGİLER TESTİ

### 3.1 Açıklama

**Test:**
1. ✅ Açıklama textarea'sına tıkla
2. ✅ "AI ile Açıklama Üret" butonuna tıkla
3. ✅ AI açıklama üretimi çalışıyor mu?
4. ✅ Açıklama metni gösteriliyor mu?

**Beklenen Sonuç:**
- AI açıklama üretimi çalışmalı
- Açıklama metni gösterilmeli

**Sorun Tespiti:**
- [ ] AI açıklama üretimi çalışmıyor
- [ ] 500 hatası alıyorum
- [ ] Hata mesajı var: `_________________`

---

### 3.2 İlan Sahibi

**Test:**
1. ✅ İlan sahibi dropdown'ına tıkla
2. ✅ Live search çalışıyor mu?
3. ✅ Bir kişi seç

**Beklenen Sonuç:**
- Live search çalışmalı
- Kişi seçilebilmeli

**Sorun Tespiti:**
- [ ] Live search çalışmıyor
- [ ] Hata mesajı var: `_________________`

---

### 3.3 Durum

**Test:**
1. ✅ Durum dropdown'ından bir durum seç
2. ✅ Varsayılan değer doğru mu?

**Beklenen Sonuç:**
- Durum seçilebilmeli
- Varsayılan değer mantıklı olmalı

**Sorun Tespiti:**
- [ ] Durum seçilemiyor
- [ ] Hata mesajı var: `_________________`

---

### 3.4 Validation ve Submit

**Test:**
1. ✅ Zorunlu alanları boş bırak
2. ✅ "İlanı Oluştur" butonuna tıkla
3. ✅ Hata mesajları gösteriliyor mu?
4. ✅ Tüm alanları doldur
5. ✅ "İlanı Oluştur" butonuna tıkla
6. ✅ Form submit ediliyor mu?

**Beklenen Sonuç:**
- Validation çalışmalı
- Hata mesajları gösterilmeli
- Tüm alanlar doluysa form submit edilmeli

**Sorun Tespiti:**
- [ ] Validation çalışmıyor
- [ ] Hata mesajları gösterilmiyor
- [ ] Form submit edilmiyor
- [ ] Hata mesajı var: `_________________`

---

## ✅ FORM SUBMIT TESTİ

### 4.1 Submit Öncesi Kontrol

**Test:**
1. ✅ Fiyat raw değere çevrildi mi? (5.000.000 → 5000000)
2. ✅ Tüm zorunlu alanlar dolu mu?
3. ✅ Validation geçti mi?

**Beklenen Sonuç:**
- Fiyat raw değere çevrilmeli
- Tüm zorunlu alanlar dolu olmalı
- Validation geçmeli

**Sorun Tespiti:**
- [ ] Fiyat formatlı gönderiliyor
- [ ] Zorunlu alanlar eksik
- [ ] Validation geçmiyor
- [ ] Hata mesajı var: `_________________`

---

### 4.2 Submit İşlemi

**Test:**
1. ✅ "İlanı Oluştur" butonuna tıkla
2. ✅ Loading state gösteriliyor mu?
3. ✅ Backend'e istek gidiyor mu?
4. ✅ Network tab'da request görünüyor mu?

**Beklenen Sonuç:**
- Loading state gösterilmeli
- Backend'e istek gitmeli
- Request başarılı olmalı

**Sorun Tespiti:**
- [ ] Loading state yok
- [ ] Request gitmiyor
- [ ] 500 hatası alıyorum
- [ ] Hata mesajı var: `_________________`

---

### 4.3 Backend İşlemi

**Test:**
1. ✅ İlan oluşturuldu mu?
2. ✅ Price text kaydedildi mi?
3. ✅ TKGM verileri kaydedildi mi?
4. ✅ Kategori ilişkileri doğru mu?

**Beklenen Sonuç:**
- İlan oluşturulmalı
- Price text kaydedilmeli
- TKGM verileri kaydedilmeli
- Kategori ilişkileri doğru olmalı

**Sorun Tespiti:**
- [ ] İlan oluşturulmadı
- [ ] Price text kaydedilmedi
- [ ] TKGM verileri kaydedilmedi
- [ ] Kategori ilişkileri yanlış
- [ ] Hata mesajı var: `_________________`

---

### 4.4 Sonuç

**Test:**
1. ✅ Başarı mesajı gösteriliyor mu?
2. ✅ Redirect çalışıyor mu?
3. ✅ Oluşturulan ilan görüntülenebiliyor mu?
4. ✅ İlan detay sayfasında tüm bilgiler doğru mu?

**Beklenen Sonuç:**
- Başarı mesajı gösterilmeli
- Redirect çalışmalı
- İlan görüntülenebilmeli
- Tüm bilgiler doğru olmalı

**Sorun Tespiti:**
- [ ] Başarı mesajı gösterilmiyor
- [ ] Redirect çalışmıyor
- [ ] İlan görüntülenemiyor
- [ ] Bilgiler yanlış
- [ ] Hata mesajı var: `_________________`

---

## 📊 TEST SONUÇ ÖZETİ

### ✅ Başarılı Testler:
- [ ] Adım 1: Temel Bilgiler
- [ ] Adım 2: Detaylar (TKGM)
- [ ] Adım 3: Ek Bilgiler
- [ ] Form Submit

### ❌ Başarısız Testler:
- [ ] Adım 1: _______________
- [ ] Adım 2: _______________
- [ ] Adım 3: _______________
- [ ] Form Submit: _______________

### 🐛 Tespit Edilen Sorunlar:

1. **Sorun:** _______________
   **Adım:** _______________
   **Açıklama:** _______________
   **Öncelik:** 🔴 Yüksek / 🟡 Orta / 🟢 Düşük

2. **Sorun:** _______________
   **Adım:** _______________
   **Açıklama:** _______________
   **Öncelik:** 🔴 Yüksek / 🟡 Orta / 🟢 Düşük

---

## 🎯 SONRAKI ADIMLAR

1. Tespit edilen sorunları düzelt
2. Tekrar test et
3. Tüm testler başarılı olana kadar devam et

---

**Test Tarihi:** 2025-12-03  
**Test Eden:** _______________  
**Durum:** 🟡 Test Ediliyor

