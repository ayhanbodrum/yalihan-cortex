# Danışman İlan Detayları - Görünür Veri Listesi

**Tarih:** 2 Aralık 2025  
**Sayfa:** `resources/views/admin/ilanlar/show.blade.php`  
**Yalıhan Bekçi Standardı:** YB-DANISMAN-VIEW-2025-12-02  
**Context7 Uyumlu:** %100

---

## 📊 DANIŞMAN İLAN DETAYLARINDA GÖREBILECEKLER

### 🎯 HEADER BOLÜMÜ

#### **1. İlan Başlığı ve Durum**
```
🏠 Yalıkavak'ta Satılık Lüks Daire  [Aktif]
```
- ✅ Başlık (baslik)
- ✅ Durum Badge (status: Aktif/Pasif/Taslak/Beklemede)

#### **2. Referans Badge (YENİ - 3 Katmanlı)**
```
[Ref: 001] ← Kısa referans (müşteri görür)

HOVER YAPINCA:
┌─────────────────────────────────────┐
│ TAM REFERANS:                       │
│ YE-SAT-YALKVK-DAİRE-001234         │
│                                     │
│ DETAY:                             │
│ Ref No: 001 Yalıkavak Satılık     │
│ Daire Ülkerler Sitesi (A. Yılmaz) │
│                                     │
│ DOSYA ADI:                         │
│ Ref YE-SAT-YALKVK-DAİRE-001234 -  │
│ Yalıkavak Satılık...               │
│                                     │
│ [📋 Detayı Kopyala] [📁 Dosya]    │
└─────────────────────────────────────┘
```

---

### 📈 İSTATİSTİKLER (4 Kart)

