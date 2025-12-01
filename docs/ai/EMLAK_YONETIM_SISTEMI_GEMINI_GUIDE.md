# 🏢 EMLAK YÖNETİM SİSTEMİ - GOOGLE GEMINI İÇİN KAPSAMLI REHBER

**Tarih:** 2025-11-27  
**Hedef:** Google Gemini'ye sistem mimarisini açıklamak ve fikir geliştirmek  
**Sistem:** Yalıhan Emlak - Laravel 10 + Context7

---

## 📋 İÇİNDEKİLER

1. [Sistem Mimarisi](#sistem-mimarisi)
2. [Kategori Yapısı](#kategori-yapısı)
3. [Ana Kategoriler ve Alt Kategoriler](#ana-kategoriler-ve-alt-kategoriler)
4. [İlan Modeli ve Alanları](#ilan-modeli-ve-alanları)
5. [Kategoriye Özel Alanlar](#kategoriye-özel-alanlar)
6. [Dinamik Field Dependencies Sistemi](#dinamik-field-dependencies-sistemi)
7. [Modüller ve İlişkiler](#modüller-ve-ilişkiler)
8. [AI Entegrasyonu](#ai-entegrasyonu)
9. [Fikir Geliştirme Önerileri](#fikir-geliştirme-önerileri)

---

## 🏗️ SİSTEM MİMARİSİ

### Teknoloji Stack

```yaml
Backend:
  Framework: Laravel 10.x
  PHP: 8.1+
  Database: MySQL 8.0+
  Cache: Redis
  Queue: Database/Redis

Frontend:
  CSS: Tailwind CSS (ONLY - Bootstrap yasak)
  JS: Vanilla JavaScript + Alpine.js
  Maps: Leaflet.js + OpenStreetMap
  Build: Vite

AI & External:
  AI Providers: OpenAI, Claude, Gemini, DeepSeek, Ollama
  Context7: Dual System (Upstash + Yalıhan Bekçi)
  APIs: TCMB (Currency), TKGM (Land Registry), TurkiyeAPI
  Automation: n8n (Workflow)
```

### Mimari Yapı

```
┌─────────────────────────────────────────────────────────┐
│                    HTTP REQUEST                          │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│              LARAVEL ROUTES (web/api)                    │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│            CONTROLLERS (Admin/Api/Frontend)              │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│            SERVICES LAYER (Business Logic)               │
│  - YalihanCortex (AI Orchestration)                     │
│  - AIService (Multi-Provider AI)                        │
│  - LocationService                                      │
│  - PropertyValuationService                             │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│           ELOQUENT MODELS (Database ORM)                 │
│  - Ilan (Ana İlan Modeli)                               │
│  - IlanKategori (3 Seviyeli Kategori)                   │
│  - Kisi (Müşteri)                                       │
│  - Talep (Müşteri Talepleri)                            │
└─────────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────────┐
│              MYSQL DATABASE (yalihanemlak_ultra)         │
└─────────────────────────────────────────────────────────┘
```

---

## 📂 KATEGORİ YAPISI

### 3 Seviyeli Hiyerarşi

Sistem **3 seviyeli kategori hiyerarşisi** kullanır:

```
SEVİYE 0: ANA KATEGORİ (Ana Kategori)
  ├── SEVİYE 1: ALT KATEGORİ (Alt Kategori)
  │     └── SEVİYE 2: YAYIN TİPİ (Satılık/Kiralık)
  │
  └── ÖZEL: YAYIN TİPİ (ilan_kategori_yayin_tipleri tablosu)
```

### Veritabanı Yapısı

#### Tablo: `ilan_kategorileri`

```sql
- id (PK)
- name (varchar) - Kategori adı
- slug (varchar) - URL-friendly slug
- parent_id (FK -> ilan_kategorileri.id) - Üst kategori
- seviye (int) - 0: Ana, 1: Alt, 2: Yayın Tipi (deprecated)
- status (boolean) - Aktif/Pasif
- display_order (int) - Sıralama
- icon (varchar) - İkon
- aciklama (text) - Açıklama
- timestamps
```

#### Tablo: `ilan_kategori_yayin_tipleri` (Yeni Sistem)

```sql
- id (PK)
- kategori_id (FK -> ilan_kategorileri.id)
- yayin_tipi (varchar) - "Satılık", "Kiralık", "Günlük", vb.
- status (boolean)
- display_order (int)
- timestamps
```

### İlan Kategorisi İlişkileri

Her ilan 3 kategori alanına sahiptir:

```php
$ilan->ana_kategori_id  // Seviye 0: Ana Kategori
$ilan->alt_kategori_id  // Seviye 1: Alt Kategori
$ilan->yayin_tipi_id    // Yayın Tipi (ilan_kategori_yayin_tipleri)
```

---

## 🎯 ANA KATEGORİLER VE ALT KATEGORİLER

### 1. KONUT (Seviye 0)

**Amaç:** Daire, villa, müstakil ev gibi konut türleri

#### Alt Kategoriler (Seviye 1):

1. **Daire** (`slug: daire`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: `oda_sayisi`, `banyo_sayisi`, `net_m2`, `brut_m2`, `kat`, `toplam_kat`, `bina_yasi`, `isitma`, `esyali`, `aidat`

2. **Villa** (`slug: villa`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: Daire ile aynı + `bahce_m2`, `site_ozellikleri`

3. **Müstakil Ev** (`slug: mustakil-ev`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: Villa ile benzer

4. **Dubleks** (`slug: dubleks`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: Daire ile benzer

---

### 2. İŞYERİ (Seviye 0)

**Amaç:** Ofis, dükkan, fabrika gibi ticari alanlar

#### Alt Kategoriler (Seviye 1):

1. **Ofis** (`slug: ofis`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: `isyeri_tipi`, `kira_bilgisi`, `ciro_bilgisi`, `ruhsat_durumu`, `personel_kapasitesi`, `isyeri_cephesi`

2. **Dükkan** (`slug: dukkan`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: Ofis ile benzer

3. **Fabrika** (`slug: fabrika`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: `uretim_alani`, `depolama_alani`, `lojistik_ozellikleri`

4. **Depo** (`slug: depo`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: `kapasite_m3`, `klima`, `güvenlik`

---

### 3. ARSA (Seviye 0)

**Amaç:** İmar, tarım, orman arazileri

#### Alt Kategoriler (Seviye 1):

1. **İmar Arsaları** (`slug: imar-arsalari`)
   - Yayın Tipleri: Satılık
   - Özel Alanlar (16 adet):
     ```
     ada_no (varchar) - Ada numarası
     parsel_no (varchar) - Parsel numarası
     ada_parsel (varchar) - Ada-Parsel birleşik
     imar_statusu (varchar) - İmarlı/İmarsız/Villa İmarlı/Konut İmarlı/Ticari İmarlı
     alan_m2 (decimal) - Arsa alanı (m²)
     yola_cephe (boolean) - Yola cepheli mi?
     yola_cephesi (decimal) - Yola cephe mesafesi (m)
     altyapi_elektrik (boolean)
     altyapi_su (boolean)
     altyapi_dogalgaz (boolean)
     altyapi_kanalizasyon (boolean)
     kaks (decimal) - Kat Alanı Katsayısı (0.00-4.00+)
     taks (decimal) - Taban Alanı Katsayısı (0.00-0.71+)
     gabari (decimal) - Yükseklik sınırı (metre)
     taban_alani (decimal) - Taban alanı (m²)
     konum_avantajlari (json) - Denize yakın, marina yakını, vb.
     ```

2. **Tarım Arazileri** (`slug: tarim-arazileri`)
   - Yayın Tipleri: Satılık
   - Özel Alanlar: İmar arsaları ile benzer + `ekilebilir_alan`, `sulama_sistemi`

3. **Orman Arazileri** (`slug: orman-arazileri`)
   - Yayın Tipleri: Satılık
   - Özel Alanlar: `orman_yogunlugu`, `agac_turu`

---

### 4. YAZLIK KİRALAMA (Seviye 0)

**Amaç:** Günlük, haftalık, aylık yazlık kiralama

#### Alt Kategoriler (Seviye 1):

1. **Günlük Kiralama** (`slug: gunluk-kiralama`)
   - Yayın Tipleri: Günlük
   - Özel Alanlar (14 adet):
     ```
     gunluk_fiyat (decimal) - Günlük fiyat
     haftalik_fiyat (decimal) - Haftalık fiyat
     aylik_fiyat (decimal) - Aylık fiyat
     sezonluk_fiyat (decimal) - Sezonluk fiyat
     min_konaklama (int) - Minimum konaklama günü
     max_misafir (int) - Maksimum misafir sayısı
     temizlik_ucreti (decimal)
     havuz (boolean)
     havuz_turu (varchar) - Özel/Genel
     havuz_boyut (varchar)
     havuz_derinlik (decimal)
     sezon_baslangic (date)
     sezon_bitis (date)
     elektrik_dahil (boolean)
     su_dahil (boolean)
     ```

2. **Haftalık Kiralama** (`slug: haftalik-kiralama`)
   - Yayın Tipleri: Haftalık
   - Özel Alanlar: Günlük kiralama ile benzer

3. **Aylık Kiralama** (`slug: aylik-kiralama`)
   - Yayın Tipleri: Aylık
   - Özel Alanlar: Günlük kiralama ile benzer

#### Yazlık Kiralama Özel Tabloları:

**Tablo: `yazlik_fiyatlandirma`**
- Sezonluk fiyatlandırma (Yaz, Ara Sezon, Kış)
- `sezon_tipi`: enum('yaz', 'ara_sezon', 'kis')
- `gunluk_fiyat`, `haftalik_fiyat`, `aylik_fiyat`
- `minimum_konaklama`, `maksimum_konaklama`
- `ozel_gunler` (JSON)

**Tablo: `yazlik_rezervasyonlar`**
- Rezervasyon yönetimi
- `check_in`, `check_out`
- `misafir_sayisi`, `cocuk_sayisi`, `pet_sayisi`
- `status`: enum('beklemede', 'onaylandi', 'iptal', 'tamamlandi')

---

### 5. TURİSTİK TESİSLER (Seviye 0)

**Amaç:** Otel, pansiyon, tatil köyü gibi tesisler

#### Alt Kategoriler (Seviye 1):

1. **Otel** (`slug: otel`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: `oda_sayisi`, `yildiz`, `tesis_ozellikleri`

2. **Pansiyon** (`slug: pansiyon`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: `kapasite`, `kahvalti_dahil`

3. **Tatil Köyü** (`slug: tatil-koyu`)
   - Yayın Tipleri: Satılık, Kiralık
   - Özel Alanlar: `hektar`, `tesis_kapasitesi`, `aktivite_alanlari`

---

## 📊 İLAN MODELİ VE ALANLARI

### Temel İlan Alanları (Tüm Kategoriler İçin)

```php
// ✅ REQUIRED FIELDS
baslik (varchar) - İlan başlığı
aciklama (text) - İlan açıklaması
fiyat (decimal) - Ana fiyat
para_birimi (varchar) - TRY, USD, EUR, GBP
status (varchar) - 'Aktif', 'Pasif', 'Yayında', 'Satıldı', vb.

// Lokasyon (Context7: il_id, ilce_id, mahalle_id)
il_id (FK -> iller.id)
ilce_id (FK -> ilceler.id)
mahalle_id (FK -> mahalleler.id)
latitude (decimal) - Enlem
longitude (decimal) - Boylam

// Kategori İlişkileri
ana_kategori_id (FK -> ilan_kategorileri.id)
alt_kategori_id (FK -> ilan_kategorileri.id)
yayin_tipi_id (FK -> ilan_kategori_yayin_tipleri.id)

// İlişkisel
ilan_sahibi_id (FK -> kisiler.id)
danisman_id (FK -> users.id)
```

### Ortak Alanlar (Çoğu Kategori İçin)

```php
metrekare (decimal) - Genel metrekare
oda_sayisi (int) - Oda sayısı
banyo_sayisi (int) - Banyo sayısı
bina_yasi (int) - Bina yaşı
isitma (varchar) - Isıtma sistemi
esyali (boolean) - Eşyalı mı?
aidat (varchar) - Aylık aidat
krediye_uygun (boolean)
takasa_uygun (boolean)
```

---

## 🔧 KATEGORİYE ÖZEL ALANLAR

### ARSA İÇİN ÖZEL ALANLAR (16 Alan)

```php
// TKGM Entegrasyonu
ada_no (varchar) - Ada numarası
parsel_no (varchar) - Parsel numarası
ada_parsel (varchar) - Ada-Parsel birleşik

// İmar Bilgileri
imar_statusu (varchar) - 'imarli', 'imarsiz', 'villa_imarli', 'konut_imarli', 'ticari_imarli'
alan_m2 (decimal) - Arsa alanı (m²)

// Yol Bilgileri
yola_cephe (boolean) - Yola cepheli mi?
yola_cephesi (decimal) - Yola cephe mesafesi (metre)

// Altyapı
altyapi_elektrik (boolean)
altyapi_su (boolean)
altyapi_dogalgaz (boolean)
altyapi_kanalizasyon (boolean)

// İmar Katsayıları
kaks (decimal) - Kat Alanı Katsayısı (0.00-4.00+)
  - 0.00-0.50: Çok düşük yoğunluk (Villa)
  - 0.51-1.00: Düşük yoğunluk
  - 1.01-2.00: Orta yoğunluk (4-6 katlı)
  - 2.01-4.00: Yüksek yoğunluk (8-12 katlı)
  - 4.01+: Çok yüksek yoğunluk (Gökdelen)

taks (decimal) - Taban Alanı Katsayısı (0.00-0.71+)
  - 0.00-0.20: Minimum taban alanı (Geniş bahçe)
  - 0.21-0.35: Düşük taban alanı (Villa)
  - 0.36-0.50: Orta taban alanı (Standart konut)
  - 0.51-0.70: Yüksek taban alanı (Apartman)
  - 0.71+: Maksimum taban alanı (Ticari)

gabari (decimal) - Yükseklik sınırı (metre)
  - 0-6.5m: 1-2 kat
  - 6.51-9.5m: 2-3 kat
  - 9.51-12.5m: 3-4 kat
  - 12.51-15.5m: 4-5 kat
  - 15.51m+: 5+ kat

taban_alani (decimal) - Taban alanı (m²)

// Konum Avantajları (JSON)
konum_avantajlari (json) - [
  'denize_yakin',
  'deniz_manzarali',
  'marina_yakin',
  'golf_sahasi_yakin',
  'havaalani_yakin',
  ...
]
```

### YAZLIK KİRALAMA İÇİN ÖZEL ALANLAR (14 Alan)

```php
// Fiyatlandırma
gunluk_fiyat (decimal)
haftalik_fiyat (decimal)
aylik_fiyat (decimal)
sezonluk_fiyat (decimal)
temizlik_ucreti (decimal)

// Konaklama Kuralları
min_konaklama (int) - Minimum konaklama günü
max_misafir (int) - Maksimum misafir sayısı

// Havuz Bilgileri
havuz (boolean)
havuz_turu (varchar) - 'ozel', 'genel', 'infinity'
havuz_boyut (varchar)
havuz_derinlik (decimal)

// Sezon Bilgileri
sezon_baslangic (date)
sezon_bitis (date)

// Faturalar
elektrik_dahil (boolean)
su_dahil (boolean)
```

### KONUT (Daire/Villa) İÇİN ÖZEL ALANLAR

```php
oda_sayisi (int)
banyo_sayisi (int)
salon_sayisi (int)
net_m2 (decimal) - Net metrekare
brut_m2 (decimal) - Brüt metrekare
kat (int) - Bulunduğu kat
toplam_kat (int) - Bina toplam kat sayısı
bina_yasi (int) - Bina yaşı
isitma (varchar) - Isıtma sistemi
isinma_tipi (varchar) - Isıtma tipi
esyali (boolean) - Eşyalı mı?
site_ozellikleri (json) - Site içindeyse özellikler
aidat (varchar) - Aylık aidat
```

### İŞYERİ İÇİN ÖZEL ALANLAR

```php
isyeri_tipi (varchar) - 'ofis', 'dukkan', 'fabrika', 'depo'
kira_bilgisi (text) - Kira bilgileri
ciro_bilgisi (decimal) - Ciro bilgisi
ruhsat_durumu (varchar) - Ruhsat durumu
personel_kapasitesi (int)
isyeri_cephesi (int) - Cephe sayısı
```

---

## 🔗 DİNAMIK FIELD DEPENDENCIES SİSTEMİ

### Amaç

Her kategori ve yayın tipi için **dinamik form alanları** tanımlama sistemi.

### Tablo: `kategori_yayin_tipi_field_dependencies`

```sql
- id (PK)
- kategori_slug (varchar) - 'konut', 'arsa', 'yazlik-kiralama'
- yayin_tipi (varchar) - 'Satılık', 'Kiralık', 'Günlük'
- field_slug (varchar) - 'ada_no', 'parsel_no', 'havuz'
- field_name (varchar) - 'Ada Numarası', 'Havuz Var mı?'
- field_type (varchar) - 'text', 'number', 'boolean', 'select', 'textarea', 'date', 'price'
- field_category (varchar) - 'fiyat', 'ozellik', 'sezonluk', 'arsa'
- field_options (json) - Select için seçenekler
- field_unit (varchar) - 'TL', 'm²', 'km'
- field_icon (varchar) - '🏊', '💰'
- status (boolean) - Aktif/Pasif
- required (boolean) - Zorunlu mu?
- display_order (int) - Sıralama
- ai_auto_fill (boolean) - AI otomatik doldurma
- ai_suggestion (boolean) - AI öneri
- searchable (boolean) - Aramada kullanılabilir mi?
- show_in_card (boolean) - Liste kartında göster
```

### Örnek: Arsa için Field Dependencies

```php
[
  [
    'kategori_slug' => 'arsa',
    'yayin_tipi' => 'Satılık',
    'field_slug' => 'ada_no',
    'field_name' => 'Ada Numarası',
    'field_type' => 'text',
    'field_category' => 'arsa',
    'required' => true,
    'ai_auto_fill' => true, // TKGM'den otomatik çekilebilir
  ],
  [
    'field_slug' => 'imar_statusu',
    'field_name' => 'İmar Durumu',
    'field_type' => 'select',
    'field_options' => [
      'imarli' => 'İmarlı',
      'imarsiz' => 'İmarsız',
      'villa_imarli' => 'Villa İmarlı',
      'konut_imarli' => 'Konut İmarlı',
      'ticari_imarli' => 'Ticari İmarlı',
    ],
    'required' => true,
  ],
  [
    'field_slug' => 'kaks',
    'field_name' => 'KAKS (Kat Alanı Katsayısı)',
    'field_type' => 'number',
    'field_unit' => '',
    'ai_suggestion' => true, // AI ile önerilebilir
  ],
]
```

---

## 🧩 MODÜLLER VE İLİŞKİLER

### 1. EMLAK MODÜLÜ (Ana Modül)

**Kapsam:** İlan yönetimi, kategori yönetimi, özellik yönetimi

**Models:**
- `Ilan` - Ana ilan modeli
- `IlanKategori` - Kategori modeli
- `IlanKategoriYayinTipi` - Yayın tipi modeli
- `IlanFotografi` - Fotoğraf yönetimi
- `Ozellik` - Özellik modeli

**Controllers:**
- `IlanController` - İlan CRUD
- `IlanKategoriController` - Kategori yönetimi
- `PropertyTypeManagerController` - Kategori + Field Dependencies yönetimi

---

### 2. CRM MODÜLÜ

**Kapsam:** Müşteri yönetimi, talep yönetimi, eşleştirme

**Models:**
- `Kisi` - Müşteri modeli
- `Talep` - Talep modeli
- `IlanTalepEslesme` - Eşleştirme modeli

**AI Entegrasyonu:**
- Smart Property Matching (Yalihan Cortex)
- Customer Churn Analysis
- Opportunity Synthesis

---

### 3. ARSA MODÜLÜ

**Kapsam:** Arsa özel işlemleri, TKGM entegrasyonu, değerleme

**Models:**
- `ArsaCalculation` - Arsa hesaplamaları

**Services:**
- `PropertyValuationService` - Değerleme servisi
- `TKGMService` - TKGM entegrasyonu

**Özel Özellikler:**
- Ada-Parsel doğrulama
- İmar durumu analizi
- KAKS/TAKS hesaplamaları
- Değerleme tahmini

---

### 4. YAZLIK KİRALAMA MODÜLÜ

**Kapsam:** Sezonluk fiyatlandırma, rezervasyon yönetimi

**Models:**
- `YazlikFiyatlandirma` - Sezonluk fiyatlandırma
- `YazlikRezervasyon` - Rezervasyon modeli
- `Season` - Sezon modeli

**Özel Özellikler:**
- 3 sezon fiyatlandırması (Yaz, Ara Sezon, Kış)
- Rezervasyon çakışma kontrolü
- Otomatik fiyat hesaplama
- Müşteri bildirimleri

---

### 5. TAKIM YÖNETİMİ MODÜLÜ

**Kapsam:** Görev yönetimi, proje yönetimi, performans takibi

**Models:**
- `Gorev` - Görev modeli
- `Proje` - Proje modeli
- `TakimUyesi` - Takım üyesi

**Özellikler:**
- Deadline takibi
- Gecikme bildirimleri
- Telegram bot entegrasyonu
- n8n workflow entegrasyonu

---

### 6. FİNANS MODÜLÜ

**Kapsam:** Finansal işlemler, komisyon yönetimi

**Models:**
- `FinansalIslem` - Finansal işlem
- `Komisyon` - Komisyon modeli

**AI Entegrasyonu:**
- Finansal trend analizi
- Gelir/gider tahminleri
- Komisyon optimizasyonu

---

## 🤖 AI ENTEGRASYONU

### Yalihan Cortex (AI Orchestrator)

**Amaç:** Tüm AI işlemlerini koordine eden merkezi sistem

**Özellikler:**
1. **Smart Property Matching**
   - Talep ile ilan eşleştirme
   - Match skoru hesaplama
   - Öncelik sıralama

2. **Price Valuation**
   - Arsa değerleme
   - Konut değerleme
   - Piyasa analizi

3. **Customer Churn Analysis**
   - Müşteri risk analizi
   - Churn skoru hesaplama
   - Öneri üretme

4. **Voice-to-CRM**
   - Sesli komut → JSON dönüşümü
   - Kisi ve Talep taslağı oluşturma
   - NLP ile doğal dil işleme

5. **Content Generation**
   - İlan açıklaması üretme
   - SEO optimizasyonu
   - Çok dilli içerik

### AI Providers

```php
- OpenAI: GPT-3.5, GPT-4
- Claude: claude-3-sonnet
- Gemini: gemini-pro
- DeepSeek: deepseek-chat
- Ollama: Local models (mistral, llama2)
```

---

## 💡 FİKİR GELİŞTİRME ÖNERİLERİ

### 1. Kategori Bazlı AI Önerileri

**Arsa İçin:**
- Ada-Parsel numarasından otomatik TKGM veri çekme
- İmar durumuna göre yatırım potansiyeli analizi
- KAKS/TAKS değerlerine göre proje önerileri
- Konum avantajlarına göre fiyat tahmini

**Yazlık İçin:**
- Sezon bazlı dinamik fiyat önerileri
- Rezervasyon yoğunluğuna göre fiyat optimizasyonu
- Müşteri tercihlerine göre özellik önerileri
- Talep tahminleme (hangi tarihlerde yoğunluk)

**Konut İçin:**
- Özelliklere göre fiyat tahmini
- Benzer ilan karşılaştırması
- İyileştirme önerileri (değer artışı için)

### 2. Dinamik Form Sistemi İyileştirmeleri

- **AI Auto-Fill:** TKGM, Nominatim gibi servislerden otomatik veri çekme
- **AI Suggestions:** Piyasa verilerine göre fiyat önerileri
- **Conditional Fields:** Bir alan doldurulduğunda başka alanları aktif/pasif etme
- **Smart Validation:** Kategoriye özel akıllı validasyon kuralları

### 3. Kategori Entegrasyonu Önerileri

**Arsa + Konut:**
- Arsa üzerine konut projesi önerileri
- İmar durumuna göre proje maliyet tahmini

**Yazlık + Konut:**
- Yazlık olarak kullanılabilen konutlar
- İki amaçlı kullanım önerileri

**İşyeri + Konut:**
- Ticari + konut karışık projeler
- Zoning analizi

### 4. Pazar Analizi

- Kategori bazlı pazar trendleri
- Lokasyon bazlı fiyat haritası
- Sezon bazlı talep analizi (yazlık için)
- Karşılaştırmalı analiz (benzer ilanlar)

### 5. Müşteri Deneyimi

- Kategori bazlı arama filtreleri
- Görselleştirme (harita, grafik)
- Benzer ilan önerileri
- Favori kategoriler takibi

---

## 📚 ÖNEMLİ DOSYALAR

### Models
- `app/Models/Ilan.php` - Ana ilan modeli
- `app/Models/IlanKategori.php` - Kategori modeli
- `app/Models/IlanKategoriYayinTipi.php` - Yayın tipi modeli
- `app/Models/YazlikFiyatlandirma.php` - Yazlık fiyatlandırma
- `app/Models/YazlikRezervasyon.php` - Yazlık rezervasyon

### Controllers
- `app/Http/Controllers/Admin/IlanController.php`
- `app/Http/Controllers/Admin/PropertyTypeManagerController.php`
- `app/Http/Controllers/Api/CategoriesController.php`

### Services
- `app/Services/AI/YalihanCortex.php` - AI orchestrator
- `app/Services/PropertyValuationService.php` - Değerleme
- `app/Services/TKGMService.php` - TKGM entegrasyonu

### Config
- `config/arsa-dictionaries.php` - Arsa sözlükleri
- `database/seeders/IlanKategoriSeeder.php` - Kategori seeder

---

## 🎯 SONUÇ

Bu sistem, **kategorilere göre dinamik form alanları**, **AI destekli analiz**, ve **modüler mimari** ile emlak yönetimini kolaylaştırıyor.

**Temel Güçlü Yanlar:**
- ✅ 3 seviyeli esnek kategori yapısı
- ✅ Kategoriye özel alan sistemi (16 arsa, 14 yazlık alanı)
- ✅ Dinamik field dependencies
- ✅ AI entegrasyonu (Yalihan Cortex)
- ✅ Modüler mimari (14 modül)

**Geliştirilebilir Alanlar:**
- 🔄 Kategori bazlı AI önerileri
- 🔄 Daha akıllı form validasyonu
- 🔄 Görselleştirme iyileştirmeleri
- 🔄 Pazar analizi derinleştirme

---

**Hazırlayan:** Yalıhan Emlak AI System  
**Tarih:** 2025-11-27  
**Versiyon:** 1.0.0



