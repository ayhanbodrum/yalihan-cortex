# ⚡ AI Asistan Hızlı Referans Kartı

**Yazdır ve Masanızda Tutun!**  
**Version:** 1.0.0

---

## 🤖 SİSTEM BİLGİLERİ

```
AI: Ollama gemma2:2b
Endpoint: http://51.75.64.121:11434
Dil: Türkçe ✅
Maliyet: $0 (Ücretsiz)
Hız: ~2-3 saniye
```

---

## ✅ CONTEXT7 KURALLAR (EZBER!)

### **DOĞRU Field Adları:**

```
✅ status (NOT durum, is_active, aktif)
✅ il_id (NOT sehir_id, region_id, city_id)
✅ il (NOT sehir, region, city)
✅ para_birimi (NOT currency)
✅ neo-* (NOT btn-*, card-*)
```

### **YASAK Field Adları:**

```
❌ durum, is_active, aktif
❌ sehir, sehir_id, region_id
❌ ad_soyad (use: tam_ad accessor)
❌ full_name (use: name)
```

---

## 🎨 TON PROFİLLERİ

```
1. SEO: Anahtar kelime yoğun, detaylı
   Örnek: "Yalıkavak Satılık Villa - 3.5M ₺"

2. Kurumsal: Profesyonel, yatırım odaklı
   Örnek: "Yalıkavak Yüksek Getirili Villa"

3. Hızlı Satış: Acil, heyecanlı, KAPSLOK
   Örnek: "FIRSATTAN! Yalıkavak Villa!"

4. Lüks: Prestijli, fiyat gösterilmez
   Örnek: "Yalıkavak'ın En Prestijli Villası"
```

---

## 📏 STANDARTLAR

### **Başlık:**

```
Uzunluk: 60-80 karakter
Format: {Lokasyon} {Özellik} {Kategori} {Fiyat}
SEO: >85/100
Emoji: ❌ YASAK
```

### **Açıklama:**

```
Kelime: 200-250
Paragraf: 3 adet
Ton: Profesyonel
Format: Lokasyon → Özellik → Yatırım
```

---

## 💰 PARA BİRİMLERİ

```
TRY: ₺
USD: $
EUR: €
GBP: £
```

---

## 🗺️ LOKASYON HİYERARŞİSİ

```
Türkiye
 └─ İl (Muğla)
     └─ İlçe (Bodrum)
         └─ Mahalle (Yalıkavak)
             └─ Site (Opsiyonel)
```

---

## 🎯 LOKASYON SKORLAMA

```
A (85-100): Mükemmel
B (70-84): İyi
C (50-69): Orta
D (0-49): Düşük

Kriterler:
- Merkeze yakınlık: 25p
- Sosyal tesis: 20p
- Ulaşım: 20p
- Altyapı: 20p
- Gelişim: 15p
```

---

## 📊 FİYAT SEVİYELERİ

```
1. Pazarlık (-10%): Hızlı satış
2. Piyasa (+5%): Ortalama
3. Premium (+15%): Özel özellikler
```

---

## 🏷️ REFERANS NO FORMAT

```
YE-{YAYIN}-{LOK}-{KAT}-{SIRA}

Örnek:
YE-SAT-YALKVK-VİLLA-001234
```

---

## 🌐 PORTAL'LAR

```
1. Sahibinden.com
2. Hepsiemlak
3. Emlakjet
4. Zingat
5. Hürriyet Emlak
6. Emlak365
```

---

## 🎯 API ENDPOINTS

```
POST /stable-create/ai-suggest
Actions:
  - title (Başlık)
  - description (Açıklama)
  - location (Lokasyon analizi)
  - price (Fiyat önerisi)
  - all (Hepsi)
```

---

## 🧪 HIZLI TEST SORULARI

```
1. "status yasak mı?" → Hayır, zorunlu
2. "Kaç ton var?" → 4: seo, kurumsal, hizli_satis, luks
3. "Kaç kelime?" → 200-250, 3 paragraf
4. "TRY sembolü?" → ₺
5. "Lokasyon?" → İl → İlçe → Mahalle
```

---

## ⚡ KISAYOLLAR

### **AnythingLLM:**

```
Chat: Cmd/Ctrl + K
New Message: Enter
Clear Chat: Trash icon
Settings: ⚙️ icon
```

### **Stable Create:**

```
Başlık Üret: 🤖 button
Açıklama Üret: 🤖 button
Tümünü Üret: 🚀 button
```

---

## 🔧 SORUN GİDERME

```
AI Yanıt Yok:
└─ Ollama çalışıyor mu?
   curl http://51.75.64.121:11434/api/tags

Yavaş (>5s):
└─ Chunk size 800'e düşür

Context7 İhlal:
└─ System prompt kontrol et
```

---

## 📞 YARDIM

```
Hızlı: QUICK-START.md
Detaylı: 07-EMBEDDING-GUIDE.md
Test: 08-TRAINING-CHECKLIST.md
Örnek: 10-REAL-WORLD-EXAMPLES.md
```

---

## 🎯 HEDEF METRİKLER

```
Response: <3s ✅
Success: >95% ✅
Context7: %100 ✅
Satisfaction: >4.5/5 ✅
```

---

**📌 BU KARTI YAZDIR ve MASA ÜSTÜNDE TUT!**

**Quick Reference:** ✅  
**Context7 Compliant:** ✅  
**Production Ready:** ✅
