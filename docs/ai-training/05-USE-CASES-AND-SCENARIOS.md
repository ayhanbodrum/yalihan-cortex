# 🎯 AI Kullanım Senaryoları ve Örnekler

**AnythingLLM Training Module 5**  
**Version:** 1.0.0

---

## 📋 GERÇEK KULLANIM SENARYOLARI

### **Senaryo 1: Hızlı İlan Oluşturma**

**Durum:**  
Danışman Ahmet, yeni bir villa ilanı ekliyor. Zamandan tasarruf için AI yardımı istiyor.

**Flow:**

```
1. Ahmet /stable-create sayfasını açar
2. Temel bilgileri girer:
   - Kategori: Villa
   - Yayın Tipi: Satılık
   - Lokasyon: Yalıkavak
   - Fiyat: 3.5M ₺
   - Özellikler: Havuz, deniz manzarası
3. "Tümünü Üret" butonuna tıklar
4. AI 5 saniyede üretir:
   ✅ 3 başlık önerisi
   ✅ Profesyonel açıklama
   ✅ Lokasyon analizi (Skor: 92/100)
5. Ahmet en iyi başlığı seçer
6. Açıklamayı hafif düzenler
7. İlanı kaydeder
```

**Zaman Tasarrufu:** 15 dakika → 3 dakika (%80 azalma)

---

### **Senaryo 2: Fiyat Danışmanlığı**

**Durum:**  
Müşteri Ayşe, villasını satmak istiyor ama fiyat konusunda kararsız.

**Dialog:**

```
Ayşe: "3 milyon TL fiyat uygun mu?"

AI Analiz:
  - Lokasyon: Yalıkavak
  - Alan: 250 m²
  - m² başı: 12,000 ₺
  - Bölge ortalaması: 13,500 ₺/m²

AI Yanıt:
💰 Fiyat Analizi

Girilen: 3.000.000 ₺
m² Başı: 12.000 ₺

Öneriler:
1. Pazarlık Payı: 2.7M ₺ (Hızlı satış)
2. Piyasa: 3.375M ₺ (Bölge ortalaması) ✅ ÖNERİLEN
3. Premium: 3.75M ₺ (Özel özellikler)

Değerlendirme: Bölge ortalamasının %11 altında.
Fiyat artırımı önerilir.
```

---

### **Senaryo 3: Müşteri Eşleştirme**

**Durum:**  
Yeni bir alıcı müşteri sisteme eklendi. Hangi ilanlar uygun?

**Input:**

```yaml
Müşteri:
    Bütçe: 2-3M ₺
    Lokasyon: Bodrum (tüm bölgeler)
    Kategori: Villa veya Daire
    Özellikler: Deniz manzarası, 3+ yatak odası
```

**AI Process:**

```
1. Aktif ilanları filtrele (status = 'Aktif')
2. Fiyat aralığında olanları bul (2M-3M ₺)
3. Lokasyon uyumunu kontrol et (Bodrum)
4. Özellikleri eşleştir (deniz manzarası, 3+ oda)
5. Eşleşme skoru hesapla (0-100)
6. Top 5 sonuç döndür
```

**AI Yanıt:**

```
🏠 Önerilen İlanlar

1. YE-SAT-YALKVK-VİLLA-001234
   Eşleşme: %95 ⭐⭐⭐⭐⭐
   - Fiyat: 2.8M ₺ ✅ Bütçe içinde
   - Lokasyon: Yalıkavak ✅
   - Özellikler: 4 oda, deniz manzarası, havuz ✅✅

2. YE-SAT-GÜMSLK-DAİRE-005678
   Eşleşme: %88 ⭐⭐⭐⭐
   - Fiyat: 2.5M ₺ ✅ Bütçe içinde
   - Lokasyon: Gümüşlük ✅
   - Özellikler: 3+1, deniz manzarası ✅

3. YE-SAT-TURGUT-VİLLA-003456
   Eşleşme: %85 ⭐⭐⭐⭐
   - Fiyat: 3.2M ₺ ⚠️ Bütçe üstünde ama görülmeli
   - Lokasyon: Turgutreis ✅
   - Özellikler: 5 oda, deniz manzarası, bahçe ✅✅
```

---

### **Senaryo 4: Çoklu Dil İçerik**

**Durum:**  
Yabancı alıcılar için İngilizce, Almanca, Rusça açıklama gerekiyor.

**Input:**

```yaml
Türkçe Açıklama: "Yalıkavak'ta denize sıfır lüks villa..."
Hedef Diller: EN, DE, RU
```

**AI Process:**

```
1. TR açıklamayı analiz et
2. Her dil için kültürel uyarlama yap
   - EN: Profesyonel, uluslararası ton
   - DE: Detaylı, teknik bilgi odaklı
   - RU: Prestij ve yatırım vurgusu
3. SEO anahtar kelimeleri her dile çevir
4. Format koru (paragraf yapısı)
```

