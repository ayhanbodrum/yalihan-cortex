# Danışman Performans Raporu - Context7 AI Prompt

**Version:** 1.0.0
**Category:** analiz
**Type:** genel
**Priority:** medium
**Last Updated:** 2025-01-27

---

## 🎯 **Görev**

Danışman performans verilerini analiz ederek detaylı performans raporu ve gelişim önerileri üret.

---

## 📥 **Giriş Parametreleri**

### **Zorunlu Parametreler:**
- **danisman_id:** integer - Danışman ID'si
- **analiz_periyodu:** string - Analiz periyodu (haftalik, aylik, yillik)
- **baslangic_tarihi:** date - Başlangıç tarihi
- **bitis_tarihi:** date - Bitiş tarihi

### **Opsiyonel Parametreler:**
- **detay_seviyesi:** string - Detay seviyesi (basit, orta, detayli)
- **karsilastirma_periyodu:** string - Karşılaştırma periyodu
- **ozel_metrikler:** array - Özel metrikler

---

## 📤 **Çıktı Formatı**

### **JSON Format:**
```json
{
  "success": true,
  "analysis": {
    "danisman_bilgileri": {
      "id": "integer",
      "ad_soyad": "string",
      "email": "string",
      "deneyim_yili": "integer",
      "uzmanlik_alanlari": ["array"],
      "aktif_durum": "boolean"
    },
    "performans_metrikleri": {
      "toplam_ilan": "integer",
      "aktif_ilan": "integer",
      "satilan_ilan": "integer",
      "toplam_satis_tutari": "decimal",
      "musteri_sayisi": "integer",
      "talep_sayisi": "integer",
      "ortalama_yanit_suresi": "decimal"
    },
    "performans_skoru": {
      "genel_skor": "decimal",
      "satis_performansi": "decimal",
      "musteri_memnuniyeti": "decimal",
      "operasyonel_verimlilik": "decimal",
      "ai_skoru": "decimal"
    },
    "detayli_analiz": {
      "guc_alanlari": ["array"],
      "gelisim_alanlari": ["array"],
      "trend_analizi": "string",
      "karsilastirma_analizi": "string"
    },
    "gelisim_onerileri": [
      {
        "alan": "string",
        "oneri": "string",
        "oncelik": "string",
        "tahmini_etki": "string",
        "uygulama_suresi": "string"
      }
    ],
    "hedefler": {
      "kisa_vadeli": ["array"],
      "uzun_vadeli": ["array"]
    },
    "analiz_tarihi": "timestamp"
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
- [ ] Performans skoru 0-100 arasında hesaplanmalı
- [ ] Gelişim önerileri somut ve uygulanabilir olmalı
- [ ] Hedefler ölçülebilir ve gerçekçi olmalı
- [ ] Analiz tarihi dahil edilmeli
- [ ] JSON formatı geçerli olmalı

### **Önerilen Kurallar:**
- [ ] Öneriler öncelik sırasına göre düzenlenmeli
- [ ] Kısa ve uzun vadeli hedefler belirlenmeli
- [ ] Tahmini etki değerlendirmesi yapılmalı
- [ ] Motivasyonel ve destekleyici ton kullanılmalı

---

## 🔍 **Performans Analizi Kriterleri**

### **1. Genel Performans Skoru (%100)**
- **Mükemmel (90-100%):** Tüm metrikler yüksek
- **Yüksek (75-89%):** Çoğu metrik yüksek
- **Orta (60-74%):** Karışık performans
- **Düşük (40-59%):** Çoğu metrik düşük
- **Kritik (0-39%):** Tüm metrikler düşük

### **2. Satış Performansı (%40)**
- **Başarı Oranı:** Hedef %80+
- **Toplam Satış:** Sektör ortalaması üzeri
- **Satış Tutarı:** Büyüme trendi
- **Müşteri Dönüşümü:** Yüksek oran

### **3. Müşteri İlişkileri (%30)**
- **Memnuniyet Oranı:** Hedef %85+
- **Yanıt Süresi:** Hızlı yanıt
- **Müşteri Sadakati:** Tekrar iş yapma
- **Referans Oranı:** Yüksek referans

### **4. Operasyonel Verimlilik (%30)**
- **Aktif İlan Sayısı:** Optimal seviye
- **Talep Tamamlama:** Yüksek oran
- **Online Durum:** Aktif çalışma
- **Son Aktivite:** Güncel aktivite

---

## 🔍 **Gelişim Önerileri Kategorileri**

### **📈 Satış ve Pazarlama:**
- Müşteri segmentasyonu
- Hedef pazar analizi
- Satış teknikleri
- Pazarlama stratejileri

### **👥 Müşteri İlişkileri:**
- İletişim becerileri
- Müşteri hizmetleri
- Sorun çözme
- İlişki yönetimi

### **⚡ Operasyonel Verimlilik:**
- Zaman yönetimi
- Teknoloji kullanımı
- Süreç optimizasyonu
- Kaynak yönetimi

### **🎓 Profesyonel Gelişim:**
- Eğitim ihtiyaçları
- Sertifikasyon
- Networking
- Sektör bilgisi

---

## 🔍 **Kalite Kontrol**

### **Otomatik Kontroller:**
- [ ] Performans skoru hesaplama doğruluğu
- [ ] JSON formatı geçerliliği
- [ ] Tüm metriklerin dahil edilmesi
- [ ] Analiz tarihi kontrolü

### **Manuel Kontroller:**
- [ ] Gelişim önerileri uygulanabilirlik
- [ ] Hedefler gerçekçilik
- [ ] Analiz kalitesi
- [ ] Motivasyonel ton

---

## 📊 **Performans Metrikleri**

### **Teknik Metrikler:**
- **Yanıt Süresi:** < 5 saniye
- **Doğruluk Oranı:** > %85
- **Başarı Oranı:** > %90

### **Kalite Metrikleri:**
- **Kullanıcı Memnuniyeti:** > 4.0/5
- **Analiz Kalitesi:** > 4.0/5
- **Öneri Uygulanabilirliği:** > 4.0/5

### **İş Metrikleri:**
- **Performans Artışı:** > %15
- **Kullanım Sıklığı:** > 20/gün
- **Hata Oranı:** < %5

---

## 💡 **Örnek Kullanım**

### **Giriş:**
```json
{
  "danisman_id": 123,
  "analiz_periyodu": "aylik",
  "baslangic_tarihi": "2025-01-01",
  "bitis_tarihi": "2025-01-31",
  "detay_seviyesi": "detayli"
}
```

### **Çıktı:**
```json
{
  "success": true,
  "analysis": {
    "danisman_bilgileri": {
      "id": 123,
      "ad_soyad": "Ahmet Yılmaz",
      "email": "ahmet@yalihanemlak.com",
      "deneyim_yili": 5,
      "uzmanlik_alanlari": ["Konut", "Arsa"],
      "aktif_durum": true
    },
    "performans_skoru": {
      "genel_skor": 0.85,
      "satis_performansi": 0.90,
      "musteri_memnuniyeti": 0.80,
      "operasyonel_verimlilik": 0.85
    },
    "gelisim_onerileri": [
      {
        "alan": "Müşteri İlişkileri",
        "oneri": "Müşteri takip sıklığını artırın",
        "oncelik": "Yüksek",
        "tahmini_etki": "Müşteri memnuniyeti %15 artabilir",
        "uygulama_suresi": "2 hafta"
      }
    ]
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
    "details": "danisman_id parametresi gerekli"
  }
}
```

---

## 🎨 **Stil Rehberi**

### **Dil Kuralları:**
- Türkçe dilbilgisi kurallarına uygun
- Profesyonel ve motivasyonel ton
- Yapıcı ve destekleyici ifadeler
- Ölçülebilir ve somut öneriler

### **Format Kuralları:**
- JSON formatında yanıt
- Yapılandırılmış veri
- Performans metrikleri dahil
- Hata yönetimi kapsamlı

### **İçerik Kuralları:**
- Öneriler somut ve uygulanabilir
- Hedefler ölçülebilir
- Analiz objektif ve adil
- Motivasyonel ve destekleyici

---

## 🔄 **Versiyonlama**

### **Version 1.0.0 (2025-01-27):**
- İlk sürüm
- Temel performans analizi
- Gelişim önerileri
- Hedef belirleme

---

**Not:** Bu prompt Context7 hafızasından veritabanı şemasını otomatik olarak okuyacak ve danışman/ilan/müşteri tablolarının yapısını anlayacaktır.
