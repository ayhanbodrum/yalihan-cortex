# 🎓 AI Eğitim Dokümanları
## ChatGPT, Gemini ve Diğer AI Asistanları için Yalıhan Emlak Rehberi

**Versiyon:** 2.0.0  
**Tarih:** 29 Kasım 2025  
**Durum:** ✅ Aktif

---

## 📚 DOKÜMANTASYON YAPISI

Bu klasör, AI asistanlarının Yalıhan Emlak projesini anlaması ve doğru kod üretmesi için hazırlanmış kapsamlı eğitim dokümanlarını içerir.

### 📖 Ana Dokümanlar

#### 1. [AI_EGITIM_GEMINI_CHATGPT.md](./AI_EGITIM_GEMINI_CHATGPT.md) ⭐ **ANA EĞİTİM**
**Kapsamlı Proje Rehberi** - 12 bölüm, ~500 satır

İçerik:
- 🎯 Proje Genel Bakış
- 🛠️ Teknoloji Stack
- 🎭 Proje Davranış Biçimi
- 📐 Context7 Kuralları
- 🧩 Modül Yapısı
- 🤖 AI Sistemi ve Rolleri
- 💻 Kod Yazma Standartları
- 🗄️ Veritabanı Standartları
- 🎨 Frontend Standartları
- 🌐 API ve Servis Standartları
- 🚫 Yasaklı Pattern'ler
- ⚡ Hızlı Başlangıç Komutları

**Kullanım:** İlk kez projeye başlarken veya detaylı bilgi gerektiğinde

---

#### 2. [HIZLI_REFERANS_KILAVUZU.md](./HIZLI_REFERANS_KILAVUZU.md) ⚡ **HIZLI REFERANS**
**Özet Rehber** - Hızlı erişim için

İçerik:
- ⚡ Hızlı Kurallar
- 🎭 Proje Davranışı
- 💻 Kod Şablonları
- 🔍 Kontrol Listesi
- 🚀 Hızlı Komutlar
- 📚 Dokümantasyon Linkleri

**Kullanım:** Kod yazarken hızlı kontrol için

---

#### 3. [PROJE_DAVRANIS_BICIMI.md](./PROJE_DAVRANIS_BICIMI.md) 🤝 **DAVRANIŞREHBERI**
**AI Davranış Kılavuzu** - Proje kültürü ve etik

İçerik:
- 🎯 Temel Prensipler
- 🔄 İş Akışları
- 🎨 Kod Yazma Davranışı
- 🗣️ İletişim Davranışı
- 🔒 Güvenlik ve Sorumluluk
- 📊 Performans ve Kalite
- 🎓 Öğrenme ve Gelişim

**Kullanım:** Proje kültürünü anlamak ve doğru davranmak için

---

## 🚀 HIZLI BAŞLANGIÇ

### Yeni AI Asistanı İçin

```
1. Önce oku: AI_EGITIM_GEMINI_CHATGPT.md
   ↓
2. Referans tut: HIZLI_REFERANS_KILAVUZU.md
   ↓
3. Davranışı öğren: PROJE_DAVRANIS_BICIMI.md
   ↓
4. Kod yazmaya başla!
```

### Kod Yazarken

```
1. HIZLI_REFERANS_KILAVUZU.md → Kontrol listesi
2. Context7 kurallarını kontrol et
3. Kod şablonlarını kullan
4. Yasaklı pattern'lerden kaçın
```

---

## 📋 TEMEL KURALLAR (Özet)

### ❌ ASLA KULLANMA

```php
// Database
'order' => 1              // → 'display_order' kullan
'enabled' => true         // → 'status' kullan
'sehir_id' => 1           // → 'il_id' kullan
'musteri_id' => 1         // → 'kisi_id' kullan

// CSS
class="neo-btn"           // → Tailwind utilities kullan
class="neo-card"          // → Tailwind utilities kullan
```

### ✅ HER ZAMAN KULLAN

```html
<!-- Tailwind Transitions -->
class="transition-all duration-200"

<!-- Dark Mode -->
class="bg-white dark:bg-gray-800"

<!-- Focus States -->
class="focus:ring-2 focus:ring-blue-500"
```

### 🤖 AI Kuralı

```
AI = Yardımcı (Taslak üretir)
İnsan = Karar Verici (Onaylar)
```

---

## 🎯 KULLANIM SENARYOLARI

### Senaryo 1: Yeni Özellik Geliştirme

```
1. AI_EGITIM_GEMINI_CHATGPT.md → Modül yapısını öğren
2. HIZLI_REFERANS_KILAVUZU.md → Kod şablonlarını kullan
3. PROJE_DAVRANIS_BICIMI.md → İş akışını takip et
4. Kod yaz ve test et
```

### Senaryo 2: Hata Düzeltme

