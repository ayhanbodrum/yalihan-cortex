# 🎓 Yalıhan Emlak AI Eğitim Paketi - Master Document

**Version:** 1.0.0  
**Platform:** AnythingLLM Embedding  
**Son Güncelleme:** 11 Ekim 2025  
**Durum:** ✅ Production Ready

---

## 📋 EĞİTİM PAKETİ GENEL BAKIŞ

Bu doküman seti, **Yalıhan Emlak Warp Sistemi**'nin tüm AI özelliklerini, kurallarını ve kullanım senaryolarını içerir. AnythingLLM workspace'ine embed edilerek AI asistanın sistemi tam olarak öğrenmesi sağlanır.

---

## 🎯 SİSTEM KİMLİĞİ

### **Proje Adı:** Yalıhan Emlak Warp

### **Teknoloji Stack:**

- **Backend:** Laravel 10.x + PHP 8.2+
- **Frontend:** Blade + Alpine.js + Tailwind CSS
- **Database:** MySQL 8.0+
- **AI Stack:** 5 Provider (OpenAI, DeepSeek, Gemini, Claude, Ollama)
- **Design System:** Neo Design System
- **Compliance:** Context7 %100

### **AI Provider Detayları:**

#### **1. Ollama Local AI (Aktif - Varsayılan)**

- **Endpoint:** http://51.75.64.121:11434
- **Model:** gemma2:2b (2.6B parametreli, Türkçe destekli)
- **Kullanım:** İlan başlık/açıklama üretimi, lokasyon analizi, fiyat önerileri
- **Config:** `config/ai.php` → `ollama_api_url`, `ollama_model`

#### **2. OpenAI GPT-4**

- **Kullanım:** Karmaşık içerik üretimi, çoklu dil çevirisi
- **Config:** `OPENAI_API_KEY` environment variable

#### **3. Google Gemini**

- **Kullanım:** Görsel analiz, OCR, nesne tanıma
- **Model:** gemini-2.5-flash
- **Config:** `GOOGLE_API_KEY`, `GOOGLE_MODEL`

#### **4. Anthropic Claude**

- **Kullanım:** Kod review, kalite kontrolü
- **Config:** `ANTHROPIC_API_KEY`

#### **5. DeepSeek AI**

- **Kullanım:** Kod analizi ve optimizasyon
- **Config:** `DEEPSEEK_API_KEY`

---

## 🏗️ CORE SİSTEM MİMARİSİ

### **Ana Modüller:**

1. **İlan Yönetimi (Listings)**
    - Tablo: `ilanlar` (100+ alan)
    - Controller: `IlanController`, `SmartIlanController`
    - Routes: `/admin/ilanlar`, `/stable-create`, `/smart-create`
    - AI Features: Başlık, açıklama, fiyat önerisi

2. **CRM Sistemi**
    - Tablo: `kisiler` (Kişiler/Müşteriler)
    - AI Features: Müşteri segmentasyonu, talep eşleştirme
    - CRM Skoru: 100 puan skalasında

3. **Kategori Sistemi**
    - 3 Seviyeli: Ana Kategori → Alt Kategori → Yayın Tipi
    - Dinamik özellikler: Kategori bazlı
    - AI: Özellik önerileri

4. **Konum Sistemi**
    - Hiyerarşi: Ülke → İl → İlçe → Mahalle
    - Google Maps entegrasyonu
    - AI: Lokasyon analizi, POI önerileri

5. **Portal Entegrasyonu**
    - 6 Portal: Sahibinden, Hepsiemlak, Emlakjet, Zingat, Hürriyet Emlak, Emlak365
    - Portal-özel fiyatlandırma
    - Senkronizasyon durumu tracking

---

## 🎯 AI GÖREV VE YETKİLER

### **AI'nin Yapabileceği İşlemler:**

✅ **İçerik Üretimi:**

- İlan başlığı üretme (3-5 varyant)
- İlan açıklaması üretme (200-400 kelime)
- SEO meta tag önerileri
- Çoklu dil çevirisi (TR, EN, DE, RU, AR)

✅ **Analiz ve Öneriler:**

- Fiyat önerisi (3 seviye: Pazarlık, Piyasa, Premium)
- Lokasyon analizi (Skor, Harf Notu, Potansiyel)
- CRM müşteri profil analizi
- Portal önerileri

✅ **Görsel İşleme:**

