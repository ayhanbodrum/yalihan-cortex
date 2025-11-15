# ⚡ Context7 Hızlı Referans - 22 Ekim 2025

**YAZDIRIN VE MASADA TUTUN!**

---

## 🚫 YASAK PATTERN'LER

```yaml
❌ durum → status
❌ is_active → enabled
❌ aktif → active
❌ sehir, sehir_id → il, il_id
❌ ad_soyad → tam_ad (accessor)
❌ currency → para_birimi
❌ btn-, card- → neo-*
```

---

## ✅ DOĞRU KULLANIM

```php
// ✅ Field Names
$ilan->status = 'Aktif';        // Field: status, Value: Aktif
$ilan->para_birimi = 'TRY';     // NOT currency!
$ilan->il_id = 48;              // NOT sehir_id!

// ✅ Relationships
$ilan->il                       // NOT sehir
$ilan->ilce                     // NOT bolge
$kisi->tam_ad                   // Accessor (NOT ad_soyad field)

// ✅ CSS Classes
neo-btn, neo-card, neo-input    // NOT btn-primary, card-body

// ✅ JavaScript
Vanilla JS (0KB)                // NOT React-Select (170KB)
Context7LiveSearch (3KB)        // NOT Choices.js (48KB)
```

---

## 💰 FİYAT SİSTEMİ

```blade
{{-- Fiyat + Sembol YAN YANA! --}}
{{ number_format($fiyat, 0, ',', '.') }} {{ $symbol }}
{{-- Çıktı: 2.500.000 ₺ ✅ --}}

{{-- Component --}}
<x-price-display :price="2500000" currency="TRY" />
<x-form.price-group name="fiyat" currencyName="para_birimi" />
```

**Para Birimleri:**

```
TRY: ₺ | USD: $ | EUR: € | GBP: £
```

---

## 🏞️ ARSA FIELDS (16 adet)

```
ada_no, parsel_no, imar_statusu
kaks, taks, gabari, alan_m2, taban_alani
altyapi_elektrik, altyapi_su, altyapi_dogalgaz
```

---

## 🏖️ YAZLIK FIELDS (14 adet)

```
gunluk_fiyat, haftalik_fiyat, aylik_fiyat
min_konaklama, max_misafir
sezon_baslangic, sezon_bitis
havuz, havuz_turu, havuz_boyut
```

---

## 🔍 KİŞİ ARAMA (Context7 Live Search)

```html
<div class="context7-live-search" data-search-type="kisiler">
    <input type="hidden" name="kisi_id" />
    <input type="text" class="neo-input" />
    <div class="context7-search-results ..."></div>
</div>
<script src="/js/context7-live-search-simple.js"></script>
```

**API:** `/api/kisiler/search?q=...&limit=20`  
**Debounce:** 300ms  
**Size:** 3KB (Vanilla JS)

---

## 📊 YENİ TABLOLAR (2 adet)

```sql
yazlik_fiyatlandirma
  └─ Sezonluk fiyat (yaz/ara/kış)

yazlik_rezervasyonlar
  └─ Rezervasyon (check-in/out, misafir, status)
```

---

## 🎯 MİGRATİON PATTERN

```bash
# Oluştur
php artisan make:migration add_MODULE_fields_to_TABLE_table --table=TABLE

# Context7 Checklist:
✅ Field name'ler İngilizce
✅ Comment'ler açıklayıcı
✅ Index'ler performans için
✅ Foreign key'ler constrained
✅ status → boolean veya enum
✅ Backward compatibility (legacy fields)

# Çalıştır
php artisan migrate --path=database/migrations/FILE.php
```

---

## 📁 SON İŞLEMLER (22 Ekim 2025)

```yaml
✅ 4 Migration oluşturuldu ve çalıştırıldı
✅ 30 field eklendi (16 arsa + 14 yazlık)
✅ 2 tablo oluşturuldu
✅ 3 eski dosya silindi
✅ 1 dosya yeniden adlandırıldı
✅ Context7 compliance: %100
```

---

**Güncelleme:** 22 Ekim 2025 Akşam  
**Context7:** %100 ✅  
**Status:** PRODUCTION READY
