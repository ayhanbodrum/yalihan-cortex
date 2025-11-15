# Veritabanı Yapılandırması - 2025-10-30

## 🗄️ DOĞRU VERİTABANI BİLGİLERİ

### Aktif Veritabanı

```yaml
Tip: MySQL
Host: 127.0.0.1
Port: 3306
Database: yalihanemlak_ultra # ← DOĞRU VERİTABANI
Kullanıcı: root
```

### ⚠️ YANLIŞ VERİTABANLARI

```yaml
yalihanemlak_test: TEST veritabanı (BOŞ - KULLANILMAYACAK!)
```

## 📊 VERİTABANI İÇERİĞİ (yalihanemlak_ultra)

### Tablo İstatistikleri

- **Toplam Tablo:** 50
- **Kullanıcılar:** 4 kayıt
- **İlan Kategorileri:** 36 kayıt
    - Seviye 0 (Ana): 5 kategori
    - Seviye 1 (Alt): 20 kategori
    - Seviye 2 (Yayın Tipi): 11 kategori
- **İlanlar:** 0 kayıt (yeni sistem)
- **Kişiler:** 3 kayıt
- **Özellikler:** 46 kayıt
- **Özellik Kategorileri:** 5 kayıt

### Ana Kategoriler (Seviye 0)

```yaml
1. Konut (ID: 1)
2. İşyeri (ID: 2)
3. Arsa (ID: 3)
4. Yazlık Kiralama (ID: 4)
5. Turistik Tesisler (ID: 5)
```

## 🔧 SORUN GİDERME

### Eğer Kategoriler Boş Görünüyorsa:

1. `.env` dosyasını kontrol et:

    ```bash
    DB_DATABASE=yalihanemlak_ultra  # DOĞRU!
    DB_DATABASE=yalihanemlak_test   # YANLIŞ!
    ```

2. Cache temizle:

    ```bash
    php artisan config:clear
    php artisan cache:clear
    ```

3. Veritabanı bağlantısını doğrula:
    ```php
    DB::connection()->getDatabaseName();  // "yalihanemlak_ultra" dönmeli
    ```

## 📋 CONTEXT7 UYUMLULUK

### Kolon İsimleri

- ✅ `name` (İngilizce - Context7 compliant)
- ✅ `status` (Boolean - TINYINT(1))
- ✅ `parent_id`, `seviye`, `order`

### Eski Türkçe Kolonlar

- ⚠️ `kategori_adi` → Artık kullanılmıyor (name kullan)
- ⚠️ `aktif` → Artık kullanılmıyor (status kullan)
- ⚠️ `is_active` → Artık kullanılmıyor (status kullan)

## 🎯 ÖNEMLİ NOTLAR

1. **yalihanemlak_ultra** = Canlı/Production veritabanı
2. **yalihanemlak_test** = Boş test veritabanı (kullanma!)
3. Tüm veriler `yalihanemlak_ultra`'da güvende
4. Migration'lar zaten çalıştırılmış (36 kategori mevcut)
5. Context7 compliance %100

## 🚨 HATIRLATMA

**ASLA** `.env` dosyasında `DB_DATABASE=yalihanemlak_test` kullanma!  
**DAIMA** `DB_DATABASE=yalihanemlak_ultra` olmalı!

---

_Son güncelleme: 2025-10-30_
_Yalıhan Bekçi tarafından kaydedildi_
