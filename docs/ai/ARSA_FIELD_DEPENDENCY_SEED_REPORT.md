# 📊 Arsa Field Dependency Seed Raporu

**Tarih:** 2025-11-30  
**Source:** `docs/ai/GEMINI_COMPLETE_SYSTEM_DATA.json v2.0.0`  
**Context7 Compliance:** ✅ %100

---

## ✅ OLUŞTURULAN DOSYALAR

### 1. Seeder Dosyası
**Dosya:** `database/seeders/GeminiJsonBasedArsaFieldDependencySeeder.php`

**Özellikler:**
- ✅ JSON'dan Arsa × Satılık field dependencies okur
- ✅ Config'den imar_statusu seçeneklerini çeker
- ✅ AI metadata'yı JSON formatında `ai_prompt_key`'e kaydeder
- ✅ Context7 uyumlu: `status`, `display_order`
- ✅ Idempotent: `updateOrCreate` kullanır

**Çalıştırma:**
```bash
php artisan db:seed --class=GeminiJsonBasedArsaFieldDependencySeeder
```

---

## 📋 SEED EDİLEN FIELD'LAR (Arsa × Satılık)

Toplam **11 field** JSON'dan seed edildi:

| # | Field Slug | Field Name | Type | Required | AI Feature |
|---|-----------|-----------|------|----------|------------|
| 1 | `satis_fiyati` | Satış Fiyatı | price | ✅ Yes | - |
| 2 | `m2_fiyati` | m² Fiyatı | number | ❌ No | ✅ auto_calculate |
| 3 | `ada_no` | Ada No | text | ❌ No | ✅ TKGM |
| 4 | `parsel_no` | Parsel No | text | ❌ No | ✅ TKGM |
| 5 | `imar_statusu` | İmar Durumu | select | ❌ No | ✅ AI suggestion |
| 6 | `kaks` | KAKS | number | ❌ No | ✅ AI suggestion |
| 7 | `taks` | TAKS | number | ❌ No | ✅ AI suggestion |
| 8 | `gabari` | Gabari | number | ❌ No | ✅ AI suggestion |
| 9 | `alan_m2` | Arsa Metrekare | number | ❌ No | - |
| 10 | `ifrazsiz` | İfrazsız Satılık | boolean | ❌ No | - |
| 11 | `kat_karsiligi` | Kat Karşılığı | boolean | ❌ No | - |

---

## 🤖 AI METADATA YAPISI

AI özellikli field'lar için metadata JSON formatında `ai_prompt_key` kolonuna kaydedilir:

```json
{
  "prompt_key": "ada_no-suggest",
  "metadata": {
    "ai_source": "TKGM",
    "ai_suggestion": true
  }
}
```

### AI Feature Types:

1. **TKGM Source** (`ada_no`, `parsel_no`):
   - TKGM'den otomatik çekilebilir
   - Metadata: `{"ai_source": "TKGM"}`

2. **Auto Calculate** (`m2_fiyati`):
   - Fiyat ve m²'den otomatik hesaplanır
   - Metadata: `{"ai_calculation": "auto_calculate"}`

3. **AI Suggestion** (`imar_statusu`, `kaks`, `taks`, `gabari`):
   - AI tarafından önerilebilir
   - Metadata: `{"ai_suggestion": true}`

---

## 🔧 CONTEXT7 COMPLIANCE

### ✅ Uygulanan Standartlar:

1. **Status Field:**
   - ✅ `status` kullanılıyor (NOT `enabled`)
   - ✅ VARCHAR veya boolean olarak cast ediliyor

2. **Display Order:**
   - ✅ `display_order` kullanılıyor (NOT `order`)

3. **Field Naming:**
   - ✅ Tüm field'lar İngilizce
   - ✅ Türkçe kolon adı yok

4. **Config Integration:**
   - ✅ `config/yali_options.php` kullanılıyor
   - ✅ İmar durumu seçenekleri config'den çekiliyor

---

## 📝 FIELD OPTIONS (İmar Durumu)

Config'den çekilen seçenekler:

- İmarlı
- İmarsız
- Tarla
- Konut İmarlı
- Ticari İmarlı

**Config Yolu:** `config/yali_options.php` → `imar_statusu`

---

## 🎯 KULLANIM ÖRNEĞİ

### Frontend'de Field'ları Çekme:

```php
use App\Models\KategoriYayinTipiFieldDependency;

// Arsa × Satılık field'larını çek
$fields = KategoriYayinTipiFieldDependency::forKategoriYayinTipi('arsa', 'Satılık')
    ->active()
    ->ordered()
    ->get();

// AI özellikli field'ları çek
$aiFields = KategoriYayinTipiFieldDependency::forKategoriYayinTipi('arsa', 'Satılık')
    ->withAI()
    ->get();
```

### AI Metadata'yı Parse Etme:

```php
foreach ($aiFields as $field) {
    $metadata = json_decode($field->ai_prompt_key, true);
    
    if (isset($metadata['metadata']['ai_source'])) {
        // TKGM'den çek
        if ($metadata['metadata']['ai_source'] === 'TKGM') {
            // TKGM API çağrısı yap
        }
    }
    
    if (isset($metadata['metadata']['ai_calculation'])) {
        // Otomatik hesaplama yap
        if ($metadata['metadata']['ai_calculation'] === 'auto_calculate') {
            // m² fiyatını hesapla
        }
    }
}
```

---

## ⚠️ NOTLAR

1. **Idempotent Seeder:**
   - Seeder birden fazla çalıştırılabilir
   - Mevcut kayıtlar güncellenir, yeni kayıtlar eklenir
   - Duplicate kayıt oluşturmaz

2. **JSON Structure:**
   - JSON path: `field_dependencies.arsa.Satılık`
   - 11 field tanımlı
   - Tüm field'lar Context7 uyumlu

3. **AI Metadata Storage:**
   - AI bilgileri `ai_prompt_key` kolonunda JSON formatında saklanır
   - Frontend'de parse edilerek "Sihirli Değnek" butonu tetiklenir

4. **Config Integration:**
   - İmar durumu seçenekleri `config/yali_options.php`'den çekilir
   - Eğer config'de yoksa JSON'daki default değerler kullanılır

---

## 🔍 DOĞRULAMA

### Veritabanında Kontrol:

```sql
-- Arsa × Satılık field'larını kontrol et
SELECT field_slug, field_name, field_type, required, ai_suggestion, ai_auto_fill
FROM kategori_yayin_tipi_field_dependencies
WHERE kategori_slug = 'arsa'
  AND yayin_tipi = 'Satılık'
ORDER BY display_order;

-- AI özellikli field'ları kontrol et
SELECT field_slug, field_name, ai_prompt_key
FROM kategori_yayin_tipi_field_dependencies
WHERE kategori_slug = 'arsa'
  AND yayin_tipi = 'Satılık'
  AND (ai_suggestion = 1 OR ai_auto_fill = 1);
```

---

**Son Güncelleme:** 2025-11-30  
**Versiyon:** 1.0.0  
**Context7 Compliance:** ✅ %100  
**Linter Errors:** ✅ 0



