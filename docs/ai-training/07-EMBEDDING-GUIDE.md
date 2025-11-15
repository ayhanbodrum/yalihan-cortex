# 🎓 AnythingLLM Embedding Rehberi

**Version:** 1.0.0  
**Tarih:** 11 Ekim 2025

---

## 📋 ANYTHINGLLM'E EMBED ETME ADIMLARI

### **Adım 1: Workspace Oluştur**

1. AnythingLLM'i aç: http://localhost:3001
2. Sol menüden "Workspaces" → "New Workspace"
3. İsim: **Yalıhan Emlak AI Assistant**
4. Açıklama: "Türkçe emlak danışmanlık AI'ı"
5. LLM Provider: **Ollama**
6. Model: **gemma2:2b**

---

### **Adım 2: Documents Upload**

**Upload edilecek dosyalar (Sırayla):**

```yaml
Öncelik 1 (Kritik): 1. ✅ 00-ANYTHINGLLM-MASTER-TRAINING.md
    2. ✅ 02-CONTEXT7-RULES-SIMPLIFIED.md
    3. ✅ 03-DATABASE-SCHEMA-FOR-AI.md

Öncelik 2 (Önemli): 4. ✅ 01-AI-FEATURES-GUIDE.md
    5. ✅ 04-PROMPT-TEMPLATES.md
    6. ✅ 05-USE-CASES-AND-SCENARIOS.md
    7. ✅ 06-API-REFERENCE.md

Öncelik 3 (Ek Bilgi): 8. docs/prompts/ilan-aciklama.prompt.md
    9. docs/prompts/ilan-baslik.prompt.md
    10. docs/index.md
```

---

### **Adım 3: Vector Database Ayarları**

**Settings → Vector Database:**

```yaml
Provider: LanceDB (varsayılan)
Chunk Size: 1000 (optimal)
Chunk Overlap: 200
Similarity Threshold: 0.7
Max Results: 5
```

---

### **Adım 4: Embedding Strategy**

```yaml
Embedding Model: text-embedding-ada-002 (OpenAI)
# veya
Embedding Model: all-MiniLM-L6-v2 (local, ücretsiz)

Document Processing:
  - Auto chunking: Enabled
  - Metadata extraction: Enabled
  - Language detection: Turkish
```

---

### **Adım 5: System Prompt Ayarla**

**AnythingLLM Settings → System Prompt:**

```
Sen Yalıhan Emlak için çalışan uzman bir emlak danışman AI'sın.

Kimliğin:
- İsim: Yalıhan AI Asistan
- Rol: Emlak Danışmanı
- Dil: Türkçe
- Uzmanlık: Bodrum, Yalıkavak bölgesi emlakları

Görevlerin:
1. İlan başlığı ve açıklaması oluşturmak
2. Fiyat önerileri sunmak
3. Lokasyon analizi yapmak
4. Müşteri danışmanlığı yapmak
5. CRM analizi yapmak

Kurallar:
✅ ZORUNLU:
  - Context7 kurallarına %100 uy
  - Türkçe gramer kurallarına dikkat et
  - Profesyonel ve güvenilir ol
  - SEO uyumlu içerik üret
  - JSON formatında yanıt ver

❌ YASAK:
  - Emoji kullanma
  - Türkçe database field adı kullanma (durum→status)
  - Otomatik kayıt yapma (sadece öneri)
  - Kişisel veri işleme

Ton Seçenekleri:
- seo: Anahtar kelime odaklı, detaylı
- kurumsal: Profesyonel, resmi
- hizli_satis: Heyecan verici, acil
- luks: Prestijli, özel

Her zaman önce embedded dokümanlardan bilgi çek, sonra yanıt ver.
Emin değilsen "Embedded dokümanlarda bu bilgi yok" de.

Hazır mısın?
```

---

## 🎯 TEST SENARYOLARI

### **Test 1: Temel Başlık Üretimi**

**Soru:**

```
Yalıkavak'ta 3.5 milyon TL'ye satılık villa için başlık öner.
```

**Beklenen Yanıt:**

```json
{
    "variants": [
        "Yalıkavak Satılık Villa - Deniz Manzaralı 3.5M ₺",
        "Bodrum Yalıkavak'ta Satılık Lüks Villa",
        "Yalıkavak Premium Lokasyonda Satılık Villa"
    ],
    "tone": "seo",
    "context7_compliant": true
}
```

---

### **Test 2: Context7 Kuralları**

**Soru:**

```
"status" alanı yerine ne kullanmalıyım?
```

**Beklenen Yanıt:**

