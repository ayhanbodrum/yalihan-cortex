# Migration İhlal Çözüm Planı - 2025-11-11

**Tarih:** 2025-11-11 14:35  
**Durum:** 🔄 UYGULANACAK  
**Öncelik:** 🔴 ACİL

---

## ✅ TAMAMLANAN İŞLEMLER

1. ✅ **Eski Migration'lar Düzeltildi** (2025-11-11)
   - 18 migration dosyası güncellendi
   - `order` → `display_order` değişiklikleri uygulandı
   - Index'ler güncellendi

2. ✅ **Pre-commit Hook Script'i Oluşturuldu** (2025-11-11)
   - `scripts/check-order-column.sh` oluşturuldu
   - Context7 compliance kontrolü eklendi

---

## 🔄 UYGULANACAK İŞLEMLER

### 1. Pre-commit Hook Aktifleştirme (ACİL)

**Aksiyon:**
```bash
# Pre-commit hook'u yeniden yükle
pre-commit install
pre-commit run --all-files
```

**Beklenen Sonuç:**
- Yeni migration'larda `order` kullanımı commit edilemeyecek
- Otomatik Context7 kontrolü yapılacak

---

### 2. Migration Template Oluşturma (ÖNEMLİ)

**Aksiyon:**
```bash
# Laravel stubs klasörüne Context7 template ekle
mkdir -p stubs
cp vendor/laravel/framework/stubs/migration.create.stub stubs/migration.create.stub
# Template'i Context7 uyumlu yap
```

**Template İçeriği:**
```php
$table->integer('display_order')->default(0); // Context7: order → display_order
```

**Beklenen Sonuç:**
- `php artisan make:migration` komutu Context7 uyumlu migration oluşturacak

---

### 3. Laravel Artisan Komut Genişletme (ÖNEMLİ)

**Aksiyon:**
```php
// app/Console/Commands/MakeMigration.php oluştur
// Context7 kontrolü ekle
```

**Beklenen Sonuç:**
- Migration oluşturulurken Context7 uyarısı verilecek

---

### 4. CI/CD Pipeline Kontrolü (ÖNEMLİ)

**Aksiyon:**
```yaml
# .github/workflows/context7-check.yml oluştur
- name: Context7 Compliance Check
  run: ./scripts/context7-full-scan.sh
```

**Beklenen Sonuç:**
- PR'larda otomatik Context7 kontrolü yapılacak
- İhlaller PR'ı engelleyecek

---

## 📋 ÖNCELİK SIRASI

### 🔴 ACİL (Bugün)
1. ✅ Pre-commit hook script'i oluşturuldu
2. 🔄 Pre-commit hook'u aktifleştir ve test et
3. 🔄 CI/CD pipeline'a Context7 kontrolü ekle

### 🟡 ÖNEMLİ (Bu Hafta)
4. 🔄 Migration template'leri oluştur
5. 🔄 Laravel artisan komutuna Context7 kontrolü ekle
6. 🔄 Dokümantasyonu migration oluşturma rehberine ekle

### 🟢 UZUN VADELİ (Bu Ay)
7. 🔄 IDE entegrasyonu (IntelliSense, snippets)
8. 🔄 Otomatik migration düzeltme script'i
9. 🔄 Context7 compliance dashboard

---

## 🎯 BAŞARI KRİTERLERİ

### Kısa Vadeli (1 Hafta)
- ✅ Pre-commit hook çalışıyor
- ✅ Yeni migration'larda ihlal yok
- ✅ CI/CD pipeline kontrolü aktif

### Orta Vadeli (1 Ay)
- ✅ Migration template'leri Context7 uyumlu
- ✅ Laravel artisan komutu Context7 uyarısı veriyor
- ✅ Tüm geliştiriciler Context7 kurallarını biliyor

### Uzun Vadeli (3 Ay)
- ✅ IDE entegrasyonu tamamlandı
- ✅ Otomatik migration düzeltme script'i çalışıyor
- ✅ Context7 compliance dashboard aktif

---

**Son Güncelleme:** 2025-11-11 14:35  
**Durum:** 🔄 UYGULANACAK

