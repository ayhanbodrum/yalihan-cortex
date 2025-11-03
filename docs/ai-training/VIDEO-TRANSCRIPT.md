# 🎬 AnythingLLM Kurulum Video Transcript

**Video Başlığı:** "Yalıhan Emlak AI Asistanı - AnythingLLM Kurulumu (5 Dakika)"  
**Süre:** 5:30  
**Seviye:** Başlangıç

---

## 🎬 VIDEO SENARYO

### **[0:00 - 0:30] Giriş**

```
🎬 AÇILIŞ:

Merhaba! Bu videoda Yalıhan Emlak için AI asistan kuracağız.

Süre: Sadece 5 dakika
Sonuç: Profesyonel emlak danışman AI
Platform: AnythingLLM
Model: Ollama gemma2:2b

Başlayalım! 🚀
```

---

### **[0:30 - 1:30] Adım 1: Workspace Oluşturma**

```
🖥️ EKRAN:

1. AnythingLLM aç → http://localhost:3001
2. Sol menü → "Workspaces"
3. "New Workspace" butonu

📝 DOLDUR:
Name: Yalıhan Emlak AI
Description: Türkçe emlak danışmanlık asistanı

LLM Provider: Ollama
Chat Model: gemma2:2b
Endpoint: http://51.75.64.121:11434

Embedding Provider: Ollama (veya OpenAI)
Embedding Model: nomic-embed-text

4. "Create Workspace" tıkla

✅ Workspace hazır!
```

---

### **[1:30 - 3:00] Adım 2: Dokümanları Upload**

```
📁 EKRAN:

1. Workspace içinde → "Documents" tab
2. "Upload Documents" butonu

📂 DOSYA SEÇ (docs/ai-training/ klasörü):

Sırayla drag & drop:
✅ 00-ANYTHINGLLM-MASTER-TRAINING.md
✅ 02-CONTEXT7-RULES-SIMPLIFIED.md
✅ 03-DATABASE-SCHEMA-FOR-AI.md
✅ 01-AI-FEATURES-GUIDE.md
✅ 04-PROMPT-TEMPLATES.md
✅ 05-USE-CASES-AND-SCENARIOS.md
✅ 06-API-REFERENCE.md

💡 İPUCU:
"Watch Folder" ile tüm klasörü ekleyebilirsiniz!
→ docs/ai-training/ seçin
→ Auto-sync: ON

⏳ BEKLE:
Her doküman ~20-30 saniye işlenir
"✅ Embedded" mesajını görün
Toplam: ~2-3 dakika

✅ 7 doküman yüklendi!
```

---

### **[3:00 - 4:00] Adım 3: System Prompt Ayarlama**

```
⚙️ EKRAN:

1. Settings ikonu (⚙️) tıkla
2. "Agent Configuration" tab
3. "System Prompt" textarea

📋 KOPYALA-YAPIŞTIR:

Sen Yalıhan Emlak için çalışan uzman bir emlak danışman AI'sın.

GÖREVLER:
1. İlan başlığı/açıklaması oluştur
2. Fiyat önerileri sun
3. Lokasyon analizi yap

KURALLAR:
✅ Context7 %100 uy (status, il_id - durum, sehir YASAK)
✅ Türkçe gramer
✅ JSON format
❌ Emoji kullanma

TONLAR: seo, kurumsal, hizli_satis, luks

Ollama: http://51.75.64.121:11434
Model: gemma2:2b

4. "Save Settings" tıkla

✅ System prompt kaydedildi!
```

---

### **[4:00 - 5:00] Adım 4: Test**

```
💬 EKRAN:

Chat penceresine gel

TEST 1: Tanışma
└─ "Merhaba! Sen kimsin?"
   ✅ Türkçe yanıt vermeli
   ✅ Görevlerini anlatmalı

TEST 2: Context7
└─ "status yerine durum kullanabilir miyim?"
   ✅ "Hayır, 'status' kullan" demeli

TEST 3: Başlık Üretimi
└─ "Yalıkavak'ta 3.5M ₺ satılık villa için başlık"
   ✅ 3 başlık varyantı
   ✅ JSON format
   ✅ 60-80 karakter

TEST 4: Ollama Endpoint
└─ "Ollama endpoint nedir?"
   ✅ "http://51.75.64.121:11434" demeli

TEST 5: Açıklama
└─ "Kaç paragraf açıklama yazmalıyım?"
   ✅ "3 paragraf, 200-250 kelime" demeli

🎯 5/5 TEST PASSED!
```

