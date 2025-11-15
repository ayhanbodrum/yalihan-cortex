# 📝 AI Prompt Şablonları

**AnythingLLM Training Module 4**  
**Version:** 1.0.0

---

## 🎯 BAŞLIK ÜRETİMİ PROMPT'LARI

### **Template 1: Genel Başlık**

```
Sen bir emlak uzmanısın. Aşağıdaki bilgilere göre 3 farklı ilan başlığı oluştur.

Kategori: {kategori}
Yayın Tipi: {yayin_tipi}
Lokasyon: {lokasyon}
Fiyat: {fiyat} {para_birimi}
Metrekare: {metrekare} m²
Özellik: {ozellik}

Kurallar:
- Her başlık 60-80 karakter
- Lokasyon mutlaka geçmeli
- SEO uyumlu anahtar kelimeler
- Sadece başlıkları yaz, numaralama yapma
- Emoji kullanma

Başlıklar:
```

### **Template 2: SEO Optimize Başlık**

```
SEO odaklı ilan başlığı oluştur.

{kategori} - {yayin_tipi}
Lokasyon: {il}, {ilce}, {mahalle}
Fiyat: {fiyat}
Alan: {metrekare} m²

Gereksinimleri:
- Lokasyon başta (SEO)
- Kategori ikinci sırada
- Fiyat sonda (opsiyonel)
- Anahtar kelime: "Satılık", "{kategori}", "{lokasyon}"
- 70 karakter maksimum

Başlık:
```

### **Template 3: Lüks Segment Başlık**

```
Lüks segment için prestijli başlık.

Kategori: {kategori}
Lokasyon: {lokasyon}
Özellikler: {ozellikler}

Kurallar:
- Fiyat gösterme (talep üzerine)
- "Exclusive", "Premium", "Lüks" gibi kelimeler
- Özel özellikleri vurgula
- Prestijli dil kullan

Başlık:
```

---

## 📄 AÇIKLAMA ÜRETİMİ PROMPT'LARI

### **Template 1: Standart Açıklama**

```
Profesyonel emlak açıklaması yaz.

Kategori: {kategori}
Yayın Tipi: {yayin_tipi}
Lokasyon: {il}, {ilce}, {mahalle}
Fiyat: {fiyat} {para_birimi}
Alan: {metrekare} m²
Oda: {oda_sayisi}
Özellikler: {ozellikler}

Kurallar:
- 200-250 kelime
- 3 paragraf
- Türkçe gramer kurallarına uygun
- SEO anahtar kelimeleri ekle
- Müşteri odaklı ton

Paragraf Yapısı:
1. Genel tanıtım + Lokasyon (60-80 kelime)
2. Teknik detaylar + Özellikler (80-100 kelime)
3. Çevre, ulaşım, yatırım (60-80 kelime)

Açıklama:
```

### **Template 2: Arsa Özel Açıklama**

```
Arsa ilanı için teknik açıklama.

Arsa Bilgileri:
- Alan: {alan_m2} m² ({donum} dönüm)
- İmar Durumu: {imar_durumu}
- KAKS: {kaks}
- TAKS: {taks}
- Ada/Parsel: {ada_no}/{parsel_no}
- Lokasyon: {lokasyon}

Vurgulanacak Konular:
- İmar durumu ve KAKS/TAKS değerleri
- İnşaat potansiyeli (KAKS × Alan)
- Yatırım getirisi
- Lokasyon avantajları
- Altyapı durumu

3 paragraf, 220 kelime.

Açıklama:
```

### **Template 3: Yazlık Özel Açıklama**

```
Yazlık villa için sezonluk kiralama açıklaması.

Villa Özellikleri:
- Yatak Odası: {oda_sayisi}
- Maksimum Kişi: {max_kisi}
- Havuz: {havuz}
- Denize Uzaklık: {denize_uzaklik} m
- Minimum Konaklama: {min_konaklama} gün
- Sezon: {sezon_baslangic} - {sezon_bitis}

Vurgu:
- Tatil deneyimi (aktiviteler, plaj)
- Konfor özellikleri
- Çevre (restaurant, market)
- Haftalık fiyat avantajı

Ton: Heyecan verici, tatil odaklı
220 kelime, 3 paragraf.

Açıklama:
```

---

## 🗺️ LOKASYON ANALİZİ PROMPT'LARI

### **Template 1: Genel Lokasyon Analizi**