- Fotoğraf kalite analizi
- OCR (tapu, belge okuma)
- Nesne tanıma (mobilya, mimari)

### **AI'nin YAPAMAYACAĞI İşlemler:**

❌ **Otomatik İşlemler (İnsan Onayı Gerekir):**

- Veritabanına otomatik kayıt
- Fiyat değiştirme
- İlanı yayınlama
- Portal'lara senkronizasyon
- Müşteri eşleştirme

❌ **Güvenlik:**

- Şifre, API key, gizli bilgilere erişim
- Kişisel veri (telefon, email) işleme
- Sistem dosyalarına erişim
- Veritabanı yapısını değiştirme

---

## 📊 VERİ YAPISI ve ALAN ADLARI

### **Context7 Zorunlu Alan Adları:**

✅ **DOĞRU Alan Adları:**

```yaml
status: status (NOT durum, is_active, aktif)
il_id: il_id (NOT sehir_id, region_id, city_id)
il: il (NOT sehir, region, city)
para_birimi: para_birimi (currency)
fiyat: fiyat (price)
baslik: baslik (title)
aciklama: aciklama (description)
metrekare: metrekare (square_meters)
oda_sayisi: oda_sayisi (room_count)
```

❌ **YASAK Alan Adları:**

```yaml
durum → status
is_active → status
aktif → status
sehir → il
sehir_id → il_id
region_id → (kaldırıldı)
ad_soyad → tam_ad
full_name → name
```

### **İlan Tablosu (ilanlar) - Core Fields:**

```yaml
# Temel
id, baslik, slug (unique), aciklama, status

# Kategori
ana_kategori_id, alt_kategori_id, yayin_tipi_id

# Fiyat
fiyat, para_birimi (TRY/USD/EUR/GBP)
baslangic_fiyati, gunluk_fiyat

# Lokasyon
il_id, ilce_id, mahalle_id, site_id
latitude, longitude, detayli_adres

# Kişiler
ilan_sahibi_id, danisman_id, ilgili_kisi_id

# Portal
sahibinden_id, hepsiemlak_id, emlakjet_id, zingat_id, hurriyetemlak_id
portal_sync_status (JSON), portal_pricing (JSON)

# Referans
referans_no (YE-SAT-YALKVK-DAİRE-001234)
dosya_adi (kullanıcı dostu isim)
```

---

## 🤖 AI PROMPT ŞABLONLARI

### **1. İlan Başlığı Üretimi:**

**Prompt Template:**

```
Sen bir emlak uzmanısın. Aşağıdaki bilgilere göre {tone} 3 farklı ilan başlığı oluştur.

Kategori: {kategori}
Yayın Tipi: {yayin_tipi}
Lokasyon: {lokasyon}
Fiyat: {fiyat}
Ton: {seo|kurumsal|hizli_satis|luks}

Kurallar:
- Her başlık 60-80 karakter arası
- Lokasyon mutlaka geçmeli
- SEO uyumlu anahtar kelimeler
- Sadece başlıkları yaz, numaralama yapma

Başlıklar:
```

**Örnek Çıktı:**

```
Yalıkavak Deniz Manzaralı Satılık Lüks Villa - 3.5M ₺
Bodrum Yalıkavak'ta Özel Havuzlu Satılık Villa
Yalıkavak Premium Lokasyonda Denize Sıfır Villa
```

### **2. İlan Açıklaması Üretimi:**

**Prompt Template:**

```
Sen profesyonel bir emlak danışmanısın. Aşağıdaki özellikte profesyonel ilan açıklaması yaz.

Kategori: {kategori}
Lokasyon: {lokasyon}
Fiyat: {fiyat}
Metrekare: {metrekare}
Özellikler: {ozellikler}
Ton: {tone}

Kurallar:
- 200-250 kelime
- 3 paragraf
- SEO uyumlu
- Lokasyon avantajlarını vurgula
- Özellikleri detaylandır

Açıklama:
```

### **3. Lokasyon Analizi:**

**Prompt Template:**

```
Sen bir gayrimenkul analistisin. Aşağıdaki lokasyon için kısa analiz:

Lokasyon: {il}, {ilce}, {mahalle}
Nearby POI: {poi_listesi}

Şu bilgileri ver:
Skor: 0-100 arası
Harf: A/B/C/D
Potansiyel: Yüksek/Orta/Düşük
Gerekçe: Kısa açıklama

Analiz:
```

### **4. Fiyat Önerisi:**

