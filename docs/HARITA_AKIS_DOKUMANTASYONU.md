# 🗺️ Harita Akış Dokümantasyonu

## 📍 İl/İlçe/Mahalle Seçiminde Harita Nasıl Çalışır?

### 🔄 Cascade (Kademeli) Akış

```
İl Seçimi → İlçe Dropdown Doldur → Harita İl'e Odaklan (Zoom 8)
    ↓
İlçe Seçimi → Mahalle Dropdown Doldur → Harita İlçe'ye Odaklan (Zoom 10)
    ↓
Mahalle Seçimi → Harita Mahalle'ye Odaklan (Zoom 18) + Marker Ekle
```

---

## 1️⃣ İL SEÇİMİ

**Trigger:** `il_id` dropdown değiştiğinde

**Akış:**
1. `VanillaLocationManager.focusMapOnProvince(provinceId)` çağrılır
2. API: `GET /api/location/provinces/{id}` → İl koordinatları çekilir
3. Harita: `map.setView([lat, lng], 8)` → İl merkezine odaklanır (Zoom 8)
4. İlçe dropdown'ı temizlenir ve yeni ilçeler yüklenir
5. Mahalle dropdown'ı temizlenir ve devre dışı bırakılır

**Zoom Seviyesi:** 8 (İl görünümü - geniş alan)

**Fallback:** API başarısız olursa hardcoded koordinatlar kullanılır

---

## 2️⃣ İLÇE SEÇİMİ

**Trigger:** `ilce_id` dropdown değiştiğinde

**Akış:**
1. `VanillaLocationManager.focusMapOnDistrict(districtId)` çağrılır
2. API: `GET /api/location/districts/{id}` → İlçe koordinatları çekilir
3. Harita: `map.setView([lat, lng], 10)` → İlçe merkezine odaklanır (Zoom 10)
4. Mahalle dropdown'ı temizlenir ve yeni mahalleler yüklenir

**Zoom Seviyesi:** 10 (İlçe görünümü - orta alan)

---

## 3️⃣ MAHALLE SEÇİMİ

**Trigger:** `mahalle_id` dropdown değiştiğinde

**Akış:**
1. `VanillaLocationManager.focusMapOnNeighborhood(mahalleId, mahalleName, ilceName, ilName)` çağrılır
2. **Öncelik 1:** Veritabanından koordinat çek
   - API: `GET /api/location/neighborhood/{id}/coordinates`
   - `mahalleler` tablosundan `enlem` ve `boylam` alanları
3. **Öncelik 2:** Veritabanında yoksa Nominatim API
   - Query: `"{mahalleName}, {ilceName}, {ilName}, Turkey"`
   - Nominatim geocoding ile koordinat bul
4. Harita: `map.flyTo([lat, lng], 18, {duration: 1.5})` → Mahalle merkezine animasyonlu odaklanır
5. Marker: `setMarker([lat, lng])` → Haritaya marker eklenir
6. Toast: `"Harita {mahalleName} mahallesine odaklandı"` mesajı gösterilir

**Zoom Seviyesi:** 18 (Mahalle görünümü - detaylı alan)

**Özellikler:**
- ✅ Veritabanı öncelikli (hızlı)
- ✅ Nominatim fallback (güvenilir)
- ✅ Animasyonlu geçiş (`flyTo`)
- ✅ Marker otomatik ekleme

---

## 🎯 Zoom Seviyeleri Özeti

| Seçim | Zoom | Açıklama |
|-------|------|----------|
| İl | 8 | Geniş alan görünümü (tüm il) |
| İlçe | 10 | Orta alan görünümü (ilçe merkezi) |
| Mahalle | 18 | Detaylı görünüm (mahalle + sokaklar) |
| Arazi Seçimi | 13 | Parsel seçimi için optimal zoom |

---

## 🔧 Teknik Detaylar

### VanillaLocationManager Class

```javascript
class VanillaLocationManager {
    // İl odaklanma
    focusMapOnProvince(provinceId) {
        // API: /api/location/provinces/{id}
        // Zoom: 8
    }

    // İlçe odaklanma
    focusMapOnDistrict(districtId) {
        // API: /api/location/districts/{id}
        // Zoom: 10
    }

    // Mahalle odaklanma
    async focusMapOnNeighborhood(mahalleId, mahalleName, ilceName, ilName) {
        // API: /api/location/neighborhood/{id}/coordinates
        // Zoom: 18
        // Marker: Otomatik ekle
    }
}
```

### API Endpoints

```php
// İl koordinatları
GET /api/location/provinces/{id}

// İlçe koordinatları
GET /api/location/districts/{id}

// Mahalle koordinatları (veritabanı öncelikli)
GET /api/location/neighborhood/{id}/coordinates
```

### Veritabanı Yapısı

```sql
-- Mahalleler tablosu
mahalleler (
    id,
    mahalle_adi,
    enlem,      -- ✅ Koordinat kaynağı 1
    boylam,     -- ✅ Koordinat kaynağı 1
    ilce_id
)
```

---

## 🚀 Kullanıcı Deneyimi

### Senaryo: Yalıkavak Seçimi

1. **İl Seçimi:** "Muğla" → Harita Muğla'ya zoom (Zoom 8)
2. **İlçe Seçimi:** "Bodrum" → Harita Bodrum'a zoom (Zoom 10)
3. **Mahalle Seçimi:** "Yalıkavak" → 
   - Veritabanından koordinat çek: `37.1676, 27.2035`
   - Harita Yalıkavak'a animasyonlu zoom (Zoom 18)
   - Marker ekle
   - Toast: "Harita Yalıkavak mahallesine odaklandı"

### Hata Senaryoları

- **API Hata:** Fallback koordinatlar kullanılır
- **Veritabanı Boş:** Nominatim API devreye girer
- **Nominatim Hata:** Kullanıcıya hata mesajı gösterilir

---

## 📝 Notlar

- ✅ Tüm akış **asenkron** çalışır (non-blocking)
- ✅ **Cascade loading** ile dropdown'lar otomatik doldurulur
- ✅ **Zoom seviyeleri** kullanıcı deneyimine göre optimize edilmiştir
- ✅ **Marker** sadece mahalle seçiminde eklenir (en detaylı seviye)
- ✅ **Animasyonlu geçişler** (`flyTo`) kullanıcı deneyimini iyileştirir

---

**Son Güncelleme:** 2025-12-04
**Context7 Compliance:** ✅ %100