```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ 👁️ Görüntülenme │ ❤️ Favori      │ 💬 Mesaj       │ 🔄 Portal    │
│    1,234      │    45        │    12        │    3/5       │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

1. **Görüntülenme Sayısı** (goruntulenme)
2. **Favori Sayısı** (favorite_count)
3. **Mesaj Sayısı** (messages_count)
4. **Portal Sync** (Kaç portalde yayında)

---

### 📋 TEMEL BİLGİLER (Header'da)

```
┌─────────────────────────────────────────────┐
│ Kategori:    Daire                          │
│ Fiyat:       2.500.000 TRY                  │
│ Lokasyon:    Muğla, Bodrum                  │
│ Yayın Tipi:  Satılık                        │
└─────────────────────────────────────────────┘
```

---

### 🗂️ 7 SEKME SİSTEMİ

#### **SEKME 1: GENEL** ✅

**Portal ID'ler:**
```
┌─────────────────────────────────────────────┐
│ Sahibinden:      1001234567                 │
│ Emlakjet:        EJ-2025-001234             │
│ Hepsiemlak:      HE-001234                  │
│ Zingat:          ZNG-001234                 │
│ Hürriyet Emlak:  -                          │
└─────────────────────────────────────────────┘
```

Görülebilen Alanlar:
- ✅ `sahibinden_id`
- ✅ `emlakjet_id`
- ✅ `hepsiemlak_id`
- ✅ `zingat_id`
- ✅ `hurriyetemlak_id`

---

#### **SEKME 2: KİŞİLER** 👥

**İlan Sahibi:**
```
Ahmet Yılmaz
0532 123 45 67 • ahmet@email.com
```

**Danışman:**
```
Mehmet Kaya
0532 987 65 43 • mehmet.kaya@yalihan.com
```

**İlgili Kişi:**
```
Ayşe Demir
0533 111 22 33 • ayse@email.com
```

Görülebilen Alanlar:
- ✅ İlan Sahibi: `ilanSahibi->ad`, `soyad`, `telefon`, `email`
- ✅ Danışman: `userDanisman->name`, `email`, `phone_number`
- ✅ İlgili Kişi: `ilgiliKisi->ad`, `soyad`, `telefon`, `email`

---

#### **SEKME 3: SITE/APARTMAN** 🏢

```
Site/Apartman:  Ülkerler Sitesi
Adres:          Yalıkavak Mahallesi, Palmarina Yanı, Bodrum/Muğla
```

Görülebilen Alanlar:
- ✅ `site->name`
- ✅ `site->full_address`

---

#### **SEKME 4: FOTOĞRAFLAR** 📸

```
┌─────────┬─────────┬─────────┬─────────┐
│ [IMG1]  │ [IMG2]  │ [IMG3]  │ [IMG4]  │
│ Kapak   │         │         │         │
└─────────┴─────────┴─────────┴─────────┘
```

Görülebilen Alanlar:
- ✅ `fotograflar->dosya_yolu` (tüm fotoğraflar)
- ✅ `kapak_fotografi` (kapak işareti)

---

#### **SEKME 5: BELGELER** 📄

**Dosya Adı:**
```
Ref YE-SAT-YALKVK-DAİRE-001234 - Yalıkavak Satılık Daire...
```

**YouTube Video:**
```
https://youtube.com/watch?v=xxxxx
[Videoyu Aç]
```

**Sanal Tur:**
```
https://virtualtour.com/xxxxx
[Turu Aç]
```

**Dokümanlar Tablosu:**
```
┌──────────────────┬──────────┬──────────┬────────────┐
│ Başlık           │ Tür      │ Bağlantı │ Tarih      │
├──────────────────┼──────────┼──────────┼────────────┤
│ Tapu Belgesi     │ PDF      │ [Aç]     │ 02.12.2025 │
│ İmar Durumu      │ Image    │ [İndir]  │ 01.12.2025 │
└──────────────────┴──────────┴──────────┴────────────┘
```

Görülebilen Alanlar:
- ✅ `dosya_adi` (Dosya adı - kopyalanabilir)
- ✅ `youtube_video_url` (YouTube linki)
- ✅ `sanal_tur_url` (Sanal tur linki)
- ✅ `documents->title`, `type`, `url`, `path`, `created_at` (tablo)

---

#### **SEKME 6: ARKA PLAN** 🔒

**⚠️ YETKİ GEREKTİRİR:** `can:view-private-listing-data`

**Mahrem Bilgiler:**
```
┌─────────────────────────────────────────────┐
│ İstenen Fiyat Min:   2.000.000 TRY         │
│ İstenen Fiyat Max:   2.800.000 TRY         │
│ Özel Notlar:         Pazarlık payı %10     │
└─────────────────────────────────────────────┘
```

Görülebilen Alanlar (Yetkili Danışman):
- ✅ `owner_private_data->desired_price_min` (İstenen min fiyat)
- ✅ `owner_private_data->desired_price_max` (İstenen max fiyat)
- ✅ `owner_private_data->notes` (Özel notlar - encrypted)

**⚠️ Yetki Yoksa:**
```
Arka plan bilgileri için yetki gerekli
```

---

#### **SEKME 7: GEÇMİŞ** 📊

**Fiyat Geçmişi Tablosu:**
```
┌────────────┬────────────┬─────────────────────┐
│ Tarih      │ Fiyat      │ Not                 │
├────────────┼────────────┼─────────────────────┤
│ 02.12.2025 │ 2.500.000₺ │ İlk ilan oluşturma  │
│ 01.12.2025 │ 2.700.000₺ │ Fiyat güncelleme    │
│ 28.11.2025 │ 3.000.000₺ │ İlk kayıt           │
└────────────┴────────────┴─────────────────────┘
```

**Fiyat Grafiği:**
```
3.0M │               ●
     │              ╱
2.7M │            ●
     │           ╱
2.5M │         ●
     │_________________________
       28.11   01.12   02.12
