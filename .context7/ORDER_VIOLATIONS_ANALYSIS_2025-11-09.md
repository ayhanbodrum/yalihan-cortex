# Order Kolonu İhlalleri Analizi - 2025-11-09

## 📊 ÖZET

**Toplam İhlal:** 8 kritik dosya, 15+ migration dosyası  
**Durum:** ⚠️ Düzeltme Gerekiyor  
**Context7 Kuralı:** `order` → `display_order` (FORBIDDEN - PERMANENT)

---

## 🚨 KRİTİK İHLALLER

### 1. Models - `order` kolonu kullanılıyor

#### ❌ `app/Models/Etiket.php`
- **Line 20, 31:** `'order'` fillable ve cast'te kullanılıyor
- **Line 75-83:** Accessor/Mutator var ama `sira` kolonuna map ediliyor
- **Line 112, 119:** `orderBy('sira')` kullanılıyor (doğru)
- **Sorun:** Model'de `order` field'ı var ama database'de `sira` var
- **Çözüm:** Model'den `order` kaldırılmalı, sadece `sira` kullanılmalı

#### ❌ `app/Models/AltKategoriYayinTipi.php`
- **Line 23, 28:** `'order'` fillable ve cast'te kullanılıyor
- **Sorun:** Context7 standardına göre `display_order` olmalı
- **Çözüm:** Migration ile `order` → `display_order` rename yapılmalı