**Prompt Template:**

```
Girilen fiyat: {base_price} {currency}
Kategori: {kategori}
Lokasyon: {lokasyon}
Metrekare: {metrekare}

3 fiyat önerisi ver:
1. Pazarlık payı ile (10% düşük) - Hızlı satış için
2. Piyasa ortalaması (5% yüksek) - Bölge ortalamasına göre
3. Premium (15% yüksek) - Özel özellikler için

Her satırda: [Label]: [Fiyat] [Gerekçe]
```

---

## 🎨 TONE/STİL PROFİLLERİ

### **1. SEO Tone (Varsayılan):**

```
- Anahtar kelime yoğunluğu: %2-3
- Lokasyon vurgusu: Yüksek
- Özellikler: Detaylı listeleme
- CTA: Orta seviye
Örnek: "Bodrum Yalıkavak'ta Denize Sıfır Satılık Villa - 3 Yatak Odalı Lüks Konut"
```

### **2. Kurumsal Tone:**

```
- Dil: Profesyonel ve resmi
- Vurgu: Yatırım değeri
- Özellikler: Teknik detaylar
- CTA: Düşük
Örnek: "Yalıkavak Bölgesinde Yüksek Yatırım Getirili Villa Projesi"
```

### **3. Hızlı Satış Tone:**

```
- Dil: Aciliyet içeren
- Vurgu: Fırsat, indirim
- Özellikler: Avantajlar
- CTA: Yüksek
Örnek: "FIRSATTAN! Yalıkavak Denize Sıfır Villa - Hemen Görüşme!"
```

### **4. Lüks Tone:**

```
- Dil: Prestijli ve özel
- Vurgu: Kalite, ayrıcalık
- Özellikler: Premium detaylar
- CTA: Soft
Örnek: "Yalıkavak'ın En Prestijli Noktasında Eşsiz Villa - Exclusive Collection"
```

---

## 📐 KATEGORİ BAZLI ÖZELLİKLER

### **Arsa (Land):**

**Zorunlu Bilgiler:**

- Ada No, Parsel No
- İmar Durumu (İmarda, İmar Dışında, Tarla, vb.)
- KAKS, TAKS değerleri
- Alan (m² / Dönüm)

**AI Önerileri:**

- TKGM entegrasyonu önerisi
- Yatırım potansiyeli analizi
- İnşaat alanı hesaplama
- m² başı fiyat karşılaştırma

### **Villa:**

**Zorunlu Bilgiler:**

- Oda sayısı, banyo sayısı
- Arsa m², bina m²
- Havuz, bahçe durumu
- Denize uzaklık

**AI Önerileri:**

- Lüks özellikleri vurgula
- Manzara avantajları
- Özel hizmetler (havuz bakımı, güvenlik)

### **Daire (Apartment):**

**Zorunlu Bilgiler:**

- Oda sayısı (1+0, 1+1, 2+1, 3+1, vb.)
- Net m², brüt m²
- Kat, toplam kat
- Site özellikleri

**AI Önerileri:**

- Site avantajları
- Komşuluk bilgisi
- Ulaşım kolaylığı

### **Yazlık (Seasonal Rental):**

**Zorunlu Bilgiler:**

- Minimum konaklama günü
- Maksimum kişi sayısı
- Sezon fiyatları (yaz, kış, ara sezon)
- Dahil hizmetler

**AI Önerileri:**

- Sezonluk özellikler vurgula
- Aktivite önerileri
- Yaz/kış avantajları

---

## 💰 FİYAT SİSTEMİ

### **Para Birimleri:**

```json
{
    "TRY": { "symbol": "₺", "name": "Türk Lirası" },
    "USD": { "symbol": "$", "name": "Amerikan Doları" },
    "EUR": { "symbol": "€", "name": "Euro" },
    "GBP": { "symbol": "£", "name": "İngiliz Sterlini" }
}
```

### **Döviz Çevirimi:**

- **Servis:** `CurrencyRateService`
- **API:** Exchange Rate API
- **Cache:** 1 saat TTL
- **Fallback:** Varsayılan kurlar (TRY:1, USD:34.50, EUR:37.20, GBP:43.80)

### **Fiyat Analizi AI Kuralları:**

```
- m² başı fiyat hesapla
- Bölge ortalaması ile karşılaştır
- 3 seviyeli öneri sun:
  1. Pazarlık (-10%): Hızlı satış için
  2. Piyasa (+5%): Bölge ortalaması
  3. Premium (+15%): Özel özellikler için
```

