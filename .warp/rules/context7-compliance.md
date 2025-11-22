# Warp AI Rules - Context7 & Yalıhan Bekçi Compliance

**Tarih:** 2025-11-12  
**Durum:** ✅ Aktif  
**Öncelik:** CRITICAL  
**Uygulama:** Tüm AI assisted development işlemleri

---

## 🎯 Temel Prensip

**Her database değişikliği, API geliştirmesi ve seeder işlemi Context7 kurallarına uygun olmalı ve Yalıhan Bekçi'ye öğretilmelidir.**

---

## 📋 Zorunlu Kontrol Listesi

Her işlemde aşağıdaki adımları **MUTLAKA** takip et:

### 1. Database İşlemleri

- [ ] Mevcut tablo structure'ını kontrol et (`SHOW CREATE TABLE`)
- [ ] Aynı amaçlı field'lar farklı tablolarda var mı kontrol et
- [ ] Varsa aynı data type ve format kullan (JSON vs string tutarsızlığı yasak)
- [ ] Context7 yasak field adlarını kontrol et (is_active, aktif, enabled, vb.)
- [ ] Migration yazmadan önce `tinker` ile mevcut veriyi test et

**Örnek Kontrol:**
```bash
php artisan tinker --execute="
\App\Models\FeatureCategory::select('applies_to')->limit(5)->get()
"
```

### 2. API Development

- [ ] Response format tutarlı mı? (success, data, metadata)
- [ ] Query optimization yapıldı mı? (whereJsonContains vs where)
- [ ] Error handling eklendi mi?
- [ ] API test edildi mi? (curl + jq)

**Örnek Test:**
```bash
curl -s "http://localhost:8000/api/admin/features?applies_to=arsa" | jq '.data.metadata'
```

### 3. Seeder Operations

