# 🧪 Wizard Form Test Senaryosu - Arsa Ekleme

**Tarih:** 2025-12-03  
**Mod:** Arsa Ekleme  
**Yöntem:** Adım Adım Test

---

## 📋 TEST ADIMLARI

### ✅ ADIM 1: TEMEL BİLGİLER

#### 1.1 Kategori Seçimi
- [ ] Ana Kategori dropdown açılıyor mu?
- [ ] Sadece seviye=0 kategoriler görünüyor mu?
- [ ] Ana kategori seçildiğinde Alt Kategori yükleniyor mu?
- [ ] Alt kategori seçildiğinde Yayın Tipi yükleniyor mu?
- [ ] Cascade dropdown'lar doğru çalışıyor mu?

**Test Verileri:**
- Ana Kategori: "Arsa" (veya arsa içeren kategori)
- Alt Kategori: "Arsa" (otomatik yüklenecek)
- Yayın Tipi: "Satılık" (otomatik yüklenecek)

#### 1.2 Başlık
- [ ] Başlık input'u çalışıyor mu?
- [ ] AI başlık üretimi butonu çalışıyor mu?
- [ ] AI başlık üretimi başarılı mı? (fallback dahil)
- [ ] Alternatif başlıklar gösteriliyor mu?

#### 1.3 Fiyat ve Para Birimi
- [ ] Fiyat input'u formatlanıyor mu? (5.000.000)
- [ ] Yazıyla gösterim çalışıyor mu?
- [ ] Para birimi değiştiğinde yazıyla gösterim güncelleniyor mu?
- [ ] Form submit'te raw değer gönderiliyor mu?

#### 1.4 Lokasyon
- [ ] İl dropdown çalışıyor mu?
- [ ] İl seçildiğinde İlçe yükleniyor mu?
- [ ] İlçe seçildiğinde Mahalle yükleniyor mu?
- [ ] Cascade dropdown'lar doğru çalışıyor mu?

**Test Verileri:**
- İl: Muğla (veya test için uygun il)
- İlçe: Bodrum (veya test için uygun ilçe)
- Mahalle: (otomatik yüklenecek)

#### 1.5 Adres
- [ ] Adres textarea çalışıyor mu?
- [ ] Harita seçici butonu var mı? (TODO olarak işaretli)

#### 1.6 Validation
- [ ] Zorunlu alanlar kontrol ediliyor mu?
- [ ] Hata mesajları gösteriliyor mu?
- [ ] "İleri" butonu validation'dan sonra çalışıyor mu?

---

### ✅ ADIM 2: DETAYLAR (ARSA İÇİN)

#### 2.1 Kategori Kontrolü
- [ ] Arsa seçildiğinde TKGM widget görünüyor mu?
- [ ] Konut seçildiğinde TKGM widget gizleniyor mu?
- [ ] Kategori değiştiğinde alanlar güncelleniyor mu?

#### 2.2 TKGM Widget
- [ ] Ada No input'u çalışıyor mu?
- [ ] Parsel No input'u çalışıyor mu?
- [ ] "TKGM'den Otomatik Doldur" butonu aktif mi?
- [ ] TKGM sorgulama çalışıyor mu?
- [ ] Loading state gösteriliyor mu?
- [ ] TKGM sonuçları gösteriliyor mu?
- [ ] Form alanları otomatik dolduruluyor mu?

**Test Verileri:**
- Ada No: 1234 (veya gerçek ada no)
- Parsel No: 5 (veya gerçek parsel no)
- İl: Muğla
- İlçe: Bodrum

#### 2.3 TKGM Sonuçları
- [ ] Alan (m²) gösteriliyor mu?
- [ ] İmar Durumu gösteriliyor mu?
- [ ] KAKS gösteriliyor mu?
- [ ] TAKS gösteriliyor mu?
- [ ] Diğer TKGM bilgileri gösteriliyor mu?

#### 2.4 Validation
- [ ] Ada No zorunlu mu? (Arsa için)
- [ ] Parsel No zorunlu mu? (Arsa için)
- [ ] Hata mesajları gösteriliyor mu?

---

### ✅ ADIM 3: EK BİLGİLER

#### 3.1 Açıklama
- [ ] Açıklama textarea çalışıyor mu?
- [ ] AI açıklama üretimi butonu çalışıyor mu?
- [ ] AI açıklama üretimi başarılı mı?

#### 3.2 İlan Sahibi
- [ ] İlan sahibi dropdown çalışıyor mu?
- [ ] Live search çalışıyor mu?

#### 3.3 Durum
- [ ] Durum dropdown çalışıyor mu?
- [ ] Varsayılan değer doğru mu?

#### 3.4 Validation
- [ ] Açıklama zorunlu mu?
- [ ] İlan sahibi zorunlu mu?
- [ ] Durum zorunlu mu?

---

### ✅ FORM SUBMIT

#### 4.1 Submit Öncesi
- [ ] Tüm adımlar tamamlandı mı?
- [ ] Validation geçti mi?
- [ ] Fiyat raw değere çevrildi mi?

#### 4.2 Submit İşlemi
- [ ] Form submit ediliyor mu?
- [ ] Loading state gösteriliyor mu?
- [ ] Backend'e doğru veriler gidiyor mu?

#### 4.3 Backend İşlemi
- [ ] İlan oluşturuluyor mu?
- [ ] Price text kaydediliyor mu?
- [ ] TKGM verileri kaydediliyor mu?
- [ ] Kategori ilişkileri doğru mu?

#### 4.4 Sonuç
- [ ] Başarı mesajı gösteriliyor mu?
- [ ] Redirect çalışıyor mu?
- [ ] Oluşturulan ilan görüntülenebiliyor mu?

---

## 🐛 TESPİT EDİLEN SORUNLAR

### Sorun 1: [Başlık]
**Adım:** [Hangi adım]
**Açıklama:** [Sorun açıklaması]
**Çözüm:** [Çözüm]

---

## ✅ TEST SONUÇLARI

### Adım 1: Temel Bilgiler
- Durum: ⏳ Test ediliyor
- Sorunlar: []
- Notlar: []

### Adım 2: Detaylar (TKGM)
- Durum: ⏳ Test ediliyor
- Sorunlar: []
- Notlar: []

### Adım 3: Ek Bilgiler
- Durum: ⏳ Test ediliyor
- Sorunlar: []
- Notlar: []

### Form Submit
- Durum: ⏳ Test ediliyor
- Sorunlar: []
- Notlar: []

---

**Son Güncelleme:** 2025-12-03  
**Test Durumu:** 🟡 Devam Ediyor