```
Context7 kurallarına göre:

✅ DOĞRU: "status" veya "active" (boolean)
❌ YASAK: "durum", "is_active", "aktif"

Örnek kullanım:
- Database field: status (boolean) veya active
- Model: $ilan->status
- Query: where('status', 'Aktif')

Context7 compliance: %100 uyumlu
```

---

### **Test 3: Lokasyon Analizi**

**Soru:**

```
Yalıkavak lokasyonunu analiz et.
```

**Beklenen Yanıt:**

```
📍 Yalıkavak Lokasyon Analizi

Skor: 92/100 ⭐⭐⭐⭐⭐
Harf: A
Potansiyel: Yüksek

Gerekçe:
Premium tatil bölgesi, tüm sosyal tesislere yakın,
yüksek turist talebi, yıllık %15-20 değer artışı.

Detaylar:
- Merkeze yakınlık: 23/25
- Sosyal tesisler: 19/20
- Ulaşım: 18/20
- Altyapı: 20/20
- Gelişim: 12/15

Context7 field: il_id, ilce_id, mahalle_id
```

---

## 📊 EMBEDDING PERFORMANS

### **Hedef Metrikler:**

```yaml
Chunk Count: ~50-70 (7 doküman için)
Embedding Time: <5 dakika
Vector DB Size: ~2-3 MB
Query Time: <500ms
Relevance Score: >0.75
```

### **Test Query:**

```
Query: "İlan başlığı nasıl oluşturulur?"
Expected: Prompt template'ler döndürülmeli
Relevance: >0.80
```

---

## 🔧 TROUBLESHOOTING

### **Sorun 1: Yavaş Yanıt**

**Sebep:** Chunk size çok büyük  
**Çözüm:** Chunk size 1000 → 800'e düşür

### **Sorun 2: İlgisiz Yanıt**

**Sebep:** Similarity threshold çok düşük  
**Çözüm:** 0.7 → 0.75'e yükselt

### **Sorun 3: Context7 Kurallarına Uymayan Yanıt**

**Sebep:** System prompt yeterince güçlü değil  
**Çözüm:** "Context7 kurallarına %100 uy" vurgusunu artır

---

## 🎯 EMBEDDING KONTROLÜ

### **Başarılı Embedding Kriterleri:**

✅ **Doküman Sayısı:** 7 core doküman yüklendi  
✅ **Chunk'lar:** Düzgün bölündü (optimal 1000 token)  
✅ **Metadata:** Doküman isimleri ve kategoriler korundu  
✅ **Test Sorular:** 5/5 doğru yanıt

### **Test Soruları:**

```
1. "Context7 nedir?" → Kurallar dökümanı referans almalı
2. "Başlık öner" → Prompt template kullanmalı
3. "status field yasak mı?" → Context7 rules'dan yanıt vermeli
4. "Para birimi nedir?" → Database schema'dan bilgi vermeli
5. "Ollama endpoint'i ne?" → Master training'den yanıt vermeli
```

---

## 📚 DOSYA MANİFESTO

### **Embedded Dosyalar ve İçerikleri:**

```yaml
00-ANYTHINGLLM-MASTER-TRAINING.md:
    - Sistem kimliği
    - AI provider detayları
    - Mimari genel bakış
    - Güvenlik kuralları

01-AI-FEATURES-GUIDE.md:
    - AI özellikleri detayı
    - Ton profilleri
    - Cache stratejileri
    - Performans metrikleri

02-CONTEXT7-RULES-SIMPLIFIED.md:
    - Zorunlu field adları
    - Yasaklar listesi
    - Lokasyon kuralları
    - Neo Design System

03-DATABASE-SCHEMA-FOR-AI.md:
    - Tablo yapıları
    - İlişkiler
    - Query örnekleri
    - Field açıklamaları

04-PROMPT-TEMPLATES.md:
    - Tüm prompt şablonları
    - Kategori özel prompt'lar
    - Ton bazlı varyantlar

05-USE-CASES-AND-SCENARIOS.md:
    - Gerçek kullanım senaryoları
    - Örnek dialog'lar
    - Edge case'ler

06-API-REFERENCE.md:
    - Tüm endpoint'ler
    - Request/Response örnekleri
    - Error handling
```

---

## 🚀 DEPLOYMENT SONRASI

### **1. İlk Test:**

```
User: "Merhaba! İlan başlığı oluşturabilir misin?"

AI: "Elbette! İlan başlığı oluşturmak için şu bilgilere ihtiyacım var:
- Kategori (Villa, Daire, Arsa, vb.)
- Yayın Tipi (Satılık, Kiralık)
- Lokasyon (İl, İlçe, Mahalle)
- Fiyat (opsiyonel)
- Özel özellikler (opsiyonel)

Hangi ton'u tercih edersiniz? (SEO, Kurumsal, Hızlı Satış, Lüks)"
```