**AI Yanıt:**

```json
{
    "tr": "Yalıkavak'ta denize sıfır konumda...",
    "en": "Luxury villa by the sea in Yalıkavak, Bodrum...",
    "de": "Luxusvilla direkt am Meer in Yalıkavak, Bodrum...",
    "ru": "Роскошная вилла у моря в Ялыкаваке, Бодрум..."
}
```

---

### **Senaryo 5: Portal Optimizasyonu**

**Durum:**  
Aynı ilan 6 farklı portala yayınlanacak. Her portal için optimize başlık gerekiyor.

**Portal Kuralları:**

```yaml
Sahibinden:
    Başlık: Max 50 karakter
    Stil: Kısa, direkt

Hepsiemlak:
    Başlık: 60-70 karakter
    Stil: Profesyonel

Emlakjet:
    Başlık: 70-80 karakter
    Stil: Detaylı

Zingat:
    Başlık: 60 karakter
    Stil: Modern, genç kitle

Hürriyet Emlak:
    Başlık: 65 karakter
    Stil: Kurumsal
```

**AI Çıktı:**

```json
{
    "sahibinden": "Yalıkavak Satılık Villa 3.5M ₺",
    "hepsiemlak": "Bodrum Yalıkavak'ta Deniz Manzaralı Satılık Villa",
    "emlakjet": "Yalıkavak Premium Lokasyonda Özel Havuzlu Satılık Lüks Villa - 3.5M ₺",
    "zingat": "Yalıkavak'ta Satılık Modern Villa - Özel Havuz + Deniz",
    "hurriyetemlak": "Bodrum Yalıkavak Deniz Manzaralı Satılık Villa"
}
```

---

## 🧮 ARSA ÖZEL SENARYOLAR

### **Senaryo 6: KAKS/TAKS Hesaplama**

**Input:**

```yaml
Alan: 1000 m²
KAKS: 1.5
TAKS: 0.35
```

**AI Hesaplama:**

```
İnşaat Alanı = Alan × KAKS
             = 1000 × 1.5
             = 1500 m²

Taban Alanı = Alan × TAKS
            = 1000 × 0.35
            = 350 m²

Maksimum Kat = KAKS / TAKS
             = 1.5 / 0.35
             = ~4 kat

AI Öneri:
"Bu arsa üzerine 4 katlı, toplam 1500 m² inşaat alanına sahip
modern villa projesi geliştirilebilir. Her kat 350 m² taban alanı
ile ferah yaşam alanları sunabilir."
```

---

## 🏖️ YAZLIK ÖZEL SENARYOLAR

### **Senaryo 7: Sezonluk Fiyatlandırma**

**Input:**

```yaml
Kategori: Yazlık Villa
Haftalık Fiyat: 50.000 ₺ (Yaz)
Kış: 15.000 ₺
Minimum: 7 gün
```

**AI Analizi:**

```
💰 Sezon Bazlı Fiyat Analizi

Yüksek Sezon (Haziran-Eylül): 50.000 ₺/hafta
Ara Sezon (Mayıs, Ekim): 30.000 ₺/hafta ÖNERİLİR
Düşük Sezon (Kasım-Nisan): 15.000 ₺/hafta

Yıllık Gelir Tahmini:
  Yaz (16 hafta): 800.000 ₺
  Ara (8 hafta): 240.000 ₺
  Kış (8 hafta): 120.000 ₺
  Doluluk %60: 696.000 ₺/yıl

ROI: %5.8 (iyi)

Öneri: Ara sezon fiyatı ekleyerek doluluk artırılabilir.
```

---

## 👥 CRM ÖZEL SENARYOLAR

### **Senaryo 8: Müşteri Segmentasyonu**

**Input:**

```yaml
Kişi:
    Bütçe: 5M ₺
    Meslek: İş İnsanı
    Lokasyon Tercihi: Bodrum
    Aktivite: Son 6 ayda 3 ilan görüntüledi
```

**AI Segmentasyon:**

```
📊 Müşteri Profil Analizi

Segment: Premium Yatırımcı
CRM Skoru: 88/100

Özellikler:
- Yüksek bütçe (5M ₺) → Premium segment
- İş insanı → Yatırım odaklı
- Bodrum tercihi → Turizm bölgesi
- Aktif arama → Sıcak müşteri

Önerilen Yaklaşım:
1. Villa ve ticari projeler göster
2. Yatırım getirisi vurgula
3. Kira garantili seçenekler sun
4. Bodrum dışı alternatifleri de değerlendir

Eşleşen İlanlar: 7 adet (Top 3 göster)
```

---

## 🎨 ÖZEL TON ÖRNEKLERİ

### **Örnek 1: SEO Tone - Standart Daire**