```
Lokasyon analizi yap.

Lokasyon: {il}, {ilce}, {mahalle}
Koordinat: {latitude}, {longitude}
Yakındaki Yerler: {poi_listesi}

Değerlendirme Kriterleri:
- Merkeze yakınlık (0-25 puan)
- Sosyal tesisler (okul, hastane) (0-20 puan)
- Ulaşım (toplu taşıma, otoyol) (0-20 puan)
- Altyapı (0-20 puan)
- Gelişim potansiyeli (0-15 puan)

Çıktı Formatı:
Skor: [0-100]
Harf: [A/B/C/D]
Potansiyel: [Yüksek/Orta/Düşük]
Gerekçe: [Kısa açıklama, max 100 kelime]

Analiz:
```

### **Template 2: Yatırım Potansiyeli**

```
Yatırım potansiyeli analizi.

Lokasyon: {lokasyon}
Kategori: {kategori}
Fiyat: {fiyat}
Alan: {metrekare} m²

Analiz Et:
- Bölgenin gelişim trendi
- Benzer satışlar (son 6 ay)
- Gelecek projeler (plan)
- Risk faktörleri

Çıktı:
Potansiyel: [Yüksek/Orta/Düşük]
Tahmini Getiri: [%... yıllık]
Risk: [Düşük/Orta/Yüksek]
Gerekçe: [Max 150 kelime]

Analiz:
```

---

## 💰 FİYAT ANALİZİ PROMPT'LARI

### **Template 1: Piyasa Karşılaştırması**

```
Fiyat analizi yap ve 3 öneri sun.

Girilen Fiyat: {base_price} {currency}
Kategori: {kategori}
Lokasyon: {lokasyon}
Alan: {metrekare} m²
Özellikler: {ozellikler}

Hesapla:
- m² başı fiyat
- Bölge ortalaması ile karşılaştır
- 3 seviyeli öneri:
  1. Pazarlık (-10%): Hızlı satış
  2. Piyasa (+5%): Ortalama
  3. Premium (+15%): Özel özellikler

Format:
[Seviye]: [Fiyat] - [Gerekçe]

Analiz:
```

### **Template 2: Fiyat Trendi**

```
Son 6 aydaki fiyat trendini analiz et.

Lokasyon: {lokasyon}
Kategori: {kategori}

Analiz:
- Fiyat artış/azalış yönü
- Trend yüzdesi
- Sebep (piyasa, sezon, vb.)
- Gelecek 3 ay tahmini

Max 100 kelime.

Trend Analizi:
```

---

## 👥 CRM ANALİZİ PROMPT'LARI

### **Template 1: Müşteri Profil Analizi**

```
Kişi profili analiz et ve CRM skoru hesapla.

Kişi Bilgileri:
- Ad Soyad: {tam_ad}
- Müşteri Tipi: {musteri_tipi}
- Toplam İlan: {ilan_sayisi}
- Ortalama Fiyat: {ortalama_fiyat}
- Son Aktivite: {son_aktivite}

CRM Skoru Hesapla (0-100):
- İlan sayısı: 30 puan
- Başarılı satış: 30 puan
- Aktiflik: 20 puan
- Bütçe uyumu: 20 puan

Çıktı:
Skor: [0-100]
Segment: [Premium/Orta/Düşük]
Öneri: [Nasıl yaklaşılmalı]

Analiz:
```

### **Template 2: İlan Eşleştirme**

```
Müşteriye uygun ilanları eşleştir.

Müşteri:
- Bütçe: {butce_min}-{butce_max} {para_birimi}
- Lokasyon: {tercih_lokasyon}
- Kategori: {tercih_kategori}
- Özellikler: {aranan_ozellikler}

İlanlar (JSON):
{ilan_listesi}

Eşleştir:
- Bütçe uyumu (0-30 puan)
- Lokasyon uyumu (0-30 puan)
- Özellik uyumu (0-40 puan)

Çıktı: Top 5 ilan, eşleşme skorları ile

Eşleşmeler:
```

---

## 🖼️ GÖRSEL ANALİZİ PROMPT'LARI

### **Template 1: Fotoğraf Kalite Analizi**

```
Emlak fotoğrafını analiz et.

Kontrol Et:
- Aydınlatma: İyi/Orta/Kötü
- Açı: Profesyonel/Amatör
- Netlik: Keskin/Bulanık
- Kompozisyon: İyi/Orta/Zayıf

Kalite Skoru: 0-10
Öneriler: Nasıl iyileştirilir?

Analiz:
```