---

### **[5:00 - 5:30] Kapanış**

```
🎉 TEBRİKLER!

AI Asistanınız hazır!

Artık yapabilir:
✅ İlan başlığı (3 varyant, <2s)
✅ İlan açıklaması (profesyonel, <3s)
✅ Lokasyon analizi (skor + harf)
✅ Fiyat önerisi (3 seviye)
✅ Context7 uyumlu %100

Kullanım:
→ /stable-create sayfasında
→ "Başlık Üret" butonuyla
→ Veya direkt chat'te

📚 Detaylı bilgi:
docs/ai-training/README.md

İyi kullanımlar! 🚀

─────────────────
📹 Video Sonu
Beğen 👍 | Abone Ol 🔔
```

---

## 🎯 VIDEO EK BİLGİLER

### **Açıklama Kısmına Eklenecekler:**

```
🎓 Yalıhan Emlak AI Asistanı Kurulum Rehberi

Bu videoda:
✅ AnythingLLM workspace kurulumu
✅ 7 core doküman embedding
✅ System prompt ayarlama
✅ Test senaryoları
✅ Production'a geçiş

⏱️ Timeline:
0:00 Giriş
0:30 Workspace Oluşturma
1:30 Doküman Upload
3:00 System Prompt
4:00 Test Senaryoları
5:00 Kapanış

🔗 Kaynaklar:
- GitHub: /docs/ai-training/
- Quick Start: QUICK-START.md
- Checklist: 08-TRAINING-CHECKLIST.md

🤖 AI Provider:
Ollama gemma2:2b (Ücretsiz, Türkçe)
Endpoint: http://51.75.64.121:11434

📋 Özellikler:
- İlan başlığı (3 varyant, SEO optimize)
- Açıklama (200-250 kelime, profesyonel)
- Lokasyon analizi (skor + harf)
- Fiyat önerisi (3 seviye)
- Context7 %100 uyumlu

#AI #Emlak #AnythingLLM #Ollama #Türkçe
```

---

## 🎬 BONUS: SCREENSHOT'LAR

### **Screenshot 1: Workspace Created**

```
Gösterim: Workspace listesi
Vurgu: "Yalıhan Emlak AI" ismi
Caption: "Workspace başarıyla oluşturuldu ✅"
```

### **Screenshot 2: Documents Uploaded**

```
Gösterim: Document listesi
Vurgu: 7 dosya "✅ Embedded"
Caption: "Tüm dokümanlar yüklendi ve embed edildi ✅"
```

### **Screenshot 3: System Prompt**

```
Gösterim: System Prompt textarea
Vurgu: "Context7 %100 uy" satırı
Caption: "System prompt ayarlandı ✅"
```

### **Screenshot 4: Test Success**

```
Gösterim: Chat penceresi
Vurgu: AI'nin JSON formatında 3 başlık varyantı
Caption: "Test başarılı! AI çalışıyor ✅"
```

---

## 🎯 VIDEO SONRAKİ BÖLÜMLER (Seri)

### **Video 2: "AI ile İlan Oluşturma"**

```
Süre: 8 dakika
Konu: stable-create sayfasında AI kullanımı
İçerik:
  - Başlık üretme
  - Açıklama yazımı
  - Lokasyon analizi
  - Fiyat önerisi
```

### **Video 3: "AI Prompt Engineering"**

```
Süre: 10 dakika
Konu: Kendi prompt'larını yazma
İçerik:
  - Prompt anatomisi
  - Ton ayarlama
  - Varyant üretme
  - Fine-tuning
```

### **Video 4: "AI Performance Optimization"**

```
Süre: 7 dakika
Konu: AI performansını artırma
İçerik:
  - Cache stratejileri
  - Chunk optimization
  - Response time iyileştirme
  - Cost optimization
```

---

**🎬 Video transcript hazır! Çekim için kullanılabilir.**

**Hedef Kitle:** Emlak danışmanları, AI meraklıları  
**Platform:** YouTube, Vimeo  
**Dil:** Türkçe  
**Kalite:** HD (1080p)
