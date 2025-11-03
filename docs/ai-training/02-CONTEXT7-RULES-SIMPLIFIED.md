# 🎯 Context7 Kuralları - AI için Basitleştirilmiş

**AnythingLLM Training Module 2**  
**Version:** 1.0.0

---

## ⚠️ CONTEXT7 NEDİR?

Context7, **Yalıhan Emlak sisteminde %100 uyulması zorunlu kod ve veri standardıdır.**

AI olarak, **her yanıtta Context7 kurallarına uymalısın.**

---

## ✅ ZORUNLU ALAN ADLARI

### **Database Field Naming:**

```yaml
# ✅ DOĞRU
status: "status" veya "active" (boolean)
il: "il" (İl tablosu için)
il_id: "il_id" (İl ID foreign key)
ilce_id: "ilce_id" (İlçe ID)
mahalle_id: "mahalle_id" (Mahalle ID)
para_birimi: "para_birimi" (TRY, USD, EUR, GBP)
fiyat: "fiyat" (numeric)
baslik: "baslik" (string)
aciklama: "aciklama" (text)

# ❌ YASAK (ASLA KULLANMA)
durum: YASAK → status kullan
is_active: YASAK → status kullan
aktif: YASAK → active kullan
sehir: YASAK → il kullan
sehir_id: YASAK → il_id kullan
region_id: YASAK → kaldırıldı
city: YASAK → il kullan
state: YASAK → status kullan
```

---

## 🗺️ LOKASYON KURALLARI

### **Model İlişkileri:**

```php
// ✅ DOĞRU
$ilan->il          // İl ilişkisi
$ilan->ilce        // İlçe ilişkisi
$ilan->mahalle     // Mahalle ilişkisi

// ❌ YASAK
$ilan->sehir       // KULLANMA
$ilan->bolge       // KULLANMA
$ilan->region      // KULLANMA
```

### **Lokasyon Metni Oluşturma:**

```php
// ✅ DOĞRU Format
"{il}, {ilce}, {mahalle}"
Örnek: "Muğla, Bodrum, Yalıkavak"

// ❌ YANLIŞ
"{sehir}, {bolge}"
"{city}, {region}"
```

---

## 💰 FİYAT ve PARA BİRİMİ

### **Para Birimi Standartları:**

```yaml
# ✅ DOĞRU Kullanım
TRY: "₺" sembolü
USD: "$" sembolü
EUR: "€" sembolü
GBP: "£" sembolü

# Formatla
fiyat: 2500000
para_birimi: "TRY"
formatted: "2.500.000 ₺"

# ❌ YASAK
currency: KULLANMA → para_birimi
price: KULLANMA → fiyat
amount: KULLANMA → fiyat
TL: KULLANMA → TRY
```

### **Fiyat Gösterimi:**
```
Türkçe Format: "2.500.000 ₺"
İngilizce Format: "2,500,000 TRY"
Kısa Format: "2.5M ₺"
Yazı ile: "İki Milyon Beş Yüz Bin Türk Lirası"
```

---

## 👥 KİŞİ ve CRM KURALLARI

### **Kişi Alan Adları:**

```yaml
# ✅ DOĞRU
ad: "ad" (first name)
soyad: "soyad" (last name)
tam_ad: Accessor (ad + soyad birleşimi)
telefon: "telefon"
email: "email"
musteri_tipi: "musteri_tipi"
danisman_id: "danisman_id"

# ❌ YASAK
ad_soyad: KULLANMA → tam_ad accessor
full_name: KULLANMA → name
musteri_ad_soyad: KULLANMA → musteri_tam_ad
```

### **Tam Ad Gösterimi:**

```php
// ✅ DOĞRU (Accessor kullan)
$kisi->tam_ad  // "Ahmet Yılmaz"

// ✅ DOĞRU (Eloquent)
CONCAT(ad, ' ', soyad) as tam_ad

// ❌ YASAK
$kisi->ad_soyad
$kisi->full_name
```

---

## 🏷️ KATEGORİ SİSTEMİ

### **3 Seviyeli Kategori Hiyerarşisi:**

```
Ana Kategori (Konut, Arsa, İşyeri, Turistik)
  ↓
Alt Kategori (Villa, Daire, Arsa, Yazlık)
  ↓
Yayın Tipi (Satılık, Kiralık, Günlük Kiralık)
```

### **Field Adları:**

```yaml
# ✅ DOĞRU
ana_kategori_id: Ana kategori ID
alt_kategori_id: Alt kategori ID
yayin_tipi_id: Yayın tipi ID

# ❌ YASAK
category_id: KULLANMA → alt_kategori_id
main_category: KULLANMA → ana_kategori_id
publication_type: KULLANMA → yayin_tipi_id
```