```

Görülebilen Alanlar:
- ✅ `fiyatGecmisi->created_at` (Tarih)
- ✅ `fiyatGecmisi->fiyat` (Fiyat)
- ✅ `fiyatGecmisi->notlar` (Not)
- ✅ Fiyat grafiği (SVG - son 20 kayıt)

---

### 🎮 HIZLI İŞLEMLER (Quick Actions)

Danışman bu butonları görür:

```
┌────────────────────────────────────────────┐
│ [✏️ İlanı Düzenle]                         │
│ [📋 İlanı Kopyala]                         │
│ [🔄 Durum Değiştir]                        │
│ [🤖 AI Analiz]                             │
└────────────────────────────────────────────┘
```

1. **İlanı Düzenle** → Edit sayfasına yönlendirir
2. **İlanı Kopyala** → Taslak olarak kopyalar
3. **Durum Değiştir** → Aktif ↔ Pasif
4. **AI Analiz** → YalihanCortex ile fiyat/SEO analizi

---

### 🗺️ NEVİGASYON

```
[← Önceki İlan]                    [Sonraki İlan →]
```

- ✅ `previousIlan` (önceki ilan linki)
- ✅ `nextIlan` (sonraki ilan linki)

---

## 📱 "İLANLARIM" SAYFASINDA GÖREBILECEKLER

**Sayfa:** `resources/views/admin/ilanlar/my-listings.blade.php`

### İstatistik Kartları (4 Kart)

```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ 📊 Toplam    │ ✅ Aktif      │ ⏳ Bekleyen   │ 👁️ Toplam    │
│    İlan      │   İlanlar    │              │ Görüntülenme │
│     15       │     12       │      2       │    5,432     │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

### İlan Kartları (Gemini AI - Güncellendi)

**Her kartta:**

```
┌──────────────────────────────────────────────┐
│ [Ref: 001]                   2.500.000 ₺    │
│                                              │
│ Yalıkavak'ta Satılık Lüks Daire             │
│ [Ülkerler Sitesi] Bodrum / Muğla            │
│ 👤 Ahmet Yılmaz                              │
│ [Aktif] [Düzenle] [Detay]                   │
│ ━━━━━━━━━━━━━━━━━━━ 45% ━━                  │
└──────────────────────────────────────────────┘
```

**Görülebilen Bilgiler:**
1. **Referans Badge** (hover ile detaylar)
2. **Fiyat** (number_format ile)
3. **Başlık**
4. **Site** (badge)
5. **Lokasyon** (İlçe / İl)
6. **İlan Sahibi** (ad soyad)
7. **Durum Badge** (Aktif/Pasif/Taslak)
8. **İşlem Butonları** (Düzenle, Detay)
9. **Görüntülenme Çubuğu** (progress bar)

---

## 📋 DETAYLI VERİ LİSTESİ (7 SEKME)

### **SEKME 1: GENEL** 📊

| Alan | Database Field | Örnek |
|------|----------------|-------|
| Sahibinden ID | `sahibinden_id` | 1001234567 |
| Emlakjet ID | `emlakjet_id` | EJ-2025-001234 |
| Hepsiemlak ID | `hepsiemlak_id` | HE-001234 |
| Zingat ID | `zingat_id` | ZNG-001234 |
| Hürriyet Emlak ID | `hurriyetemlak_id` | - |

**Toplam:** 5 alan

---

### **SEKME 2: KİŞİLER** 👥

**İlan Sahibi:**
| Alan | Database Field | Örnek |
|------|----------------|-------|
| Ad Soyad | `ilanSahibi->ad`, `soyad` | Ahmet Yılmaz |
| Telefon | `ilanSahibi->telefon` | 0532 123 45 67 |
| Email | `ilanSahibi->email` | ahmet@email.com |

**Danışman:**
| Alan | Database Field | Örnek |
|------|----------------|-------|
| Ad | `userDanisman->name` | Mehmet Kaya |
| Email | `userDanisman->email` | mehmet.kaya@yalihan.com |
| Telefon | `userDanisman->phone_number` | 0532 987 65 43 |

**İlgili Kişi:**
| Alan | Database Field | Örnek |
|------|----------------|-------|
| Ad Soyad | `ilgiliKisi->ad`, `soyad` | Ayşe Demir |
| Telefon | `ilgiliKisi->telefon` | 0533 111 22 33 |
| Email | `ilgiliKisi->email` | ayse@email.com |

**Toplam:** 9 alan

---