### **Template 2: OCR Tapu Okuma**

```
Tapu senedini oku ve bilgileri çıkar.

Görsel: [Tapu Senedi]

Çıkar:
- Ada No
- Parsel No
- Alan (m²)
- İl, İlçe, Mahalle
- Hisse oranı

Format: JSON

OCR Sonuç:
```

---

## 🎨 PORTAL ÖZEL PROMPT'LAR

### **Template 1: Sahibinden.com Optimizasyon**

```
Sahibinden.com için optimize edilmiş açıklama.

Kurallar:
- Başlık: Maksimum 50 karakter
- Açıklama: 100-150 kelime (kısa)
- Bullet point'ler kullan
- Fiyat vurgusu
- Hızlı bilgi

Açıklama:
```

### **Template 2: Hepsiemlak Optimizasyon**

```
Hepsiemlak için açıklama.

Kurallar:
- Başlık: 60-70 karakter
- Açıklama: 180-220 kelime
- Profesyonel ton
- Teknik detaylar

Açıklama:
```

---

## 🎯 ÖZEL SENARYO PROMPT'LARI

### **A/B Test Varyantları:**

```
2 farklı başlık versiyonu oluştur (A/B test).

Version A: SEO odaklı, anahtar kelime yoğun
Version B: Emotion odaklı, çekici dil

Veri: {ilan_bilgileri}

Çıktı:
A: [Başlık]
B: [Başlık]

Test Hipotezi: Hangisi daha çok tıklanır?

Varyantlar:
```

### **Seasonal Content:**

```
Sezon bazlı açıklama güncellemesi.

Mevsim: {mevsim} (Yaz/Kış/İlkbahar/Sonbahar)
Kategori: Yazlık Villa
Lokasyon: {lokasyon}

Vurgu:
Yaz: Plaj, deniz, aktiviteler
Kış: Sessizlik, huzur, kış tatili
İlkbahar: Doğa, çiçekler, yürüyüş
Sonbahar: Rahat, dinlence

220 kelime, sezona uygun ton.

Açıklama:
```

---

## 🤖 SİSTEM PROMPT'LARI

### **Base System Prompt (Ollama):**

```
Sen Yalıhan Emlak için çalışan uzman bir emlak danışmanısın.

Görevin:
- İlan başlıkları ve açıklamaları oluşturmak
- Fiyat önerileri sunmak
- Lokasyon analizi yapmak
- Müşteri danışmanlığı vermek

Kurallar:
- Türkçe yaz, dilbilgisi kurallarına uy
- Profesyonel ve güvenilir ol
- SEO uyumlu içerik üret
- Context7 standartlarına uy
- Emoji kullanma
- Asla otomatik kayıt yapma (sadece öneri)

Tonlar:
- seo: Anahtar kelime odaklı
- kurumsal: Profesyonel ve resmi
- hizli_satis: Heyecan verici ve acil
- luks: Prestijli ve özel

Şimdi kullanıcıya yardım et!
```

---

## 🎯 ÖRNEKLERLE PROMPT'LAR

### **Örnek 1: Villa Başlığı**

**Input:**

```yaml
kategori: Villa
yayin_tipi: Satılık
lokasyon: Bodrum Yalıkavak
fiyat: 3500000
para_birimi: TRY
ozellik: Deniz manzarası, özel havuz
tone: seo
```

**Prompt:**

```
Sen bir emlak uzmanısın. Aşağıdaki bilgilere göre SEO optimize 3 farklı ilan başlığı oluştur.

Kategori: Villa
Yayın Tipi: Satılık
Lokasyon: Bodrum Yalıkavak
Fiyat: 3.500.000 ₺
Özellik: Deniz manzarası, özel havuz

Kurallar:
- Her başlık 60-80 karakter
- Lokasyon mutlaka geçmeli (SEO)
- Anahtar kelime: "Satılık Villa", "Yalıkavak", "Bodrum"
- Özelliği vurgula
- Emoji kullanma

Başlıklar:
```

**Beklenen Çıktı:**

```
Yalıkavak Deniz Manzaralı Satılık Villa - Özel Havuzlu 3.5M ₺
Bodrum Yalıkavak'ta Satılık Lüks Villa - Denize Sıfır Konumda
Yalıkavak Premium Lokasyonda Satılık Villa - Havuz ve Deniz Manzarası
```