### **2. Context7 Kontrol:**

```
User: "status field'ı kullanabilir miyim?"

AI: "Hayır, Context7 kurallarına göre 'status' field adı yasaktır.

✅ DOĞRU: 'status' veya 'active' kullanmalısınız
❌ YASAK: 'durum', 'is_active', 'aktif'

Örnek:
$ilan->status  // Doğru
$ilan->durum   // Yanlış

Context7 compliance: Bu kurala uymalısınız."
```

### **3. Performans Testi:**

```bash
# 10 ardışık soru sor, yanıt sürelerini kaydet
# Ortalama <3 saniye olmalı
```

---

## 🎉 BAŞARILI EMBEDDING KONTROLÜ

### **Checklist:**

- [ ] 7 core doküman yüklendi
- [ ] Vector DB oluşturuldu (50-70 chunk)
- [ ] System prompt ayarlandı
- [ ] Test soruları doğru yanıtlandı
- [ ] Context7 kuralları öğrenildi
- [ ] Türkçe yanıt veriyor
- [ ] JSON formatında yanıt
- [ ] Yanıt süresi <3s
- [ ] Ollama endpoint biliyor (http://51.75.64.121:11434)
- [ ] Para birimi sembolleri doğru (₺, $, €, £)

---

## 🔄 GÜNCELLEME STRATEJİSİ

### **Doküman Güncellendiğinde:**

```
1. Eski dokümanı workspace'ten sil
2. Yeni versiyonu upload et
3. Re-embed et (otomatik)
4. Test et (3-5 soru)
5. Doğrula (Context7 compliance)
```

### **Yeni Özellik Eklendiğinde:**

```
1. Yeni doküman hazırla (08-NEW-FEATURE.md)
2. Master training'e referans ekle
3. Upload ve embed et
4. System prompt'a ekle (gerekirse)
5. Test senaryoları ekle
```

---

## 💡 EMBEDDING İPUÇLARI

### **Optimal Chunk Size:**

```
Too Small (<500): Context kaybı
Optimal (800-1200): En iyi performans ✅
Too Large (>2000): Yavaş retrieval
```

### **Metadata Kullanımı:**

```yaml
Her doküman için:
    category: rules|features|schema|prompts|examples
    priority: critical|high|medium|low
    version: 1.0.0
    last_updated: 2025-10-11
```

### **Query Optimization:**

```
Spesifik sorular: En iyi sonuç
Genel sorular: Daha az kesin
Örnekle soru: Çok etkili

Örnek:
  ❌ Kötü: "Başlık ver"
  ✅ İyi: "Villa için başlık öner"
  ✅✅ En İyi: "Yalıkavak'ta 3.5M ₺ satılık villa için SEO başlığı"
```

---

## 🎯 BEKLENEN SONUÇLAR

### **AI Yetkinlikleri:**

✅ **Bilmesi Gerekenler:**

- Context7 kurallarının %100'ü
- Tüm database field adları
- AI provider endpoint'leri (Ollama: http://51.75.64.121:11434)
- Prompt şablonlarının tamamı
- Ton profilleri (SEO, Kurumsal, Hızlı Satış, Lüks)
- API endpoint'leri ve formatları

✅ **Yapabilmesi Gerekenler:**

- İlan başlığı üretmek (3 varyant, 60-80 karakter)
- İlan açıklaması yazmak (200-250 kelime, 3 paragraf)
- Lokasyon analizi (Skor, Harf, Potansiyel)
- Fiyat önerisi (3 seviye)
- CRM müşteri analizi
- Portal optimizasyonu

✅ **Yapmaması Gerekenler:**

- Emoji kullanmak
- Türkçe database field adı kullanmak
- Otomatik kayıt yapmak
- Kişisel veri işlemek

---

## 📊 KALİTE METRİKLERİ

### **Embedding Kalitesi:**

```yaml
Doküman Coverage: %100 (7/7 dosya)
Chunk Quality: >0.85
Retrieval Accuracy: >90%
Response Relevance: >0.80
```

### **AI Performans:**

```yaml
Response Time: <3s
Accuracy: >90%
Context7 Compliance: %100
User Satisfaction: >4.5/5
```

---

## 🔍 ÖRNEK CONVERSATION FLOW

### **Conversation 1: Başlık Üretimi**

```
User:
Yalıkavak'ta 3 milyon TL'ye satılık villa için 3 başlık öner.

AI:
📝 İlan Başlığı Önerileri (SEO Tone)

1. "Yalıkavak Deniz Manzaralı Satılık Villa - Özel Havuzlu 3M ₺"
   (Uzunluk: 67 karakter, SEO Skor: 88/100)

2. "Bodrum Yalıkavak'ta Satılık Lüks Villa - 3.000.000 ₺"
   (Uzunluk: 59 karakter, SEO Skor: 85/100)

3. "Yalıkavak Premium Lokasyonda Satılık Villa"
   (Uzunluk: 48 karakter, SEO Skor: 82/100)

Önerim: Başlık #1 - En yüksek SEO skoru ve özellik vurgusu.

Farklı ton ister misiniz? (kurumsal, hizli_satis, luks)
```

---

### **Conversation 2: Context7 Sorgulama**

```
User:
"il" yerine "sehir" kullanabilir miyim?

AI:
❌ HAYIR - Context7 Kuralı İhlali

"sehir" field adı yasaktır. Bunun yerine:
✅ DOĞRU: "il" (İl tablosu için)
✅ DOĞRU: "il_id" (Foreign key için)

Database'de:
- Tablo: iller
- Kolon: il_adi
- Foreign Key: il_id

Model'de:
- İlişki: $ilan->il
- Accessor: $ilan->il->il_adi

Context7 Compliance: %100 zorunlu
```

---

### **Conversation 3: Fiyat Analizi**

```
User:
2.5 milyon TL fiyat Yalıkavak için uygun mu?

AI:
💰 Yalıkavak Fiyat Analizi

Girilen: 2.500.000 ₺
Bölge: Yalıkavak (Premium)

Analiz:
- Bölge Ortalaması: 3.200.000 ₺
- Sizin fiyat: %22 düşük ⚠️

Öneriler:
1. Hızlı Satış: 2.5M ₺ (mevcut) - Çabuk satılır
2. Piyasa: 3.2M ₺ (+28%) - Bölge ortalaması
3. Premium: 3.5M ₺ (+40%) - Özel özellikler varsa

Değerlendirme:
Yalıkavak premium lokasyon olduğu için 2.5M düşük kalıyor.
Eğer özel özellikler varsa (deniz manzarası, havuz) 3-3.5M uygun olur.

Önerim: En az 3M ₺ olarak belirleyin.
```

---

## 🎓 ÖĞRENME KONTROLÜ

### **AI'ye Sorulacak Kontrol Soruları:**

```
1. "Ollama endpoint'i nedir?"
   Beklenen: http://51.75.64.121:11434

2. "status field yasak mı?"
   Beklenen: Evet, "status" kullan

3. "Kaç paragraf açıklama?"
   Beklenen: 3 paragraf, 200-250 kelime

4. "Para birimi sembolü?"
   Beklenen: TRY=₺, USD=$, EUR=€, GBP=£

5. "Emoji kullanabilir miyim?"
   Beklenen: Hayır, YASAK

6. "CRM skoru nasıl hesaplanır?"
   Beklenen: 0-100, 4 kriter (ilan, satış, aktiflik, bütçe)

7. "Referans no formatı?"
   Beklenen: YE-{YAYIN}-{LOK}-{KAT}-{SIRA}

8. "Neo Design System prefix?"
   Beklenen: neo-* (neo-btn, neo-card)

9. "Ton seçenekleri?"
   Beklenen: seo, kurumsal, hizli_satis, luks

10. "Il ilişkisi?"
    Beklenen: $ilan->il (NOT $ilan->sehir)
```

---

## ✅ EMBEDDING TAMAMLANDI KONTROLÜ

### **Final Checklist:**

```
✅ 7 core doküman uploaded
✅ System prompt ayarlandı
✅ Ollama model seçildi (gemma2:2b)
✅ Vector DB oluşturuldu
✅ Test soruları yanıtlandı (10/10)
✅ Context7 compliance %100
✅ Türkçe yanıt veriyor
✅ JSON format kullanıyor
✅ Yanıt süresi <3s
✅ Relevance score >0.75
```

### **Başarı Mesajı:**

```
🎉 Yalıhan Emlak AI Asistanı Hazır!

Embedding: ✅ Tamamlandı
Dokümanlar: 7/7
Chunk'lar: 65
Vector DB: 2.3 MB
Test: 10/10 ✅

AI artık:
- İlan başlığı/açıklaması üretebilir
- Fiyat önerisi sunabilir
- Lokasyon analizi yapabilir
- Context7 kurallarına %100 uyabilir
- Türkçe profesyonel yanıt verebilir

Kullanıma hazır! 🚀
```

---

**🎓 ÖZET:** Bu rehberi takip ederek AnythingLLM'de eksiksiz AI asistan elde edersiniz.
