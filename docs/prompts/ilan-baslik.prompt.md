# İlan Başlığı Üretimi - Context7 AI Prompt

**Version:** 1.0.0
**Category:** baslik-olustur
**Type:** genel
**Priority:** high
**Last Updated:** 2025-01-27

---

## 🎯 **Görev**

Mevcut ilan verilerine dayanarak SEO uyumlu, çekici ve profesyonel bir ilan başlığı üret.

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
- **ozellikler:** array - Önemli özellikler (asansör, otopark, vb.)
- **mevcut_baslik:** string - Mevcut başlık (varsa)

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
      "character_count": "number",
      "seo_score": "number",
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
- [ ] Başlık maksimum 80 karakter olmalı
- [ ] SEO uyumlu anahtar kelimeler içermeli
- [ ] Türkçe dilbilgisi kurallarına uygun olmalı
- [ ] Müşteri çekici ve profesyonel ton kullanmalı
- [ ] Konum bilgisi mutlaka yer almalı

### **Önerilen Kurallar:**
- [ ] Fiyat bilgisi dahil edilmeli
- [ ] Önemli özellikler vurgulanmalı
- [ ] Emlak sektörü standartlarına uygun olmalı
- [ ] Mobil cihazlarda okunabilir olmalı

---

## 🔍 **Kalite Kontrol**

### **Otomatik Kontroller:**
- [ ] Karakter sayısı kontrolü (max 80)
- [ ] SEO skoru hesaplama
- [ ] Dilbilgisi kontrolü
- [ ] Performans metrikleri

### **Manuel Kontroller:**
- [ ] İçerik kalitesi
- [ ] Müşteri çekiciliği
- [ ] SEO uygunluğu
- [ ] Profesyonellik

---

## 📊 **Performans Metrikleri**

### **Teknik Metrikler:**
- **Yanıt Süresi:** < 2 saniye
- **Doğruluk Oranı:** > %90
- **Başarı Oranı:** > %95

### **Kalite Metrikleri:**
- **Kullanıcı Memnuniyeti:** > 4.5/5
- **İçerik Kalitesi:** > 4.0/5
- **SEO Uygunluğu:** > 4.0/5

### **İş Metrikleri:**
- **Dönüşüm Oranı:** > %15
- **Kullanım Sıklığı:** > 100/gün
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
  "ozellikler": ["asansör", "otopark", "deniz_manzarasi"]
}
```

### **Çıktı:**
```json
{
  "success": true,
  "data": {
    "result": "Kadıköy'de Deniz Manzaralı 3+1 Daire - 2.500.000 TL",
    "metadata": {
      "word_count": 8,
      "character_count": 52,
      "seo_score": 0.92,
      "confidence_score": 0.95
    }
  },
  "performance": {
    "response_time": 1200,
    "accuracy": 0.92,
    "user_satisfaction": 4.7
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
- Müşteri odaklı ton

### **Format Kuralları:**
- Konum öncelikli sıralama
- Fiyat bilgisi dahil
- Önemli özellikler vurgulanmalı
- Kısa ve öz ifadeler

### **SEO Kuralları:**
- Anahtar kelimeler doğal şekilde yerleştirilmeli
- Konum bilgisi mutlaka bulunmalı
- Emlak terimleri doğru kullanılmalı
- Arama motoru dostu yapı

---

## 🔄 **Versiyonlama**

### **Version 1.0.0 (2025-01-27):**
- İlk sürüm
- Temel başlık üretimi
- SEO optimizasyonu
- Performans metrikleri

---

**Not:** Bu prompt Context7 hafızasından veritabanı şemasını otomatik olarak okuyacak ve ilan tablosu yapısını anlayacaktır.
