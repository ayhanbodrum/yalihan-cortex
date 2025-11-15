# Context7 Migration Template Standards - 2025-11-11

**Tarih:** 2025-11-11 14:40  
**Durum:** ✅ ACTIVE - PERMANENT  
**Severity:** CRITICAL  
**Enforcement:** STRICT

---

## 🎯 AMAÇ

Laravel'in `php artisan make:migration` komutu ile oluşturulan migration dosyalarının otomatik olarak Context7 standartlarına uyumlu olmasını sağlamak.

---

## ✅ DOĞRU YAKLAŞIM: Template Override

**Evet, bu doğru bir yaklaşımdır!** ✅

Laravel, `stubs/` klasöründeki template dosyalarını kullanır. Bu template'leri Context7 uyumlu hale getirerek:
- ✅ Yeni migration'lar otomatik Context7 uyumlu olur
- ✅ Geliştiriciler `order` yerine `display_order` kullanır
- ✅ Pre-commit hook'lar daha az ihlal yakalar
- ✅ Kod kalitesi artar

---

## 📋 TEMPLATE DOSYALARI

### 1. `stubs/migration.create.stub` ✅

**Durum:** Context7 uyumlu hale getirildi

**İçerik:**
- ✅ `display_order` kolonu (NOT `order`)
- ✅ `status` kolonu (NOT `enabled`, `aktif`, `is_active`)
- ✅ Context7 yorumları ve uyarıları
- ✅ Pre-commit hook bilgilendirmesi

**Kullanım:**
```bash
php artisan make:migration create_example_table
# Otomatik olarak Context7 uyumlu migration oluşturur
```

---

### 2. `stubs/migration.context7-complete.stub` ✅

**Durum:** Yeni oluşturuldu - Tam Context7 uyumlu template

**İçerik:**
- ✅ `display_order` kolonu
- ✅ `status` kolonu
- ✅ Tablo varlık kontrolü (`Schema::hasTable()`)
- ✅ Index'ler (`status`, `display_order`)
- ✅ Detaylı Context7 dokümantasyonu

**Kullanım:**
```bash
# Manuel olarak kullanılabilir veya
# migration.create.stub'ı bu template ile değiştirilebilir
```

---

### 3. `stubs/migration.context7-status.stub` ✅

**Durum:** Mevcut - Status için Context7 uyumlu

**İçerik:**
- ✅ `status` kolonu (TINYINT(1) boolean)
- ✅ Context7 yorumları

---

## 🚫 FORBIDDEN PATTERNS (Template'lerde)

### ❌ YANLIŞ:
```php
$table->integer('order')->default(0);
$table->boolean('enabled')->default(true);
$table->boolean('aktif')->default(true);
$table->boolean('is_active')->default(true);
$table->string('status')->default('Aktif');
```

### ✅ DOĞRU:
```php
$table->integer('display_order')->default(0); // Context7: order → display_order
$table->tinyInteger('status')->default(1); // Context7: enabled/aktif/is_active → status
```

---

## 📊 TEMPLATE KULLANIMI

### Laravel'in Template Sistemi

Laravel, migration oluştururken şu sırayı takip eder:

1. **`stubs/migration.create.stub`** → `php artisan make:migration create_*`
2. **`stubs/migration.update.stub`** → `php artisan make:migration update_*`
3. **`stubs/migration.stub`** → Diğer migration'lar

**Önemli:** `stubs/` klasöründeki dosyalar Laravel'in vendor klasöründeki default template'leri override eder.

---

## 🔧 TEMPLATE GÜNCELLEME SÜRECİ

### 1. Template'leri Context7 Uyumlu Yap

```bash
# Template'leri kontrol et
cat stubs/migration.create.stub

# Context7 uyumlu hale getir
# (Zaten yapıldı ✅)
```

### 2. Yeni Migration Oluştur

```bash
php artisan make:migration create_example_table
```

### 3. Oluşan Migration'ı Kontrol Et

```bash
cat database/migrations/2025_11_11_*_create_example_table.php
# display_order ve status kolonları otomatik olarak eklenmiş olmalı
```

---

## ✅ AVANTAJLAR

### 1. **Otomatik Uyumluluk**
- ✅ Geliştiriciler manuel olarak `display_order` yazmak zorunda değil
- ✅ Template otomatik Context7 uyumlu migration oluşturur

### 2. **Hata Önleme**
- ✅ `order` kolonu kullanımı template seviyesinde engellenir
- ✅ Pre-commit hook'lara daha az ihlal gelir

### 3. **Tutarlılık**
- ✅ Tüm migration'lar aynı standartları kullanır
- ✅ Kod kalitesi artar

### 4. **Dokümantasyon**
- ✅ Template içinde Context7 kuralları dokümante edilmiş
- ✅ Geliştiriciler kuralları template'ten öğrenir

---

## ⚠️ DİKKAT EDİLMESİ GEREKENLER

### 1. **Template Güncellemeleri**
- Laravel güncellendiğinde template'leri kontrol et
- Yeni Laravel özellikleri template'lere eklenmeli

### 2. **Geriye Dönük Uyumluluk**
- Eski migration'lar template değişikliğinden etkilenmez
- Sadece yeni migration'lar yeni template'i kullanır

### 3. **Özel Durumlar**
- Bazı migration'lar `display_order` veya `status` gerektirmeyebilir
- Bu durumda template'ten gereksiz kolonları kaldır

---

## 📚 REFERANSLAR

- `.context7/authority.json` (master authority file)
- `.context7/ORDER_DISPLAY_ORDER_STANDARD.md`
- `.context7/MIGRATION_STANDARDS.md`
- `stubs/migration.create.stub` (Context7 uyumlu template)
- `stubs/migration.context7-complete.stub` (Tam Context7 uyumlu template)

---

## 🎯 SONUÇ

**Evet, template oluşturma yaklaşımı DOĞRUDUR!** ✅

Bu yaklaşım:
- ✅ Laravel'in standart çalışma şekline uygun
- ✅ Otomatik Context7 uyumluluğu sağlar
- ✅ Geliştirici hatalarını önler
- ✅ Kod kalitesini artırır
- ✅ Pre-commit hook'lara daha az ihlal gelir

**Önerilen Aksiyon:**
1. ✅ `stubs/migration.create.stub` Context7 uyumlu hale getirildi
2. 🔄 `stubs/migration.update.stub` da Context7 uyumlu hale getirilmeli
3. 🔄 Geliştiricilere template kullanımı hakkında bilgi verilmeli
4. 🔄 Pre-commit hook'ları aktifleştirilmeli

---

**Son Güncelleme:** 2025-11-11 14:40  
**Durum:** ✅ ACTIVE - TEMPLATE'LER GÜNCELLENDİ