---

## 🎨 NEO DESIGN SYSTEM

### **CSS Class Prefix:**

```yaml
# ✅ DOĞRU (Neo Design System)
neo-btn
neo-card
neo-input
neo-form-group
neo-table

# ❌ YASAK (Bootstrap Legacy)
btn-primary
card-body
form-control
table-striped
```

### **Component Kullanımı:**

```blade
✅ DOĞRU:
<x-neo.button variant="primary" size="md">Kaydet</x-neo.button>
<x-admin.neo-toast />
<x-admin.neo-skeleton type="table" rows="5" />

❌ YASAK:
<button class="btn btn-primary">Kaydet</button>
<div class="alert alert-success">Başarılı</div>
```

---

## 🔄 AI YANIT FORMATI

### **Zorunlu JSON Yapısı:**

```json
{
  "success": true,
  "data": {
    // Ana veri
  },
  "metadata": {
    "model": "gemma2:2b",
    "response_time": 2500,
    "confidence_score": 0.92
  },
  "context7_compliant": true
}
```

### **Hata Yanıtı:**

```json
{
  "success": false,
  "error": "Hata mesajı",
  "fallback": "Alternatif öneri",
  "context7_compliant": true
}
```

---

## 🎯 AI GÖREV ÖNCELİKLERİ

### **Yüksek Öncelik:**
1. Context7 kurallarına %100 uyum
2. Türkçe gramer doğruluğu
3. SEO optimizasyonu
4. Hız (<3 saniye)

### **Orta Öncelik:**
5. Yaratıcılık ve çeşitlilik
6. Detaylı açıklamalar
7. Çoklu varyant üretimi

### **Düşük Öncelik:**
8. Emoji kullanımı (YASAK, sadece icon'lar)
9. Aşırı yaratıcı başlıklar (SEO öncelikli)

---

## 🚫 YASAKLAR (Critical)

### **Asla Yapma:**

❌ **Database alanlarında Türkçe isim kullan**
```
durum, aktif, sehir, bolge → YASAK
```

❌ **Otomatik kayıt yap**
```
AI sadece öneri üretir, kayıt insan onayı gerektirir
```

❌ **Kişisel veri işle**
```
Telefon, email, adres → Sadece maskelenmiş gösterilir
```

❌ **Bootstrap class kullan**
```
btn-primary, card-body → YASAK
neo-btn, neo-card → ZORUNLU
```

❌ **Emoji kullan** (başlık/açıklamada)
```
🏠 🌊 ⭐ → YASAK (UI'de icon kullan)
```

---

## ✅ BEST PRACTICES

### **İyi AI Yanıtı:**

```json
{
  "success": true,
  "variants": [
    "Yalıkavak Deniz Manzaralı Satılık Villa - 5+2 Havuzlu",
    "Bodrum Yalıkavak'ta Satılık Lüks Villa - Özel Havuz",
    "Yalıkavak Premium Lokasyonda Satılık Villa"
  ],
  "metadata": {
    "tone": "seo",
    "avg_length": 67,
    "seo_score": 88,
    "context7_compliant": true
  }
}
```

### **Kötü AI Yanıtı:**

```json
{
  "variants": [
    "🏠 Süper Villa!! KAÇIRMA 🌊",  // ❌ Emoji yasak
    "şehirde villa",  // ❌ Küçük harf, eksik bilgi
    "Villa satılık durum aktif"  // ❌ Anlamsız
  ],
  "context7_compliant": false  // ❌ Uyumsuz
}
```

---

## 🎯 HIZLI REFERANS

### **AI Soru Cevap:**

**S: Başlıkta fiyat gösterilmeli mi?**
C: Ton'a göre. SEO/Kurumsal/Hızlı Satış: EVET, Lüks: HAYIR

**S: Emoji kullanabilir miyim?**
C: HAYIR. Sadece UI component'lerinde icon.

**S: Türkçe field adı kullanabilir miyim?**
C: HAYIR. Database field'ları İngilizce (status, active).

**S: Kaç kelime açıklama?**
C: 200-250 kelime, 3 paragraf.

**S: Cache kullanmalı mıyım?**
C: EVET. 1 saat TTL.

**S: Fallback ne zaman devreye girer?**
C: Ollama timeout (30s) veya error durumunda.

---

**🎯 ÖZET:** Context7 = İngilizce field adları + Neo Design + Türkçe içerik + Cache + Fallback

