# ⚡ AnythingLLM Hızlı Başlangıç

**5 Dakikada AI Asistan Kurulumu**

---

## 🎯 HEDEF

Yalıhan Emlak sistemini %100 bilen AI asistan oluşturmak.

---

## ⚡ 5 ADIM KURULUM

### **1️⃣ Workspace Oluştur (1 dk)**

```
AnythingLLM Aç → http://localhost:3001
  ↓
"New Workspace" tıkla
  ↓
İsim: Yalıhan Emlak AI
Provider: Ollama
Model: gemma2:2b
Endpoint: http://51.75.64.121:11434
  ↓
"Create" tıkla
```

---

### **2️⃣ Dokümanları Upload Et (2 dk)**

**Sırayla Drag & Drop:**

```
1. ✅ 00-ANYTHINGLLM-MASTER-TRAINING.md (Temel bilgiler)
2. ✅ 02-CONTEXT7-RULES-SIMPLIFIED.md (Kurallar - KRİTİK)
3. ✅ 03-DATABASE-SCHEMA-FOR-AI.md (Veritabanı)
4. ✅ 01-AI-FEATURES-GUIDE.md (AI özellikleri)
5. ✅ 04-PROMPT-TEMPLATES.md (Prompt'lar)
6. ✅ 05-USE-CASES-AND-SCENARIOS.md (Örnekler)
7. ✅ 06-API-REFERENCE.md (API)
```

**Upload Sırasında:**

- Processing bekleyin (~30 saniye/doküman)
- "✅ Embedded" mesajını görün
- Toplam ~4 dakika sürer

---

### **3️⃣ System Prompt Ayarla (1 dk)**

```
Settings → Agent Configuration → System Prompt
```

**Kopyala-Yapıştır:**

```
Sen Yalıhan Emlak için çalışan uzman bir emlak danışman AI'sın.

GÖREVLER:
1. İlan başlığı/açıklaması oluştur
2. Fiyat önerileri sun
3. Lokasyon analizi yap
4. CRM analizi yap

KURALLAR (ZORUNLU):
✅ Context7 %100 uy (status, il_id kullan - durum, sehir YASAK)
✅ Türkçe gramer kurallarına uy
✅ JSON formatında yanıt ver
✅ SEO optimize et
❌ Emoji kullanma
❌ Otomatik kayıt yapma

TONLAR:
- seo: Anahtar kelime odaklı
- kurumsal: Profesyonel
- hizli_satis: Acil, heyecanlı
- luks: Prestijli

OLLAMA:
Endpoint: http://51.75.64.121:11434
Model: gemma2:2b

Her zaman embedded dokümanlardan bilgi çek.
Hazır mısın?
```

---

### **4️⃣ Ayarları Optimize Et (30 sn)**

```
Settings → Vector Database
```

**Optimal Ayarlar:**

```yaml
Chunk Size: 1000
Chunk Overlap: 200
Similarity Threshold: 0.75
Max Results: 5
Top K: 4
```

---

### **5️⃣ Test Et (30 sn)**

**Test Soruları:**

```
1️⃣ "Merhaba! İlan başlığı oluşturabilir misin?"
   Beklenen: "Evet! Hangi kategoride?"

2️⃣ "Yalıkavak'ta 3.5M ₺ satılık villa için başlık"
   Beklenen: 3 başlık varyantı, JSON format

3️⃣ "status field yasak mı?"
   Beklenen: "Evet, 'status' kullan"

4️⃣ "Ollama endpoint'i nedir?"
   Beklenen: "http://51.75.64.121:11434"

5️⃣ "Kaç paragraf açıklama?"
   Beklenen: "3 paragraf, 200-250 kelime"
```

**Tümü doğru yanıtlandıysa → ✅ BAŞARILI!**

---

## 🎉 KURULUM TAMAMLANDI

### **Artık AI yapabilir:**

✅ İlan başlığı üretmek (3 varyant)  
✅ İlan açıklaması yazmak (200-250 kelime)  
✅ Lokasyon analizi (Skor, Harf, Potansiyel)  
✅ Fiyat önerisi (3 seviye)  
✅ CRM analizi  
✅ Context7'ye %100 uyum

---

## 💡 KULLANIM ÖRNEĞİ

**Chat:**

```
You: Yalıkavak'ta 250 m² villa, 3.5M ₺ için SEO başlığı

AI: {
  "variants": [
    "Yalıkavak Deniz Manzaralı Satılık Villa - 250 m² 3.5M ₺",
    "Bodrum Yalıkavak'ta Satılık Lüks Villa - Özel Havuzlu",
    "Yalıkavak Premium Villa - 5+2 Satılık 250 m²"
  ],
  "recommended": 0,
  "seo_score": 88,
  "context7_compliant": true
}
```

---

## 🔍 SORUN GİDERME

### **AI yanıt vermiyor:**

```
1. Workspace seçili mi?
2. Ollama çalışıyor mu? (http://51.75.64.121:11434)
3. Dokümanlar embedded mi? (7/7)
```

### **Context7 kurallarına uymuyor:**

```
1. System prompt'u kontrol et
2. "Context7 %100 uy" vurgusu var mı?
3. 02-CONTEXT7-RULES... embedded mi?
```

### **Türkçe yanıt vermiyor:**

```
1. System prompt'ta "Türkçe" belirtilmiş mi?
2. Ollama model Türkçe destekliyor mu? (gemma2:2b ✅)
```

---

## 📊 BAŞARI KONTROLÜ

```
✅ 5/5 test sorusu doğru yanıtlandı
✅ Context7 kurallarını biliyor
✅ Türkçe yanıt veriyor
✅ JSON format kullanıyor
✅ Yanıt süresi <3 saniye
✅ Ollama endpoint'i biliyor
```

**Tümü ✅ ise → Kullanıma hazır! 🚀**

---

## 🎯 İLERİ SEVİYE

### **Fine-tuning için:**

1. `docs/prompts/*.prompt.md` dosyalarını da ekle
2. `docs/index.md` sistem genel bakış için
3. User feedback'e göre prompt'ları güncelle

---

**⚡ Toplam Süre: ~5 dakika**  
**Sonuç: Profesyonel AI asistan hazır! 🎉**