### **SEKME 3: SITE/APARTMAN** 🏢

| Alan | Database Field | Örnek |
|------|----------------|-------|
| Site Adı | `site->name` | Ülkerler Sitesi |
| Tam Adres | `site->full_address` | Yalıkavak Mahallesi, Palmarina Yanı... |

**Toplam:** 2 alan

---

### **SEKME 4: FOTOĞRAFLAR** 📸

| Alan | Database Field | Açıklama |
|------|----------------|----------|
| Fotoğraf Grid | `fotograflar->dosya_yolu` | Tüm fotoğraflar grid'de gösterilir |
| Kapak İşareti | `kapak_fotografi` | Kapak fotoğrafı işaretlenir |

**Görsel:** 2x4 veya 4x4 grid (responsive)

---

### **SEKME 5: BELGELER** 📄

**Dosya Bilgileri:**
| Alan | Database Field | Örnek |
|------|----------------|-------|
| Dosya Adı | `dosya_adi` | Ref YE-SAT-YALKVK-DAİRE-001234 -... |
| YouTube Video | `youtube_video_url` | https://youtube.com/watch?v=xxxxx |
| Sanal Tur | `sanal_tur_url` | https://virtualtour.com/xxxxx |

**Doküman Tablosu:**
| Alan | Database Field | Örnek |
|------|----------------|-------|
| Başlık | `documents->title` | Tapu Belgesi |
| Tür | `documents->type` | PDF |
| Bağlantı | `documents->url` / `path` | [Aç] / [İndir] |
| Tarih | `documents->created_at` | 02.12.2025 14:30 |

**+ Doküman Yükleme Formu** (başlık, tür, URL, dosya)

**Toplam:** 3 + Doküman tablosu

---

### **SEKME 6: ARKA PLAN** 🔒

**⚠️ YETKİ GEREKTİRİR:** `can:view-private-listing-data`

**Mahrem Bilgiler (Encrypted):**
| Alan | Database Field | Örnek |
|------|----------------|-------|
| İstenen Fiyat Min | `owner_private_data->desired_price_min` | 2.000.000 TRY |
| İstenen Fiyat Max | `owner_private_data->desired_price_max` | 2.800.000 TRY |
| Özel Notlar | `owner_private_data->notes` | Pazarlık payı %10, acil satış değil |

**Toplam:** 3 alan (yetkili kullanıcı için)

---

### **SEKME 7: GEÇMİŞ** 📈

**Fiyat Geçmişi Tablosu:**
| Alan | Database Field | Örnek |
|------|----------------|-------|
| Tarih | `fiyatGecmisi->created_at` | 02.12.2025 14:30 |
| Fiyat | `fiyatGecmisi->fiyat` | 2.500.000 TRY |
| Not | `fiyatGecmisi->notlar` | Fiyat düşürüldü |

**Fiyat Grafiği:**
- ✅ SVG line chart (son 20 kayıt)
- ✅ Otomatik scaling
- ✅ Responsive

**Toplam:** 1 tablo + 1 grafik

---

## 🎯 TOPLAM VERİ SAYISI