---

## 🗺️ LOKASYON SİSTEMİ

### **Hiyerarşi:**

```
Türkiye
 ├── İl (81 il)
 │   ├── İlçe
 │   │   └── Mahalle
 │   │       └── Site/Apartman
```

### **Google Maps Entegrasyonu:**

- **Geocoding:** Adres → Koordinat
- **Reverse Geocoding:** Koordinat → Adres
- **Nearby POI:** 1km içinde (okul, hastane, market, park)
- **Mesafe Hesaplama:** Haversine formula

### **AI Lokasyon Analizi Kriterleri:**

```yaml
Skor Hesaplama (0-100):
    - Merkeze yakınlık: 25 puan
    - Altyapı (elektrik, su, doğalgaz): 20 puan
    - Ulaşım (toplu taşıma, otoyol): 20 puan
    - Sosyal tesis (okul, hastane, AVM): 20 puan
    - Yatırım potansiyeli: 15 puan

Harf Notu:
    - A: 85-100 (Mükemmel)
    - B: 70-84 (İyi)
    - C: 50-69 (Orta)
    - D: 0-49 (Düşük)

Potansiyel:
    - Yüksek: Gelişen bölge, yeni projeler, altyapı iyileştirmeleri
    - Orta: Stabil bölge, mevcut altyapı
    - Düşük: Durağan bölge, sınırlı gelişim
```

---

## 🎨 NEO DESIGN SYSTEM KURALLARI

### **Component Prefix:**

```
neo-* (ZORUNLU)
YASAK: btn-*, card-*, form-* (Bootstrap legacy)
```

### **Renk Paleti:**

```css
Primary: Orange (#f97316)
Success: Green (#10b981)
Warning: Yellow (#f59e0b)
Danger: Red (#ef4444)
Info: Cyan (#06b6d4)
```

### **Dark Mode:**

```
Tüm component'lerde dark: prefix zorunlu
Örnek: bg-white dark:bg-gray-800
```

---

## 🔐 GÜVENLİK ve COMPLIANCE

### **Context7 Kuralları:**