**Input:** 3+1 daire, Kadıköy, 2.5M ₺

**AI Çıktı:**

```
"Kadıköy Satılık 3+1 Daire - Deniz Manzaralı 120 m² Lüks Konut

Kadıköy'ün en gözde lokasyonlarından birinde, deniz manzaralı 3+1 daire.
120 m² kullanım alanı, modern mutfak, geniş balkon. Site içinde otopark,
asansör, güvenlik. Toplu taşıma ve alışveriş merkezlerine yürüme mesafesi.

2.500.000 ₺ fiyat ile bölge ortalamasına uygun. Görüşmeler başladı,
hemen arayın!"
```

### **Örnek 2: Lüks Tone - Premium Villa**

**Input:** 5+2 villa, Yalıkavak, 15M ₺

**AI Çıktı:**

```
"Yalıkavak'ın En Prestijli Noktasında Eşsiz Deniz Manzaralı Villa

Bodrum Yalıkavak'ın prime lokasyonunda, denizle iç içe konumlanmış bu
muhteşem villa, modern lüksün ve konforun tüm unsurlarını bir araya getiriyor.
500 m² yaşam alanı, özel infinity havuz, 1200 m²조경 bahçe.

Her detayında zarafet ve kaliteyi hissedebileceğiniz bu özel konut,
ayrıcalıklı yaşam arayanlar için benzersiz bir fırsat sunuyor.
Detaylar ve özel görüşme için lütfen bizimle iletişime geçin."

Not: Fiyat talep üzerine paylaşılır (lüks tone gereği)
```

### **Örnek 3: Hızlı Satış Tone - Acil İlan**

**Input:** 2+1 daire, Gümbet, 1.8M ₺, %15 indirim

**AI Çıktı:**

```
"FIRSATTAN KAÇIRMAYIN! Gümbet'te 2+1 Daire - %15 İndirimli!

Sadece bu hafta! Gümbet'in en popüler bölgesinde, denize 200 metre
mesafede 2+1 daire. Normal fiyat 2.1M ₺ iken, sadece bu hafta
1.800.000 ₺!

Sitede havuz, güvenlik, otopark mevcut. İlk gören alır!
HEMEN ARAYIN: 0533 209 03 02"
```

---

## 🗺️ LOKASYON ANALİZİ ÖRNEKLERİ

### **Örnek 1: Yalıkavak Analizi**

**Input:**

```yaml
Lokasyon: Muğla, Bodrum, Yalıkavak
Nearby POI:
    - Okul: 350m
    - Hastane: 2.5km
    - Market: 180m
    - Deniz: 500m
```

**AI Analiz:**

```
📍 Yalıkavak Lokasyon Analizi

Skor: 92/100 ⭐⭐⭐⭐⭐
Harf: A
Potansiyel: Yüksek

Değerlendirme:
✅ Premium tatil bölgesi
✅ Tüm sosyal tesislere yakın (okul 350m, market 180m)
✅ Denize 500m (yürüme mesafesi)
✅ Yüksek turist talebi
✅ Yıllık değer artışı: %15-20

Detaylar:
- Merkeze yakınlık: 23/25 puan
- Sosyal tesisler: 19/20 puan
- Ulaşım: 18/20 puan
- Altyapı: 20/20 puan (tam)
- Gelişim potansiyeli: 12/15 puan

Sonuç: Yatırım için ideal lokasyon.
```

### **Örnek 2: İç Bölge Arsa**

**Input:**

```yaml
Lokasyon: Muğla, Köyceğiz (iç bölge)
Kategori: Arsa
```

**AI Analiz:**

```
📍 Köyceğiz Lokasyon Analizi

Skor: 68/100 ⭐⭐⭐
Harf: C
Potansiyel: Orta

Değerlendirme:
✅ Doğal güzellikler (göl, orman)
✅ Sakin yaşam
⚠️ Turistik talep düşük
⚠️ Merkeze 90km uzaklık

Öneriler:
- Yatırım süresi: 5-10 yıl (uzun vadeli)
- Hedef kitle: Doğa seven, emekli, yabancı alıcılar
- Fiyat: Bölge ortalamasına uygun
```

---

## 💼 TİCARİ EMLAK ÖRNEKLERİ

### **Örnek: Dükkan İlanı**

**Input:**

```yaml
Kategori: Dükkan
Lokasyon: Bodrum Merkez
Alan: 80 m²
Ciro: 150.000 ₺/ay (varsa)
Kiralık: 25.000 ₺/ay
```

**AI Açıklama:**