| Bölüm | Alan Sayısı |
|-------|-------------|
| **Header** | 6 (başlık, durum, kategori, fiyat, lokasyon, yayın tipi) |
| **İstatistikler** | 4 (görüntülenme, favori, mesaj, portal) |
| **Referans Badge** | 3 (kısa, orta, uzun referans) |
| **Genel Sekme** | 5 (portal ID'ler) |
| **Kişiler Sekme** | 9 (ilan sahibi, danışman, ilgili kişi) |
| **Site Sekme** | 2 (site adı, adres) |
| **Fotoğraflar Sekme** | ∞ (tüm fotoğraflar) |
| **Belgeler Sekme** | 3 + ∞ (dosya adı, YouTube, sanal tur + dokümanlar) |
| **Arka Plan Sekme** | 3 (mahrem bilgiler - yetkili için) |
| **Geçmiş Sekme** | ∞ (fiyat geçmişi + grafik) |
| **Quick Actions** | 4 (düzenle, kopyala, durum değiştir, AI analiz) |
| **Navigasyon** | 2 (önceki, sonraki ilan) |

**TOPLAM:** **35+ alan** + Dinamik içerikler (fotoğraf, doküman, fiyat geçmişi)

---

## 🔍 ARAMA ÖZELLİĞİ (BONUS)

Danışman arama kutusuna şunları yazabilir:

✅ **Referans No:** `001`, `234`, `YE-SAT-YALKVK-DAİRE-001234`  
✅ **Portal ID:** `1001234567` (Sahibinden)  
✅ **Telefon:** `0532 123 45 67`  
✅ **Email:** `ahmet@email.com`  
✅ **Site Adı:** `Ülkerler Sitesi`  
✅ **Danışman Adı:** `Mehmet Kaya`  
✅ **Başlık:** `Yalıkavak Satılık`  
✅ **Dosya Adı:** `Ref YE-SAT-YALKVK...`

**Sonuç:** İlanı hızlıca bulur! 🎯

---

## 💡 KULLANIM SENARYOLARı

### **Senaryo 1: Müşteri Arama**
1. Müşteri: "001 numaralı ilandan bahsediyorum"
2. Danışman: Arama kutusuna `001` yazar → İlanı bulur
3. İlan detayına tıklar
4. **Kişiler** sekmesinde "Ahmet Yılmaz" görür
5. Telefon numarasını görür ve arar ✅

### **Senaryo 2: Dosya Oluşturma**
1. Danışman ilan detayında
2. Referans badge'e hover yapar
3. **"Dosya Adı"** butonuna tıklar
4. Clipboard: `Ref YE-SAT-YALKVK-DAİRE-001234 - Yalıkavak...`
5. Word/Excel'de dosya oluşturur ✅

### **Senaryo 3: Fiyat Analizi**
1. Danışman **Geçmiş** sekmesine gider
2. Fiyat geçmişi tablosuna bakar
3. Grafikte fiyat düşüşünü görür
4. Pazarlık stratejisi belirler ✅

### **Senaryo 4: Portal Takibi**
1. Danışman **Genel** sekmesinde
2. Portal ID'leri görür
3. Hangi portallarda yayında olduğunu kontrol eder
4. Eksik portal varsa ekler ✅

---

## 🎨 CONTEXT7 UYUMLU TASARIM

### ✅ Tailwind CSS:
- Gradient backgrounds
- Dark mode support
- Responsive grid
- Hover effects
- Transition animations

### ✅ Vanilla JavaScript:
- Clipboard API
- Toast notifications
- Tab system (Alpine.js)
- Event listeners

### ❌ Forbidden Pattern Yok:
- Bootstrap kullanılmıyor
- Neo Design System kullanılmıyor
- jQuery kullanılmıyor

---

## 📊 ÖZET

**Danışman ilan detaylarında görebilir:**

### **PUBLIC BİLGİLER (Herkes):**
- ✅ 35+ alan
- ✅ 7 sekme
- ✅ 4 istatistik kartı
- ✅ Fotoğraflar
- ✅ Dokümanlar
- ✅ Fiyat geçmişi + grafik

### **MAHREM BİLGİLER (Yetkili):**
- 🔒 İstenen fiyat aralığı
- 🔒 Özel notlar (encrypted)

### **HIZLI İŞLEMLER:**
- ✏️ Düzenle
- 📋 Kopyala
- 🔄 Durum değiştir
- 🤖 AI analiz

### **REFERANS SİSTEMİ (YENİ - Gemini AI):**
- ✅ Kısa referans: `Ref: 001`
- ✅ Orta referans: `Ref No: 001 Yalıkavak...` (hover)
- ✅ Uzun referans: `Ref YE-SAT-YALKVK-DAİRE-001234 -...` (dosya)

---

**Rapor Tarihi:** 2 Aralık 2025  
**Yalıhan Bekçi Onayı:** ✅ Onaylandı  
**Context7 Compliance:** ✅ %100  
**Gemini AI Integration:** ✅ Tam uyumlu