1. **Alan Adı Standardı:** İngilizce field adları (status, active, enabled)
2. **Türkçe Yasak:** durum, aktif, sehir (database field'larında)
3. **İlişki Adları:** il(), ilce(), mahalle() (NOT sehir(), bolge())
4. **Model Helper Metodlar:** Zorunlu (isActive(), isOwnerEligible(), getDisplayTextAttribute())

### **PII Maskeleme:**

```
Telefon: 0533 209 03 02 → 0533 XXX XX 02
Email: info@yalihanemlak.com → i***@yalihanemlak.com
Adres: Detaylı adres gösterilmez
```

### **Rate Limiting:**

```
AI Endpoints: 10 requests/minute/user
Public API: 60 requests/minute/IP
Admin API: 100 requests/minute/user
```

---

## 🎯 KULLANIM SENARYOLARI

### **Senaryo 1: İlan Oluşturma Asistanı**

**Kullanıcı:** "Yalıkavak'ta satılık villa için başlık öner"

**AI Yanıt:**

```
📌 3 Başlık Önerisi:

1. SEO Optimize: "Yalıkavak Deniz Manzaralı Satılık Villa - 5+2 Havuzlu Lüks Konut"
2. Kurumsal: "Bodrum Yalıkavak'ta Yüksek Standart Villa - Yatırım Fırsatı"
3. Lüks: "Yalıkavak'ın En Prestijli Noktasında Eşsiz Villa"

Hangi tonu tercih edersiniz?
```

### **Senaryo 2: Fiyat Danışmanlığı**

**Kullanıcı:** "3.5 milyon TL fiyat doğru mu?"

**AI Yanıt:**

```
💰 Fiyat Analizi:

Girilen Fiyat: 3.5M ₺
m² Başı: 14,000 ₺/m² (250 m² için)
Bölge Ortalaması: 13,500 ₺/m²

Önerilerim:
1. Pazarlık Payı: 3.15M ₺ (Hızlı satış için)
2. Piyasa: 3.5M ₺ ✅ (Bölge ortalamasına uygun)
3. Premium: 4M ₺ (Özel özelliklere uygun)

Değerlendirme: Fiyatınız bölge ortalamasına uygun. ✅
```

### **Senaryo 3: CRM Müşteri Profili**

**Kullanıcı:** "Bu müşteri için uygun ilanlar?"

**AI Yanıt:**

```
👤 Müşteri Profili:
CRM Skoru: 85/100
Bütçe: 2-3M ₺
Tercih: Bodrum, Yalıkavak, Gümüşlük
Kategori: Villa, Daire

🏠 Önerilen İlanlar (Top 3):
1. YE-SAT-YALKVK-VİLLA-001234 - 2.8M ₺ (Eşleşme: %92)
2. YE-SAT-GÜMSLK-DAİRE-005678 - 2.5M ₺ (Eşleşme: %88)
3. YE-SAT-BODRUM-VİLLA-003456 - 3.2M ₺ (Eşleşme: %85)
```

---

## 📱 API ENDPOINT'LER

### **AI Endpoints:**

```bash
# Başlık Üretimi
POST /stable-create/ai-suggest
Body: { action: "title", kategori: "...", lokasyon: "...", ai_tone: "seo" }

# Açıklama Üretimi
POST /stable-create/ai-suggest
Body: { action: "description", kategori: "...", metrekare: 150, ai_tone: "luks" }

# Lokasyon Analizi
POST /stable-create/ai-suggest
Body: { action: "location", latitude: 37.09, longitude: 27.43 }

# Fiyat Önerisi
POST /stable-create/ai-suggest
Body: { action: "price", fiyat: 2500000, kategori: "Villa" }

# AI İlan Geçmişi Analizi (YENİ - v3.4.0)
GET /api/kisiler/{id}/ai-gecmis-analiz
Response: { success: true, has_history: true, oneriler: [...], baslik_analizi: {...} }

# TKGM Parsel Sorgulama (YENİ - v3.4.0)
POST /api/tkgm/parsel-sorgu
Body: { ada: "126", parsel: "7", il: "Muğla", ilce: "Bodrum" }
Response: { success: true, parsel_bilgileri: {...}, hesaplamalar: {...}, oneriler: [...] }

# TKGM Yatırım Analizi (YENİ - v3.4.0)
POST /api/tkgm/yatirim-analizi
Body: { ada: "126", parsel: "7", il: "Muğla", ilce: "Bodrum" }
Response: { success: true, yatirim_analizi: { yatirim_skoru: 85, harf_notu: "A", ... } }

# Currency Rates
GET /api/currency/rates
Response: { rates: { TRY: 1, USD: 34.5, EUR: 37.2, GBP: 43.8 }, last_updated: "..." }
```

---

## 🎯 CONTEXT7 COMPLIANCE CHECKLIST

### **Her AI Yanıt İçin Kontrol Et:**

- [ ] Context7 uyumlu alan adları kullanıldı mı?
- [ ] Türkçe field adı yok mu? (durum, sehir, aktif)
- [ ] PII maskeleme yapıldı mı?
- [ ] Response format doğru mu? (JSON)
- [ ] Hata yönetimi var mı?
- [ ] Cache kullanımı uygun mu?
- [ ] Rate limit aşılmadı mı?

---

## 💡 BEKLENTİLER ve SINIRLAR

### **AI'den Beklenenler:**

✅ Hızlı ve doğru yanıtlar (<3 saniye)
✅ Context7 kurallarına uyum
✅ Türkçe dil desteği
✅ SEO optimize içerik
✅ Profesyonel ton

### **AI'nin Sınırları:**

❌ Otomatik kayıt yapamaz
❌ Fiyat değiştiremez
❌ Portal'lara yayınlayamaz
❌ Müşteri verilerini işleyemez
❌ Sistem ayarlarını değiştiremez

---

## 📚 REFERANS DOKÜMANLARI

**AnythingLLM'e Eklenecek Dosyalar:**

1. `00-ANYTHINGLLM-MASTER-TRAINING.md` (Bu dosya)
2. `01-AI-FEATURES-GUIDE.md` (AI özellikleri)
3. `02-CONTEXT7-RULES.md` (Kurallar)
4. `03-DATABASE-SCHEMA.md` (Veritabanı yapısı)
5. `04-PROMPT-TEMPLATES.md` (Prompt şablonları)
6. `05-USE-CASES.md` (Kullanım senaryoları)
7. `06-API-REFERENCE.md` (API dökümanı)

---

**🎓 Bu master doküman, AI asistanın Yalıhan Emlak sistemini %100 öğrenmesi için temel referanstır.**