- [ ] applies_to, status gibi standard field'lar doğru formatta mı?
- [ ] String kullanılmalı mı yoksa JSON mı? (Context7'ye göre karar ver)
- [ ] Duplicate data check var mı? (firstOrCreate kullanıldı mı?)

### 4. Documentation

- [ ] Yalıhan Bekçi'ye öğretildi mi? (`yalihan-bekci/learned/`)
- [ ] Context7 Rules güncellendi mi? (gerekirse)
- [ ] Warp Rules güncellendi mi? (gerekirse)

---

## ⚠️ Kritik Kurallar

### Kural #1: Database Field Consistency

**Aynı amaçlı field'lar farklı tablolarda aynı formatta olmalı**

❌ **YANLIŞ:**
```php
// feature_categories table
applies_to = '["arsa"]' // JSON array

// features table  
applies_to = 'arsa' // string
```

✅ **DOĞRU:**
```php
// Her iki tabloda da
applies_to = 'arsa' // string
```

**Neden:**
- Basit query → hızlı performans
- Index kullanabilir
- Tutarlı kod
- MySQL JSON function gerektirmez

---

### Kural #2: Context7 Yasak Field Adları

**Bu field adlarını ASLA kullanma:**

| Yasak | Kullan | Tablo |
|-------|--------|-------|
| `is_active` | `status` | Tüm tablolar |
| `aktif` | `status` | Tüm tablolar |
| `enabled` | `status` | Tüm tablolar |
| `ad_soyad` | `tam_ad` | kisiler |
| `full_name` | `name` | users |

**Kontrol Komutu:**
```bash
grep -r "is_active\|enabled\|ad_soyad" app/Models/
```

---

### Kural #3: String vs JSON Karar Matrisi

| Senaryo | Format | Neden |
|---------|--------|-------|
| Single value (kategori slug) | String | Basit query, index |
| Enum değerler | String | Performans |
| Many-to-many ilişki | Pivot Table | Normalizasyon |
| Çoklu değer (tags) | JSON | Esneklik |

**applies_to için:** String kullan ✅

---

## 🔧 İşlem Adımları (Standart Prosedür)

### Adım 1: Analiz

```bash
# Mevcut veriyi kontrol et
php artisan tinker --execute="
\App\Models\YourModel::select('field_name')->limit(10)->get()
"

# Table structure
php artisan tinker --execute="
DB::select('SHOW CREATE TABLE your_table')
"
```

### Adım 2: Karar

- [ ] Field type ne olmalı? (string/json/enum)
- [ ] Diğer tablolarda aynı field var mı?
- [ ] Context7 kuralına uygun mu?

### Adım 3: Uygulama

```php
// ✅ Doğru approach
DB::statement("
    UPDATE table_name 
    SET field_name = REPLACE(REPLACE(field_name, '[', ''), ']', '')
    WHERE field_name LIKE '[%'
");
```

### Adım 4: Test

```bash
# API test
curl -s "http://localhost:8000/api/endpoint" | jq '.'

# Database verify
php artisan tinker --execute="Model::count()"
```

### Adım 5: Dokümantasyon

```bash
# Yalıhan Bekçi'ye öğret
touch yalihan-bekci/learned/issue-name-$(date +%Y-%m-%d).json

# Context7 güncelle (gerekirse)
vim docs/active/CONTEXT7-RULES-DETAILED.md
```

---

## 📊 Performans Metrikleri

Her değişiklikten sonra şunları ölç:

- [ ] API response time (before/after)
- [ ] Query execution time (`EXPLAIN`)
- [ ] Code complexity (whereJsonContains vs where)

**Örnek:**
```
Önce: N/A (çalışmıyordu)
Sonra: ~50ms
Kazanç: ∞
```

---

## 🚨 Acil Durum Prosedürü

API çalışmıyor ve "not found" hatası alıyorsan:

1. **Database veri tipini kontrol et**
   ```bash
   php artisan tinker --execute="Model::first()->field_name"
   ```

2. **API query'yi kontrol et**
   ```php
   Log::info('Query:', ['value' => $variable]);
   ```

3. **JSON vs String mismatch kontrol et**
   ```bash
   grep -n "whereJsonContains\|where(" app/Http/Controllers/Api/YourController.php
   ```

4. **Hızlı fix (temporary)**
   ```sql
   UPDATE table SET field = 'plain_value' WHERE field LIKE '[%'
   ```

5. **Kalıcı fix: Migration + Seeder güncelleme**

---

## 📚 Referanslar

- **Master Project Prompt:** `.warp/rules/master-project-prompt.md` (Ana referans)
- **Context7 Authority:** `.context7/authority.json` (TEK YETKİLİ KAYNAK)
- **Forbidden Patterns:** `.context7/FORBIDDEN_PATTERNS.md`
- **Yalıhan Bekçi:** `yalihan-bekci/learned/applies-to-field-standardization-2025-11-12.json`
- **API Controller:** `app/Http/Controllers/Api/FeatureController.php` (Lines 40-47)

---

## 🎓 Öğrenilen Dersler

### 1. Migration yazmadan önce mevcut veriyi kontrol et
- `tinker` kullan
- Data type'ı anla
- Format'ı anla

### 2. JSON her zaman gerekli değil
- Single value için string yeterli
- Performans farkı var
- Index kullanımı önemli

### 3. Tutarlılık her şeyden önemli
- Bir field farklı tablolarda farklı format = BUG
- "Çalışıyor ama" = "Çalışmıyor ama"
- Standartlaştır, dokümante et

### 4. Test et, test et, test et
- API response
- Database query
- Frontend integration

### 5. Yalıhan Bekçi'ye öğret
- JSON formatında
- Detaylı açıklama
- Kod örnekleri ile
- Gelecek referans için

---

## ✅ Bu Dosya Nedir?

Bu dosya, Warp AI'ın **otomatik olarak** okuduğu ve uyguladığı kural setidir.

**Her AI-assisted development işleminde:**
1. Bu dosya otomatik okunur
2. Kurallar uygulanır
3. Violation durumunda uyarı verilir
4. Compliance sağlanır

**Güncelleme:** Her yeni pattern öğrenildiğinde bu dosya güncellenir.

---

**Versiyon:** 1.0  
**Son Güncelleme:** 2025-11-12  
**Sahip:** Yalıhan Emlak Development Team  
**Status:** ✅ Production Ready
