# ✅ AI Eğitim Kontrol Listesi

**AnythingLLM Embedding Final Checklist**  
**Version:** 1.0.0

---

## 📋 KURULUM ÖNCESİ

### **Gereksinimler:**

- [ ] AnythingLLM kurulu ve çalışıyor (http://localhost:3001)
- [ ] Ollama server çalışıyor (http://51.75.64.121:11434)
- [ ] gemma2:2b modeli indirilmiş
- [ ] Tüm eğitim dokümanları hazır (8 dosya)
- [ ] System prompt metni hazır

---

## 📁 DOKÜMAN KONTROLÜ

### **Core Dokümanlar (Zorunlu):**

- [ ] **00-ANYTHINGLLM-MASTER-TRAINING.md**
    - [ ] Ollama endpoint doğru (http://51.75.64.121:11434)
    - [ ] 5 AI provider listelenmiş
    - [ ] Sistem mimarisi açıklanmış
    - [ ] Güvenlik kuralları var

- [ ] **01-AI-FEATURES-GUIDE.md**
    - [ ] Başlık/açıklama/lokasyon/fiyat özellikleri
    - [ ] 4 ton profili (SEO, Kurumsal, Hızlı Satış, Lüks)
    - [ ] Cache stratejileri
    - [ ] Performans metrikleri

- [ ] **02-CONTEXT7-RULES-SIMPLIFIED.md** ⭐ **KRİTİK**
    - [ ] Zorunlu alan adları (status, il_id, para_birimi)
    - [ ] Yasaklar listesi (durum, sehir, aktif)
    - [ ] Neo Design System kuralları
    - [ ] Lokasyon hiyerarşisi

- [ ] **03-DATABASE-SCHEMA-FOR-AI.md**
    - [ ] İlanlar tablosu (100+ alan)
    - [ ] Kişiler tablosu
    - [ ] Kategoriler tablosu
    - [ ] İlişki şemaları

- [ ] **04-PROMPT-TEMPLATES.md**
    - [ ] Başlık prompt'ları (4 template)
    - [ ] Açıklama prompt'ları (3 template)
    - [ ] Lokasyon analizi prompt'ı
    - [ ] Fiyat analizi prompt'ı

- [ ] **05-USE-CASES-AND-SCENARIOS.md**
    - [ ] 8 gerçek senaryo
    - [ ] Örnek dialog'lar
    - [ ] Edge case'ler
    - [ ] Performans örnekleri

- [ ] **06-API-REFERENCE.md**
    - [ ] 6 ana endpoint
    - [ ] Request/Response örnekleri
    - [ ] Error handling
    - [ ] Rate limiting bilgisi

- [ ] **07-EMBEDDING-GUIDE.md**
    - [ ] Kurulum adımları
    - [ ] Test senaryoları
    - [ ] Troubleshooting
    - [ ] Başarı kriterleri

---

## 🚀 UPLOAD ADIMLARI

### **AnythingLLM'de:**

**1. Workspace Oluştur:**

```
Name: Yalıhan Emlak AI
LLM Provider: Ollama
Chat Model: gemma2:2b
Embedding Provider: Ollama (veya OpenAI)
Embedding Model: nomic-embed-text (veya text-embedding-ada-002)
```

**2. Documents Upload:**

```
Workspace → Documents → Upload

Sırayla Drag & Drop:
1. 00-ANYTHINGLLM-MASTER-TRAINING.md ✅
2. 02-CONTEXT7-RULES-SIMPLIFIED.md ✅
3. 03-DATABASE-SCHEMA-FOR-AI.md ✅
4. 01-AI-FEATURES-GUIDE.md ✅
5. 04-PROMPT-TEMPLATES.md ✅
6. 05-USE-CASES-AND-SCENARIOS.md ✅
7. 06-API-REFERENCE.md ✅

"Watch Folder" kullanarak hepsini aynı anda da ekleyebilirsiniz:
→ docs/ai-training/ klasörünü seçin
→ Auto-sync: ON
```

**3. System Prompt:**

```
Settings → Agent Configuration → System Prompt

Kopyala: (07-EMBEDDING-GUIDE.md içindeki system prompt)

Kaydet!
```

**4. Vector DB Ayarları:**

```
Settings → Vector Database

Chunk Size: 1000
Chunk Overlap: 200
Similarity Threshold: 0.75
Max Results: 5
```

---

## 🧪 TEST SENARYOLARI

### **Temel Testler (Zorunlu):**

#### **Test 1: Tanışma**

```
Soru: "Merhaba! Sen kimsin?"

✅ Beklenen:
"Merhaba! Ben Yalıhan Emlak için çalışan AI asistanıyım.
Size emlak ilanları için başlık/açıklama üretimi, fiyat analizi,
lokasyon değerlendirmesi gibi konularda yardımcı olabilirim.
Nasıl yardımcı olabilirim?"

❌ Hatalı:
"I am an AI assistant..." (İngilizce - Türkçe olmalı)
```

#### **Test 2: Context7 Bilgisi**

```
Soru: "status yerine durum kullanabilir miyim?"

✅ Beklenen:
"Hayır, Context7 kurallarına göre 'durum' field adı yasaktır.
✅ DOĞRU: 'status' veya 'active'
❌ YASAK: 'durum', 'is_active', 'aktif'
Context7 compliance %100 zorunludur."

❌ Hatalı:
"Evet kullanabilirsiniz" (Yanlış)
"Bilmiyorum" (Öğrenmemiş)
```

#### **Test 3: Başlık Üretimi**

```
Soru: "Yalıkavak'ta 3.5M ₺ satılık villa için SEO başlığı"

✅ Beklenen:
{
  "variants": [
    "Yalıkavak Deniz Manzaralı Satılık Villa - 3.5M ₺",
    "Bodrum Yalıkavak'ta Satılık Lüks Villa",
    "Yalıkavak Premium Lokasyonda Satılık Villa"
  ],
  "tone": "seo",
  "context7_compliant": true
}

❌ Hatalı:
"🏠 Süper Villa!!" (Emoji yasak)
"Şehirde villa" (sehir yasak, il kullan)
```

#### **Test 4: Ollama Endpoint**

```
Soru: "Ollama endpoint'i nedir?"

✅ Beklenen:
"Ollama endpoint: http://51.75.64.121:11434
Model: gemma2:2b
Durum: Aktif ✅"

❌ Hatalı:
"Bilmiyorum" (Embedded bilgi)
"localhost:11434" (Yanlış endpoint)
```

#### **Test 5: Fiyat Formatı**

```
Soru: "Para birimi sembolleri nelerdir?"

✅ Beklenen:
"Para birimi sembolleri:
- TRY: ₺
- USD: $
- EUR: €
- GBP: £

Context7 field: para_birimi (currency YASAK)"

❌ Hatalı:
"TL: ₺" (TL değil, TRY)
```

---

## 📊 EMBEDDING KALİTE KONTROLÜ

### **Vector DB Metrikleri:**

- [ ] **Chunk Count:** 60-70 arası ✅
- [ ] **Total Size:** 2-3 MB ✅
- [ ] **Embedding Time:** <5 dakika ✅
- [ ] **Index Status:** Completed ✅

### **Retrieval Kalitesi:**

```bash
# AnythingLLM Console'da kontrol et:

Query: "Context7"
Expected Chunks: 5-7 (relevance >0.75)
Source: 02-CONTEXT7-RULES-SIMPLIFIED.md

Query: "Başlık üret"
Expected Chunks: 4-6
Source: 04-PROMPT-TEMPLATES.md

Query: "ilanlar tablosu"
Expected Chunks: 3-5
Source: 03-DATABASE-SCHEMA-FOR-AI.md
```

---

## 🎯 PERFORMANS KONTROLÜ

### **Yanıt Hızı:**

- [ ] Basit soru (<1s): "Merhaba"
- [ ] Orta soru (<2s): "Başlık öner"
- [ ] Karmaşık (<3s): "Açıklama yaz"
- [ ] Analiz (<4s): "CRM analizi"

### **Relevance Score:**

```
Target: >0.75 (her query için)

Kontrol:
Query: "Context7 kuralları"
→ Relevance: 0.85-0.95 ✅

Query: "Başlık prompt"
→ Relevance: 0.80-0.90 ✅
```

---

## ✅ FINAL CHECKLIST

### **Embedding:**

- [ ] 7 core doküman uploaded
- [ ] Processing tamamlandı (her biri ✅)
- [ ] Chunk'lar oluşturuldu (60-70)
- [ ] Vector DB indexed

### **Configuration:**

- [ ] Workspace oluşturuldu
- [ ] Ollama provider seçildi
- [ ] gemma2:2b model seçildi
- [ ] System prompt ayarlandı
- [ ] Vector DB optimize edildi

### **Testing:**

- [ ] 5 temel test PASSED (5/5)
- [ ] Context7 compliance ✅
- [ ] Türkçe yanıt ✅
- [ ] JSON format ✅
- [ ] Yanıt hızı <3s ✅

### **Quality:**

- [ ] Relevance score >0.75
- [ ] Chunk quality >0.85
- [ ] Response accuracy >90%
- [ ] User satisfaction >4.5/5

---

## 🎉 BAŞARI KRİTERLERİ

### **Tüm bunlar ✅ ise → BAŞARILI!**

```yaml
Embedding Quality: ✅ Excellent (>0.85)
Test Coverage: ✅ 5/5 passed
Context7 Compliance: ✅ 100%
Performance: ✅ <3s response
Turkish Support: ✅ Native
JSON Format: ✅ Structured
Ollama Integration: ✅ Active
```

---

## 🚀 KULLANIMA BAŞLA

### **İlk Kullanım:**

```
1. Chat penceresini aç
2. "Merhaba! Yalıkavak'ta villa için başlık öner" yaz
3. AI yanıt versin (3 varyant, JSON)
4. Sonucu değerlendir
5. Production'da kullanmaya başla! 🎉
```

---

## 📈 İZLEME ve İYİLEŞTİRME

### **Haftalık Kontrol:**

- [ ] AI kullanım sayısı (hedef: >50/hafta)
- [ ] Yanıt doğruluğu (hedef: >90%)
- [ ] User feedback (hedef: >4.5/5)
- [ ] Error rate (hedef: <5%)

### **Aylık Güncelleme:**

- [ ] Yeni özellikler dokümana eklendi mi?
- [ ] Prompt'lar optimize edildi mi?
- [ ] Context7 kuralları güncellendi mi?
- [ ] Performance iyileştirildi mi?

---

## 🔧 TROUBLESHOOTING

### **Sorun: AI yanıt vermiyor**

```bash
Kontrol:
1. AnythingLLM çalışıyor mu?
2. Workspace seçili mi?
3. Ollama çalışıyor mu?
   curl http://51.75.64.121:11434/api/tags
4. Documents embedded mi? (7/7)
```

### **Sorun: Context7 ihlal ediyor**

```bash
Çözüm:
1. System prompt'u güncelle
2. "Context7 %100 uy" vurgusunu artır
3. 02-CONTEXT7-RULES... dokümanını re-embed et
4. Test et: "status kullanabilir miyim?"
```

### **Sorun: Yavaş yanıt (>5s)**

```bash
Optimize Et:
1. Chunk size: 1000 → 800
2. Max results: 5 → 3
3. Similarity: 0.75 → 0.80
4. Cache kontrol et
```

---

## 🎓 EĞİTİM KOMPLETİ ONAY

### **Tüm bunlar ✅ ise → Eğitim Başarılı!**

```
✅ Dokümanlar (7/7)
✅ Embedding (60-70 chunks)
✅ System Prompt (ayarlandı)
✅ Vector DB (optimize)
✅ Testler (5/5 passed)
✅ Context7 (100% uyumlu)
✅ Türkçe (native support)
✅ Performance (<3s)
✅ Ollama (active)
✅ JSON (structured)
```

**SONUÇ: AI ASISTAN KULLANIMA HAZIR! 🚀**

---

## 📞 DESTEK

### **Sorun Yaşarsanız:**

1. **QUICK-START.md** → Hızlı çözümler
2. **07-EMBEDDING-GUIDE.md** → Detaylı troubleshooting
3. Re-embed yap (son çare)

---

## 🎯 SONRAKI ADIMLAR

### **Production'a Geçiş:**

1. [ ] Final testler (10 farklı soru)
2. [ ] Performance benchmark
3. [ ] User acceptance test
4. [ ] Production deployment
5. [ ] Monitoring setup

### **İlk Hafta:**

1. [ ] 20+ gerçek kullanım
2. [ ] Feedback toplama
3. [ ] Prompt iyileştirme
4. [ ] Doküman güncelleme

---

**✅ Bu checklist'i tamamladınız mı? Tebrikler! AI asistanınız hazır! 🎉**