```
"Bodrum Merkez'de Devren Kiralık İşlek Dükkan - 80 m²

Bodrum'un en işlek caddelerinde, yüksek insan trafiğine sahip bölgede
80 m² dükkan. Mevcut cafe işletmesi ile birlikte devren kiralık.

İşletme Bilgileri:
- Aylık ciro: 150.000 ₺
- Net kar: ~30.000 ₺
- Müşteri profili: Turist + yerel
- Sezon: Haziran-Eylül yoğun

Kira: 25.000 ₺/ay + KDV
Devir bedeli: Görüşülür
ROI: %40+ (yüksek getiri)

Café lisansı, tüm ekipmanlar ve müşteri portföyü dahil."
```

---

## 🎯 EDGE CASE'LER

### **Case 1: Eksik Bilgi**

**Input:**

```yaml
Kategori: Villa
Lokasyon: Yalıkavak
# Fiyat YOK
# Metrekare YOK
```

**AI Yaklaşımı:**

```
⚠️ Eksik Bilgi Tespit Edildi

Başlık önerisi oluşturulabilir ama:
- Fiyat bilgisi olmadığı için "Fiyat: Talep Üzerine" eklenebilir
- Metrekare olmadığı için alan vurgusu yapılmaz

Önerilen Başlık:
"Yalıkavak'ta Satılık Villa - Özel Konum, Fiyat Talep Üzerine"

Öneri: Fiyat ve metrekare ekleyerek daha etkili başlık alabilirsiniz.
```

### **Case 2: Çok Yüksek Fiyat**

**Input:**

```yaml
Fiyat: 50.000.000 ₺ (çok yüksek)
Kategori: Villa
```

**AI Uyarısı:**

```
⚠️ Fiyat Kontrolü

Girilen: 50.000.000 ₺
Bölge Ortalaması: 5.000.000 ₺
Fark: %900 yüksek ⚠️

Bu fiyat oldukça yüksek görünüyor. Kontrol edin:
- Sıfır eksik mi? (5M yerine 50M)
- Para birimi doğru mu?
- Gerçekten özel bir villa mı?

Onaylar mısınız?
```

---

## 🎨 A/B TEST ÖRNEKLERİ

### **Örnek: Başlık A/B Test**

**Input:** 3+1 daire, Beşiktaş, 4.5M ₺

**Version A (SEO):**

```
"Beşiktaş Satılık 3+1 Daire - Deniz Manzaralı 140 m² - 4.5M ₺"

Artılar: Tüm anahtar kelimeler, fiyat açık, detaylı
Eksi: Uzun, kalabalık
```

**Version B (Emotion):**

```
"Beşiktaş'ta Denize Nazır Lüks Daire - Özel Konum"

Artılar: Duygusal, prestijli, merak uyandırır
Eksi: Fiyat yok, bilgi az
```

**AI Önerisi:**

```
Test Hipotezi:
- Version A: Daha fazla tıklama (SEO)
- Version B: Daha kaliteli lead (Lüks segment)

Hedef kitleye göre seçin:
- Geniş kitle → Version A
- Premium alıcılar → Version B
```

---

## 📊 PERFORMANS İZLEME ÖRNEĞİ

### **İlan Performans Raporu:**

**Input:**

```yaml
İlan: YE-SAT-YALKVK-VİLLA-001234
Yayın: 30 gün
Görüntülenme: 450
Tıklama: 38
Lead: 5
```

**AI Analiz:**

```
📈 İlan Performans Raporu

Metrikler:
- Görüntülenme: 450 (günlük ort: 15)
- CTR: %8.4 (sektör ort: %5-7) ✅ İyi
- Conversion: %13.1 (sektör ort: %10) ✅ İyi
- Lead Kalitesi: 3 sıcak, 2 soğuk

Değerlendirme: İyi performans ✅

Öneriler:
✅ Mevcut stratejiyi koru
✅ Fotoğrafları yenile (30 gün geçti)
⚠️ Fiyat revizyonu değerlendirilsin (30 günde satılmadı)
```

---

## 🎯 YANLIŞ ve DOĞRU ÖRNEKLER

### **❌ Yanlış AI Yanıtı:**

```
{
  "başlık": "🏠 Süper Villa KAÇIRMA!! 🌊",  // Emoji yasak
  "açıklama": "çok güzel villa durum aktif",  // Küçük harf, anlamsız
  "fiyat_önerisi": "ucuza ver hemen satılsın"  // Profesyonel değil
}
```

### **✅ Doğru AI Yanıtı:**

```json
{
    "success": true,
    "variants": [
        "Yalıkavak Deniz Manzaralı Satılık Villa - 5+2 Havuzlu",
        "Bodrum Yalıkavak'ta Satılık Lüks Villa - 250 m²",
        "Yalıkavak Premium Lokasyonda Satılık Villa - 3.5M ₺"
    ],
    "metadata": {
        "tone": "seo",
        "avg_length": 67,
        "seo_score": 88,
        "context7_compliant": true
    }
}
```

---

**🎯 ÖZET:** Gerçek kullanım senaryolarını öğren, benzer durumlarda aynı yaklaşımı uygula.
