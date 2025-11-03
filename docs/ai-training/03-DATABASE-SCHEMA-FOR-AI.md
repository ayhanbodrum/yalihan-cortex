# 🗄️ Database Schema - AI için Basitleştirilmiş

**AnythingLLM Training Module 3**  
**Version:** 1.0.0

---

## 📋 CORE TABLOLAR

### **1. ilanlar (Listings)**

**Açıklama:** Ana ilan tablosu - Tüm emlak ilanları

**Önemli Kolonlar:**
```yaml
# Temel Bilgiler
id: bigint (Primary Key)
baslik: varchar(255) - İlan başlığı
slug: varchar(255) UNIQUE - URL için
aciklama: text - İlan açıklaması
status: enum('Taslak','Aktif','Pasif','Beklemede')

# Kategori
ana_kategori_id: bigint → ilan_kategorileri.id
alt_kategori_id: bigint → ilan_kategorileri.id
yayin_tipi_id: bigint → ilan_kategorileri.id

# Fiyat
fiyat: decimal(15,2) - Ana fiyat
para_birimi: enum('TRY','USD','EUR','GBP')
baslangic_fiyati: decimal(15,2) - Pazarlık fiyatı
gunluk_fiyat: decimal(10,2) - Günlük kiralama

# Lokasyon
il_id: bigint → iller.id
ilce_id: bigint → ilceler.id
mahalle_id: bigint → mahalleler.id
site_id: bigint → sites.id
latitude: decimal(10,8) - Enlem
longitude: decimal(11,8) - Boylam
detayli_adres: text

# Kişiler
ilan_sahibi_id: bigint → kisiler.id (Mal sahibi)
danisman_id: bigint → users.id (Danışman)
ilgili_kisi_id: bigint → kisiler.id (İlgili kişi)

# Portal IDs
sahibinden_id: varchar(50)
hepsiemlak_id: varchar(50)
emlakjet_id: varchar(50)
zingat_id: varchar(50)
hurriyetemlak_id: varchar(50)
portal_sync_status: json
portal_pricing: json

# Referans
referans_no: varchar(50) UNIQUE - YE-SAT-YALKVK-DAİRE-001234
dosya_adi: varchar(255) - Kullanıcı dostu isim

# Özellikler
metrekare: decimal(10,2)
oda_sayisi: varchar(20) - "3+1"
kat: int
toplam_kat: int

# Meta
created_at: timestamp
updated_at: timestamp
deleted_at: timestamp (soft delete)
```

**AI Kullanım Örneği:**
```
"Bu ilan için başlık öner" → baslik field'ına yazılacak
"Açıklama üret" → aciklama field'ına yazılacak
"Fiyat öner" → fiyat field'ına yazılacak
```

---

### **2. kisiler (People/Customers)**

**Açıklama:** CRM kişi/müşteri tablosu

**Kolonlar:**
```yaml
id: bigint
ad: varchar(255) - Ad
soyad: varchar(255) - Soyad
tam_ad: ACCESSOR (ad + ' ' + soyad)
telefon: varchar(50)
telefon_2: varchar(50)
email: varchar(255)
musteri_tipi: enum('ev_sahibi','satici','alici','kiraci','yatirimci')
status: enum('Aktif','Pasif','Beklemede')
danisman_id: bigint → users.id
il_id: bigint → iller.id
ilce_id: bigint → ilceler.id
mahalle_id: bigint → mahalleler.id
```

**AI CRM Analizi:**
```
CRM Skoru: 0-100 (calculated)
  - İlan sayısı: 30 puan
  - Başarılı satış: 30 puan
  - Aktiflik: 20 puan
  - Bütçe uyumu: 20 puan
```

---

### **3. ilan_kategorileri (Categories)**

**Açıklama:** 3 seviyeli kategori sistemi

**Kolonlar:**
```yaml
id: bigint
parent_id: bigint (NULL = ana kategori)
name: varchar(255) - Kategori adı
slug: varchar(255)
seviye: int (1=Ana, 2=Alt, 3=Yayın Tipi)
status: enum('Aktif','Pasif')
```