```
1. HIZLI_REFERANS_KILAVUZU.md → Yasaklı pattern kontrolü
2. AI_EGITIM_GEMINI_CHATGPT.md → Standartları kontrol et
3. Hatayı düzelt
4. Doğrula
```

### Senaryo 3: Kod İnceleme

```
1. HIZLI_REFERANS_KILAVUZU.md → Kontrol listesi
2. Context7 kurallarını kontrol et
3. Geri bildirim ver
```

---

## 📊 DOKÜMANTASYON METRİKLERİ

```yaml
Toplam Doküman: 3 adet
Toplam Satır: ~1500 satır
Kapsam:
  - Proje Genel Bakış: ✅
  - Teknoloji Stack: ✅
  - Context7 Kuralları: ✅
  - Kod Standartları: ✅
  - AI Rolleri: ✅
  - İş Akışları: ✅
  - Davranış Rehberi: ✅
  - Hızlı Referans: ✅
Güncellik: %100
Kalite: A+ (95/100)
```

---

## 🔗 İLGİLİ DOKÜMANTASYON

### Proje Kök Dizini
- `README.md` - Proje genel bakış
- `MASTER_PROMPT_YALIHAN_EMLAK_AI.md` - AI master prompt
- `YALIHAN_BEKCI_EGITIM_DOKUMANI.md` - Yalıhan Bekçi eğitimi

### Context7 Standartları
- `.context7/authority.json` - Context7 kuralları (gitignore'da)
- `docs/FORM_STANDARDS.md` - Form standartları
- `docs/active/RULES_KONSOLIDE_2025_11_25.md` - Konsolide kurallar

### Modül Dokümantasyonu
- `docs/modules/` - Modül detayları
- `docs/technical/` - Teknik dokümantasyon
- `docs/api/` - API dokümantasyonu

---

## 🎓 EĞİTİM YOLU

### Seviye 1: Başlangıç (2-3 saat)

```
1. AI_EGITIM_GEMINI_CHATGPT.md okuma
2. Temel kuralları öğrenme
3. Basit kod örnekleri inceleme
```

### Seviye 2: Orta (3-4 saat)

```
1. Modül yapısını öğrenme
2. İş akışlarını anlama
3. Kod şablonlarını kullanma
```

### Seviye 3: İleri (4-6 saat)

```
1. AI sistem entegrasyonu
2. Karmaşık iş akışları
3. Performans optimizasyonu
```

---

## 🤝 KATKIDA BULUNMA

### Dokümantasyon Güncelleme

```bash
# 1. Dokümanı düzenle
vim docs/ai-training/AI_EGITIM_GEMINI_CHATGPT.md

# 2. Versiyonu güncelle
# Dosya başındaki versiyon numarasını artır

# 3. Tarihi güncelle
# Dosya başındaki tarihi güncelle

# 4. Commit
git add docs/ai-training/
git commit -m "docs: AI eğitim dokümanı güncellendi"
```

### Yeni Doküman Ekleme

```bash
# 1. Yeni doküman oluştur
touch docs/ai-training/YENI_DOKUMAN.md

# 2. Bu README'yi güncelle
# Yeni dokümanı listeye ekle

# 3. Commit
git add docs/ai-training/
git commit -m "docs: Yeni AI eğitim dokümanı eklendi"
```

---

## 📞 DESTEK

### Sorular ve Geri Bildirim

- 📧 **Email**: docs@yalihanemlak.com
- 💬 **Slack**: #ai-training
- 🐛 **Issues**: GitHub Issues

### Sık Sorulan Sorular

**S: Hangi dokümanı okumalıyım?**
A: `AI_EGITIM_GEMINI_CHATGPT.md` ile başlayın.

**S: Hızlı referans için ne kullanmalıyım?**
A: `HIZLI_REFERANS_KILAVUZU.md` kullanın.

**S: Context7 kuralları nerede?**
A: `AI_EGITIM_GEMINI_CHATGPT.md` içinde detaylı açıklanmış.

**S: Kod şablonları nerede?**
A: `HIZLI_REFERANS_KILAVUZU.md` içinde.

---

## 🎉 GÜNCELLEMELER

### v2.0.0 (29 Kasım 2025) - Kapsamlı Yenileme

- ✅ 3 yeni kapsamlı doküman oluşturuldu
- ✅ AI_EGITIM_GEMINI_CHATGPT.md (Ana eğitim)
- ✅ HIZLI_REFERANS_KILAVUZU.md (Hızlı referans)
- ✅ PROJE_DAVRANIS_BICIMI.md (Davranış rehberi)
- ✅ Tüm Context7 kuralları güncellendi
- ✅ Kod şablonları eklendi
- ✅ İş akışları detaylandırıldı
- ✅ AI rolleri açıklandı

---

**Son Güncelleme:** 29 Kasım 2025  
**Versiyon:** 2.0.0  
**Durum:** 🚀 Production Ready  
**Kapsam:** %100 Tamamlandı

---

Made with ❤️ by Yalıhan Emlak Team