#### ✅ `app/Models/Photo.php`
- **Line 31:** `'sira'` kullanılıyor (doğru)
- **Line 82:** `orderBy('sira')` kullanılıyor (doğru)
- **Durum:** Context7 uyumlu (Photo model'de `sira` kullanılıyor)

---

## 📋 MIGRATION DOSYALARINDA İHLALLER

### Yeni Migration'lar (Düzeltilmeli)

1. **`2025_11_05_133340_create_dashboard_widgets_table.php`**
   - Line 26: `$table->integer('order')->default(0);`
   - **Çözüm:** `display_order` olmalı

2. **`2025_11_05_000001_create_feature_assignments_table.php`**
   - Line 30: `$table->integer('order')->default(0);`
   - **Çözüm:** `display_order` olmalı

3. **`2025_11_03_093414_create_photos_table.php`**
   - Line 21: `$table->integer('order')->default(0);`
   - **Not:** Photo model'de `sira` kullanılıyor, migration'da `order` var
   - **Çözüm:** Migration'da `sira` olmalı veya `display_order` olmalı

4. **`2025_11_02_000001_create_polymorphic_features_system.php`**
   - Line 23, 64, 94: `$table->integer('order')->default(0);` (3 yerde)
   - **Çözüm:** `display_order` olmalı

5. **`2025_10_29_170932_create_alt_kategori_yayin_tipi_table.php`**
   - Line 43: `$table->integer('order')->default(0);`
   - **Çözüm:** `display_order` olmalı

6. **`2025_10_25_160239_create_kategori_yayin_tipi_field_dependencies_table.php`**
   - Line 39: `$table->integer('order')->default(0);`
   - **Çözüm:** `display_order` olmalı

### Eski Migration'lar (Düşük Öncelik)

- 19 eski migration dosyasında `order` kullanılıyor
- Bu migration'lar zaten çalıştırılmış, yeni migration ile düzeltilebilir
- **Çözüm:** Yeni migration ile `order` → `display_order` rename yapılmalı

---

## 🔍 NEDEN UYULMADI?

### 1. **Migration Template Kontrolü Yok**
- **Sorun:** Yeni migration oluştururken Context7 kuralları kontrol edilmiyor
- **Sebep:** Pre-commit hook migration dosyalarını kontrol etmiyor
- **Çözüm:** Pre-commit hook'a migration kontrolü eklenmeli

### 2. **Model Template Kontrolü Yok**
- **Sorun:** Yeni model oluştururken Context7 kuralları kontrol edilmiyor
- **Sebep:** Model stub'larında Context7 kontrolü yok
- **Çözüm:** Model stub'larına Context7 kontrolü eklenmeli

### 3. **Code Review Süreci Eksik**
- **Sorun:** Code review'da Context7 kuralları kontrol edilmiyor
- **Sebep:** Otomatik kontrol yok
- **Çözüm:** CI/CD pipeline'a Context7 kontrolü eklenmeli

### 4. **Documentation Eksikliği**
- **Sorun:** Geliştiriciler Context7 kurallarını bilmiyor
- **Sebep:** Documentation yeterince görünür değil
- **Çözüm:** README'ye Context7 kuralları eklenmeli

### 5. **Backward Compatibility Karmaşası**
- **Sorun:** Accessor/Mutator kullanımı ile `order` field'ı karıştırılıyor
- **Sebep:** Accessor/Mutator kullanıldığında model'de `order` field'ı olmamalı
- **Çözüm:** Accessor/Mutator kullanıldığında model'den `order` kaldırılmalı

---

## ✅ ÇÖZÜM ÖNERİLERİ

### 1. Pre-commit Hook Güçlendirme

```bash
# .git/hooks/pre-commit
#!/bin/bash

# Context7: order kolonu kontrolü
if git diff --cached --name-only | grep -E "(migrations|Models)" | xargs grep -l "['\"]order['\"]" | grep -v "display_order"; then
    echo "❌ Context7 VIOLATION: 'order' kolonu kullanılamaz, 'display_order' kullanılmalı"
    echo "📚 Detaylar: .context7/ORDER_DISPLAY_ORDER_STANDARD.md"
    exit 1
fi
```

### 2. CI/CD Pipeline Kontrolü

```yaml
# .github/workflows/context7-check.yml
- name: Context7 Compliance Check
  run: |
    php artisan context7:check
    if [ $? -ne 0 ]; then
      echo "❌ Context7 compliance check failed"
      exit 1
    fi
```

### 3. Model Stub Güncelleme

```php
// stubs/model.context7.stub
protected $fillable = [
    'name',
    'display_order', // ✅ Context7: order → display_order
];
```

### 4. Migration Stub Güncelleme

```php
// stubs/migration.context7.stub
$table->integer('display_order')->default(0); // ✅ Context7: order → display_order
```

### 5. Documentation Güncelleme

- README.md'ye Context7 kuralları eklenmeli
- Her yeni geliştirici için Context7 onboarding yapılmalı

---

## 🔧 DÜZELTME PLANI

### Öncelik 1: Kritik Modeller (Hemen)

1. **`app/Models/AltKategoriYayinTipi.php`**
   - `order` → `display_order` migration oluştur
   - Model'i güncelle

2. **`app/Models/Etiket.php`**
   - Model'den `order` kaldır (sadece `sira` kullan)
   - Accessor/Mutator'ı güncelle

### Öncelik 2: Yeni Migration'lar (Bu Hafta)

1. **`2025_11_05_133340_create_dashboard_widgets_table.php`**
   - `order` → `display_order` değiştir

2. **`2025_11_05_000001_create_feature_assignments_table.php`**
   - `order` → `display_order` değiştir

3. **`2025_11_03_093414_create_photos_table.php`**
   - `order` → `sira` veya `display_order` değiştir

4. **`2025_11_02_000001_create_polymorphic_features_system.php`**
   - `order` → `display_order` değiştir (3 yerde)

5. **`2025_10_29_170932_create_alt_kategori_yayin_tipi_table.php`**
   - `order` → `display_order` değiştir

6. **`2025_10_25_160239_create_kategori_yayin_tipi_field_dependencies_table.php`**
   - `order` → `display_order` değiştir

### Öncelik 3: Pre-commit Hook (Bu Hafta)

1. Pre-commit hook'a migration kontrolü ekle
2. Pre-commit hook'a model kontrolü ekle
3. Test et ve dokümante et

### Öncelik 4: CI/CD Pipeline (Gelecek Hafta)

1. CI/CD pipeline'a Context7 kontrolü ekle
2. Build failure durumunda bildirim gönder
3. Test et ve dokümante et

---

## 📊 İSTATİSTİKLER

- **Toplam İhlal:** 8 kritik dosya, 15+ migration dosyası
- **Düzeltilmesi Gereken:** 6 yeni migration, 2 model
- **Tahmini Süre:** 2-3 saat
- **Öncelik:** YÜKSEK

---

## 🎯 HEDEF

**%100 Context7 Compliance** - Tüm `order` kullanımları `display_order` olmalı (veya özel durumlar için `sira`)

---

**Son Güncelleme:** 2025-11-09  
**Durum:** ⚠️ Düzeltme Gerekiyor