**Hiyerarşi:**
```
Konut (parent_id=NULL, seviye=1)
  ├── Villa (parent_id=1, seviye=2)
  │   ├── Satılık (parent_id=2, seviye=3)
  │   └── Kiralık (parent_id=2, seviye=3)
  └── Daire (parent_id=1, seviye=2)
      ├── Satılık
      └── Kiralık
```

---

### **4. users (Danışmanlar/Adminler)**

**Kolonlar:**
```yaml
id: bigint
name: varchar(255) - Tam ad
email: varchar(255) UNIQUE
password: string (hashed)
status: boolean - Aktif mi?
role: Spatie Permission (danisman, admin, super_admin)
```

**Danışman Filtreleme (AI için önemli):**
```php
// ✅ DOĞRU
User::whereHas('roles', function($q) {
    $q->where('name', 'danisman');
})->get()

// ❌ YASAK
User::all()  // Tüm kullanıcıları getirme
User::role('danisman')  // Static call yasak
```

---

### **5. iller, ilceler, mahalleler (Locations)**

**iller:**
```yaml
id: bigint
il_adi: varchar(255) - "Muğla"
plaka_kodu: varchar(3) - "48"
status: boolean
```

**ilceler:**
```yaml
id: bigint
il_id: bigint → iller.id
ilce_adi: varchar(255) - "Bodrum"
status: boolean
```

**mahalleler:**
```yaml
id: bigint
ilce_id: bigint → ilceler.id
mahalle_adi: varchar(255) - "Yalıkavak"
status: boolean
```

---

### **6. sites (Site/Apartman)**

**Kolonlar:**
```yaml
id: bigint
name: varchar(255) - Site adı
address: text - Adres
il_id: bigint → iller.id
ilce_id: bigint → ilceler.id
mahalle_id: bigint → mahalleler.id
active: boolean
lat: decimal(10,8)
lng: decimal(11,8)
```

---

## 🔗 İLİŞKİ ŞEMASI

### **İlan İlişkileri:**

```
ilanlar
├── ana_kategori → ilan_kategorileri (parent)
├── alt_kategori → ilan_kategorileri (child)
├── yayin_tipi → ilan_kategorileri (grandchild)
├── il → iller
├── ilce → ilceler
├── mahalle → mahalleler
├── site → sites
├── ilan_sahibi → kisiler
├── danisman → users
└── ozellikler → ilan_ozellik (many-to-many)
```

### **Kişi İlişkileri:**

```
kisiler
├── danisman → users
├── il → iller
├── ilce → ilceler
├── mahalle → mahalleler
└── ilanlar → ilanlar (hasMany)
```

---

## 📊 ÖRNEK SORGULAR (AI için)

### **İlan Arama:**

```sql
-- Yalıkavak'taki satılık villalar
SELECT * FROM ilanlar
WHERE alt_kategori_id = (SELECT id FROM ilan_kategorileri WHERE name = 'Villa')
AND yayin_tipi_id = (SELECT id FROM ilan_kategorileri WHERE name = 'Satılık')
AND ilce_id = (SELECT id FROM ilceler WHERE ilce_adi = 'Bodrum')
AND mahalle_id = (SELECT id FROM mahalleler WHERE mahalle_adi = 'Yalıkavak')
AND status = 'Aktif';
```

### **Kişi Analizi:**

```sql
-- Bir kişinin tüm ilanları ve CRM skoru
SELECT 
  k.ad, k.soyad,
  COUNT(i.id) as toplam_ilan,
  AVG(i.fiyat) as ortalama_fiyat,
  MAX(i.created_at) as son_ilan_tarihi
FROM kisiler k
LEFT JOIN ilanlar i ON k.id = i.ilan_sahibi_id
WHERE k.id = 123
GROUP BY k.id;
```

---

## 🎯 AI İÇİN ALAN KULLANIM REHBERİ

### **Başlık Üretirken Kullan:**
```yaml
Zorunlu:
  - alt_kategori (kategori adı)
  - yayin_tipi (Satılık/Kiralık)
  - lokasyon (il + ilce veya il + ilce + mahalle)
  
Opsiyonel:
  - fiyat (ton'a göre)
  - oda_sayisi
  - metrekare
  - öne çıkan özellik (deniz manzarası, havuzlu)
```