---

### **Örnek 2: Arsa Açıklaması**

**Input:**

```yaml
kategori: Arsa
imar_durumu: İmarlı
kaks: 1.5
taks: 0.35
alan: 1000
lokasyon: Bodrum Gümüşlük
fiyat: 5000000
```

**Prompt:**

```
Profesyonel arsa ilanı açıklaması yaz.

Arsa: İmarlı, 1000 m²
KAKS: 1.5, TAKS: 0.35
İnşaat Alanı: 1500 m² (KAKS × Alan)
Lokasyon: Bodrum Gümüşlük
Fiyat: 5.000.000 ₺

Vurgula:
- İmar durumu ve KAKS/TAKS avantajı
- 1500 m² inşaat imkanı
- Gümüşlük lokasyonunun değeri
- Yatırım potansiyeli

3 paragraf:
1. İmar durumu ve teknik detaylar (70 kelime)
2. Lokasyon ve çevre (80 kelime)
3. Yatırım analizi ve potansiyel (70 kelime)

Açıklama:
```

**Beklenen Çıktı:**

```
Bodrum Gümüşlük bölgesinde, tamamen imar dahilinde 1000 m² arsa.
KAKS 1.5 ve TAKS 0.35 değerleri ile toplam 1500 m² inşaat alanı imkanı sunuyor.
Modern villa veya butik otel projesi için ideal.

Gümüşlük'ün turistik bölgesinde, denize 800 metre mesafede konumlanmış arsa,
bölgenin artan değerine paralel olarak yüksek yatırım potansiyeline sahip.
Elektrik, su, doğalgaz altyapısı mevcut.

5 milyon TL fiyat ile bölge ortalamasına uygun, 2-3 yıllık dönemde %25-30
değer artışı potansiyeli bulunuyor. Detaylı bilgi ve imar planı için iletişime geçin.
```

---

## 📊 ANALYTICS PROMPT'LARI

### **İlan Performans Raporu:**

```
İlan performansını analiz et.

İlan ID: {id}
Yayın Tarihi: {created_at}
Görüntülenme: {views}
Tıklama: {clicks}
Lead: {leads}
Gün: {days_active}

Değerlendirme:
- CTR (Click Through Rate)
- Conversion Rate
- Günlük ortalama
- Beklenen vs Gerçekleşen

Öneriler:
- Başlık değişikliği?
- Fiyat ayarı?
- Fotoğraf kalitesi?

Rapor:
```

---

## 🎓 PROMPT İYİLEŞTİRME

### **Feedback Loop:**

```
Kullanıcı Düzenlemesi:
  AI Önerisi: "Yalıkavak'ta satılık villa"
  Kullanıcı: "Yalıkavak'ta deniz manzaralı satılık lüks villa"

Öğrenme:
  - "deniz manzaralı" eklenmiş → Vurgula
  - "lüks" eklenmiş → Segment: Premium

Sonraki Önerilerde:
  → Lokasyon + "deniz manzaralı" + kategori + "lüks"
```

### **Prompt Versiyonlama:**

```yaml
v1.0: "Yalıkavak villa" (kabul %60)
v1.1: "Yalıkavak'ta villa" (kabul %65)
v1.2: "Yalıkavak'ta satılık villa" (kabul %72)
v1.3: "Yalıkavak deniz manzaralı satılık villa" (kabul %85) ✅ Best
```

---

## 🚀 ADVANCED PROMPT'LAR

### **Multi-Step Reasoning:**

```
Adım adım düşün ve ilan için en iyi başlığı bul.

Adım 1: Kategori analizi
  - Ne tür bir emlak?
  - Hedef kitle kim?

Adım 2: Lokasyon değeri
  - Premium mi, standart mı?
  - Özel özellik var mı?

Adım 3: Ton seçimi
  - Fiyat segmentine göre
  - SEO mi, Lüks mü?

Adım 4: Başlık oluştur
  - Adım 1-3'ü birleştir
  - 3 varyant üret

Veri: {ilan_bilgileri}

Düşünce Süreci ve Başlıklar:
```

---

**📝 ÖZET:** Bu prompt'ları kopyala-yapıştır ve değişkenleri doldur. AI tutarlı ve kaliteli sonuç üretir.
