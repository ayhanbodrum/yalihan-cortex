# İlan Açıklaması Üretimi - Context7 AI Prompt

**Version:** 1.0.0
**Category:** aciklama-olustur
**Type:** genel
**Priority:** high
**Last Updated:** 2025-01-27

---

## 🎯 **Görev**

Mevcut ilan verilerine dayanarak detaylı, müşteri odaklı ve SEO uyumlu bir ilan açıklaması üret.

---

## 📥 **Giriş Parametreleri**

### **Zorunlu Parametreler:**
- **ilan_id:** integer - İlan ID'si
- **kategori:** string - İlan kategorisi (konut, arsa, isyeri, turistik)
- **konum:** string - Lokasyon bilgisi (il, ilçe, mahalle)
- **fiyat:** decimal - İlan fiyatı
- **para_birimi:** string - Para birimi (TRY, USD, EUR)

### **Opsiyonel Parametreler:**
- **oda_sayisi:** string - Oda sayısı (1+1, 2+1, 3+1, vb.)
- **metrekare:** integer - Metrekare bilgisi
- **ozellikler:** array - Özellikler listesi
- **mevcut_aciklama:** string - Mevcut açıklama (varsa)
- **bina_yasi:** integer - Bina yaşı
- **kat_bilgisi:** string - Kat bilgisi

---

## 📤 **Çıktı Formatı**

### **JSON Format:**
```json
{
  "success": true,
  "data": {
    "result": "string",
    "metadata": {
      "word_count": "number",
      "paragraph_count": "number",
      "seo_score": "number",
      "readability_score": "number",
      "confidence_score": "number"
    }
  },
  "performance": {
    "response_time": "number",
    "accuracy": "number",
    "user_satisfaction": "number"
  }
}
```

---

## ⚙️ **İşlem Kuralları**

### **Zorunlu Kurallar:**
- [ ] Açıklama 200-400 kelime arasında olmalı
- [ ] 3-5 paragraf halinde düzenlenmeli
- [ ] Türkçe dilbilgisi kurallarına uygun olmalı
- [ ] Müşteri odaklı ve çekici ton kullanmalı
- [ ] SEO uyumlu anahtar kelimeler içermeli

### **Önerilen Kurallar:**
- [ ] Konum avantajları vurgulanmalı
- [ ] Özellikler detaylı açıklanmalı
- [ ] Çevre bilgileri dahil edilmeli
- [ ] Emlak sektörü standartlarına uygun olmalı

---

## 🔍 **Kalite Kontrol**

### **Otomatik Kontroller:**
- [ ] Kelime sayısı kontrolü (200-400)
- [ ] Paragraf sayısı kontrolü (3-5)
- [ ] SEO skoru hesaplama
- [ ] Okunabilirlik skoru
- [ ] Performans metrikleri

### **Manuel Kontroller:**
- [ ] İçerik kalitesi
- [ ] Müşteri çekiciliği
- [ ] SEO uygunluğu
- [ ] Profesyonellik
- [ ] Bilgi doğruluğu

---

## 📊 **Performans Metrikleri**

### **Teknik Metrikler:**
- **Yanıt Süresi:** < 3 saniye
- **Doğruluk Oranı:** > %90
- **Başarı Oranı:** > %95

### **Kalite Metrikleri:**
- **Kullanıcı Memnuniyeti:** > 4.5/5
- **İçerik Kalitesi:** > 4.0/5
- **SEO Uygunluğu:** > 4.0/5
- **Okunabilirlik:** > 4.0/5

### **İş Metrikleri:**
- **Dönüşüm Oranı:** > %20
- **Kullanım Sıklığı:** > 80/gün
- **Hata Oranı:** < %5

---

## 💡 **Örnek Kullanım**

### **Giriş:**
```json
{
  "ilan_id": 123,
  "kategori": "konut",
  "konum": "Kadıköy, İstanbul",
  "fiyat": 2500000,
  "para_birimi": "TRY",
  "oda_sayisi": "3+1",
  "metrekare": 120,
  "ozellikler": ["asansör", "otopark", "deniz_manzarasi", "güvenlik"],
  "bina_yasi": 5,
  "kat_bilgisi": "3. kat"
}
```

### **Çıktı:**
```json
{
  "success": true,
  "data": {
    "result": "Kadıköy'ün en prestijli bölgelerinden birinde yer alan bu 3+1 daire, deniz manzarası ve modern yaşam standartları sunuyor. 120 m² kullanım alanına sahip daire, ferah salon, 3 yatak odası, mutfak ve banyo ile günlük yaşamın tüm ihtiyaçlarını karşılıyor.\n\nDaire, 5 yıllık yeni binanın 3. katında yer alıyor ve asansör, otopark, 7/24 güvenlik gibi modern konfor unsurlarına sahip. Deniz manzarası ile günün her saati huzurlu bir atmosfer sunan daire, iş merkezlerine yakın konumu ile de dikkat çekiyor.\n\nKadıköy'ün canlı sosyal yaşamı, alışveriş merkezleri, restoranlar ve kafeler ile çevrili bu bölge, hem şehir merkezine yakınlığı hem de deniz kenarındaki konumu ile ideal bir yaşam alanı sunuyor.",
    "metadata": {
      "word_count": 185,
      "paragraph_count": 3,
      "seo_score": 0.88,
      "readability_score": 0.85,
      "confidence_score": 0.92
    }
  },
  "performance": {
    "response_time": 2500,
    "accuracy": 0.90,
    "user_satisfaction": 4.6
  }
}
```

---

## ⚠️ **Hata Yönetimi**

### **Hata Kodları:**
- **400:** Geçersiz parametreler
- **422:** Doğrulama hatası
- **500:** Sunucu hatası
- **503:** Servis kullanılamıyor

### **Hata Mesajları:**
```json
{
  "success": false,
  "error": {
    "code": 400,
    "message": "Geçersiz parametre",
    "details": "ilan_id parametresi gerekli"
  }
}
```

---

## 🎨 **Stil Rehberi**

### **Dil Kuralları:**
- Türkçe dilbilgisi kurallarına uygun
- Profesyonel emlak dili
- SEO dostu anahtar kelimeler
- Müşteri odaklı ve çekici ton

### **Format Kuralları:**
- 3-5 paragraf halinde düzenleme
- Her paragraf 40-80 kelime arası
- Alt başlık kullanımı (gerekirse)
- Noktalama işaretleri doğru kullanım

### **İçerik Kuralları:**
- Konum avantajları öncelikli
- Özellikler detaylı açıklanmalı
- Çevre bilgileri dahil edilmeli
- Müşteri faydaları vurgulanmalı

---

## 🔄 **Versiyonlama**

### **Version 1.0.0 (2025-01-27):**
- İlk sürüm
- Temel açıklama üretimi
- SEO optimizasyonu
- Okunabilirlik metrikleri

---

**Not:** Bu prompt Context7 hafızasından veritabanı şemasını otomatik olarak okuyacak ve ilan tablosu yapısını anlayacaktır.