### **Açıklama Üretirken Kullan:**
```yaml
Zorunlu:
  - kategori
  - lokasyon
  - fiyat
  - metrekare
  
Önerilen:
  - oda_sayisi
  - kat_bilgisi
  - site_adi
  - ozellikler (array)
  - yakin_yerler (POI)
```

### **Fiyat Önerirken Kullan:**
```yaml
Zorunlu:
  - fiyat (base price)
  - metrekare
  - kategori
  
Önerilen:
  - lokasyon (bölge ortalaması için)
  - bina_yasi
  - ozellikler
```

---

## 🔍 ÖZEL ALAN AÇIKLAMALARI

### **referans_no (Referans Numarası):**
```
Format: YE-{YAYINTIPI}-{LOKASYON}-{KATEGORI}-{SIRANO}

Örnekler:
  YE-SAT-YALKVK-DAİRE-001234
  YE-KİR-BODRUM-VİLLA-005678
  YE-GÜN-TURGUT-YAZLK-000099

Kurallar:
  - Otomatik üretilir (IlanReferansService)
  - UNIQUE constraint
  - Değiştirilemez
  - Arama ve filtrede kullanılır
```

### **portal_sync_status (Portal Senkronizasyon):**
```json
{
  "sahibinden": {
    "status": "success",
    "last_sync": "2025-10-11T10:30:00Z",
    "portal_id": "123456789"
  },
  "hepsiemlak": {
    "status": "pending",
    "last_sync": null,
    "error": null
  }
}
```

### **portal_pricing (Portal Özel Fiyat):**
```json
{
  "sahibinden": {
    "price": 3500000,
    "currency": "TRY",
    "notes": "Komisyon dahil"
  },
  "hepsiemlak": {
    "price": 3450000,
    "currency": "TRY"
  }
}
```

---

## 🎯 AI QUERY ÖRNEKLERİ

### **Örnek 1: Villa Sayısı**

**Soru:** "Bodrum'da kaç villa var?"

**AI SQL:**
```sql
SELECT COUNT(*) FROM ilanlar
WHERE alt_kategori_id IN (
  SELECT id FROM ilan_kategorileri WHERE name = 'Villa'
)
AND il_id = (SELECT id FROM iller WHERE il_adi = 'Muğla')
AND ilce_id = (SELECT id FROM ilceler WHERE ilce_adi = 'Bodrum')
AND status = 'Aktif';
```

### **Örnek 2: Ortalama Fiyat**

**Soru:** "Yalıkavak'ta villa ortalama fiyatı ne?"

**AI SQL:**
```sql
SELECT AVG(fiyat) as ortalama, para_birimi
FROM ilanlar
WHERE mahalle_id = (
  SELECT id FROM mahalleler WHERE mahalle_adi = 'Yalıkavak'
)
AND alt_kategori_id IN (
  SELECT id FROM ilan_kategorileri WHERE name = 'Villa'
)
AND status = 'Aktif'
GROUP BY para_birimi;
```

---

## 🎓 SCHEMA ÖĞRENME NOTLARI

### **İlişki Mantığı:**
```
1. İlan → Kategori: 3 seviyeli hiyerarşi (ana → alt → yayın)
2. İlan → Lokasyon: 4 seviyeli (ülke → il → ilce → mahalle)
3. İlan → Kişi: 2 ilişki (sahibi, danışman)
4. İlan → Site: 1 ilişki (opsiyonel)
5. İlan → Özellikler: Many-to-many (checkboxes)
```

### **Status Değerleri:**
```yaml
# ilanlar.status
Taslak: Henüz yayınlanmamış
Aktif: Yayında
Pasif: Yayından alınmış
Beklemede: Onay bekliyor

# kisiler.status
Aktif: Aktif müşteri
Pasif: Eski müşteri
Beklemede: Potansiyel
```

---

**🗄️ ÖZET:** Bu schema bilgilerini her AI yanıtında kullan. Context7 field adlarına dikkat et!

