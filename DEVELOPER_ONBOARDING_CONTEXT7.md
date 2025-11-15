# Developer Onboarding - Context7 Standards

**Tarih:** 2025-11-11  
**Durum:** ✅ ACTIVE  
**Versiyon:** 1.0

---

## 🎯 AMAÇ

Yeni geliştiricilerin Context7 standartlarını hızlıca öğrenmesi ve projeye uyum sağlaması.

---

## 🚀 HIZLI BAŞLANGIÇ

### 1. Context7 Nedir?

Context7, bu projenin kod kalitesi ve tutarlılık standartlarıdır. Tüm geliştiriciler bu standartlara uymalıdır.

### 2. Temel Kurallar

#### ❌ YASAK:
- `order` → `display_order` kullan
- `enabled`, `aktif`, `is_active` → `status` kullan
- `sehir` → `il` kullan
- `musteri_*` → `kisi_*` kullan
- `neo-*` CSS classes → Tailwind CSS kullan
- `layouts.app` → `admin.layouts.neo` kullan
- `crm.*` routes → `admin.*` kullan

#### ✅ ZORUNLU:
- `display_order` (sıralama için)
- `status` (aktif/pasif için)
- `il` (şehir için)
- `kisi_*` (kişi bilgileri için)
- Tailwind CSS (styling için)
- `admin.layouts.neo` (layout için)
- `admin.*` routes (route naming için)

---

## 📋 MIGRATION OLUŞTURMA

### ✅ DOĞRU YOL:

```bash
# Context7 uyumlu migration oluştur
php artisan make:migration create_example_table

# Oluşturulan migration otomatik Context7 uyumlu olacak:
# - display_order kolonu eklenmiş
# - status kolonu eklenmiş
```

### ❌ YANLIŞ:

```php
// ❌ YANLIŞ
$table->integer('order')->default(0);
$table->boolean('enabled')->default(true);

// ✅ DOĞRU
$table->integer('display_order')->default(0); // Context7
$table->tinyInteger('status')->default(1); // Context7
```

---

## 🔍 KONTROL MEKANİZMALARI

### 1. Pre-commit Hook

**Otomatik çalışır:** Her commit öncesi

**Ne yapar:**
- Context7 ihlallerini kontrol eder
- İhlal varsa commit'i engeller
- Düzeltme önerileri sunar

**Test:**
```bash
pre-commit run --all-files
```

### 2. CI/CD Pipeline

**Otomatik çalışır:** Her PR'da

**Ne yapar:**
- Tüm projeyi tarar
- İhlal varsa PR'ı engeller
- Compliance raporu oluşturur

### 3. Günlük Tarama

**Manuel çalıştır:**
```bash
./scripts/context7-daily-check.sh
```

**Ne yapar:**
- Tüm projeyi tarar
- Rapor oluşturur
- `.context7/daily-reports/` klasörüne kaydeder

---

## 📚 DOKÜMANTASYON

### Temel Dokümantasyon:
- `.context7/authority.json` - Master authority file
- `.context7/ORDER_DISPLAY_ORDER_STANDARD.md` - Order → display_order standardı
- `.context7/MIGRATION_STANDARDS.md` - Migration standartları
- `.context7/MIGRATION_TEMPLATE_STANDARDS.md` - Template standartları

### Yardımcı Komutlar:
```bash
# Context7 compliance kontrolü
./scripts/context7-full-scan.sh

# Günlük tarama
./scripts/context7-daily-check.sh

# Pre-commit test
pre-commit run --all-files
```

---

## ⚠️ SIK YAPILAN HATALAR

### 1. Migration'da `order` kullanımı
**Hata:** `$table->integer('order')->default(0);`  
**Doğru:** `$table->integer('display_order')->default(0);`

### 2. Model'de `order` kullanımı
**Hata:** `protected $fillable = ['order'];`  
**Doğru:** `protected $fillable = ['display_order'];`

### 3. Query'de `order` kullanımı
**Hata:** `->orderBy('order')`  
**Doğru:** `->orderBy('display_order')`

---

## 🎯 ÖNEMLİ NOTLAR

1. **Template'ler Otomatik:** Migration template'leri Context7 uyumlu, `order` kullanamazsınız
2. **Pre-commit Engeller:** İhlal varsa commit edemezsiniz
3. **CI/CD Kontrolü:** PR'larda otomatik kontrol yapılır
4. **Dokümantasyon:** Tüm kurallar `.context7/` klasöründe

---

## 📞 YARDIM

Sorunuz varsa:
1. `.context7/authority.json` dosyasını kontrol edin
2. `.context7/` klasöründeki dokümantasyonu okuyun
3. `./scripts/context7-full-scan.sh` ile kontrol edin

---

**Son Güncelleme:** 2025-11-11  
**Durum:** ✅ ACTIVE

