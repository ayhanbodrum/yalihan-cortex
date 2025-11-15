# Comprehensive Code Check Script - Kullanım Rehberi

**Tarih:** 2025-11-11  
**Script:** `scripts/comprehensive-code-check.php`  
**Durum:** ✅ GÜNCEL VE ÇALIŞIYOR

---

## 📋 TEMEL KULLANIM

### Tam Tarama

```bash
php scripts/comprehensive-code-check.php
```

### Rapor Okuma

```bash
# En son raporu bul
LATEST=$(ls -t .yalihan-bekci/reports/comprehensive-code-check-*.json | head -1)

# Özet görüntüle
php -r "\$data = json_decode(file_get_contents('$LATEST'), true); print_r(\$data['summary']);"

# Belirli kategoriyi görüntüle
php -r "\$data = json_decode(file_get_contents('$LATEST'), true);
foreach (\$data['details']['orphaned_code'] ?? [] as \$i => \$file) {
    echo (\$i+1) . '. ' . \$file . PHP_EOL;
}"
```

---

## 📊 ANALİZ KATEGORİLERİ

Script 10 kategori analiz eder:

1. **Lint Kontrolü** - Syntax hataları
2. **Dead Code Analizi** - Kullanılmayan kod
3. **Orphaned Code** - Route'a bağlı olmayan controller'lar
4. **Incomplete Implementation** - TODO/FIXME, boş metodlar
5. **Disabled Code** - Devre dışı kodlar
6. **Code Duplication** - Kod tekrarı
7. **Security Issues** - Güvenlik sorunları
8. **Performance Issues** - N+1 queries, performans sorunları
9. **Dependency Issues** - Kullanılmayan paketler
10. **Code Coverage** - Test kapsamı

---

## ✅ SON İYİLEŞTİRMELER

### 1. Test Dosyası Sayma

- **Önceki:** `glob()` kullanıyordu (recursive desteklemiyor)
- **Yeni:** `RecursiveIteratorIterator` kullanıyor
- **Sonuç:** Test dosyaları doğru sayılıyor (4 → 20)

### 2. CSRF False Positive Filtreleme

- **Önceki:** Tüm route'larda CSRF kontrolü yapıyordu
- **Yeni:** Web middleware kontrolü eklendi
- **Sonuç:** Security Issues azaldı (10 → 2)

### 3. Orphaned Code Route Kontrolü

- **Önceki:** Sadece 3 route dosyasını kontrol ediyordu
- **Yeni:** Tüm route dosyalarını recursive kontrol ediyor
- **Sonuç:** Daha doğru tespit (9 → 3)

---

## 📁 RAPORLAR

### JSON Raporu

```
.yalihan-bekci/reports/comprehensive-code-check-YYYY-MM-DD-HHMMSS.json
```

### Knowledge Dosyası

```
.yalihan-bekci/knowledge/code-check-results-YYYY-MM-DD.json
```

---

## 🔍 ÖRNEK ÇIKTI

```
📊 KAPSAMLI KOD KONTROLÜ TAMAMLANDI!
=====================================

📋 ÖZET:
  - Lint Hataları: 0
  - Dead Code: -1537
  - Orphaned Code: 3
  - TODO/FIXME: 4
  - Boş Metodlar: 3
  - Stub Metodlar: 3
  - Disabled Code: 0
  - Code Duplication: 119
  - Security Issues: 2
  - Performance Issues: 40
  - Dependency Issues: 10
  - Test Files: 20

✅ Detaylı rapor kaydedildi: .yalihan-bekci/reports/comprehensive-code-check-2025-11-11-141749.json
✅ Yalıhan Bekçi'ye öğretildi: .yalihan-bekci/knowledge/code-check-results-2025-11-11.json
```

---

## 🎯 KULLANIM ÖRNEKLERİ

### 1. Sadece Özet Görmek

```bash
php scripts/comprehensive-code-check.php | tail -20
```

### 2. Belirli Sorunları Listelemek

```bash
# Orphaned Code
LATEST=$(ls -t .yalihan-bekci/reports/comprehensive-code-check-*.json | head -1)
php -r "\$data = json_decode(file_get_contents('$LATEST'), true);
foreach (\$data['details']['orphaned_code'] ?? [] as \$file) {
    echo \$file . PHP_EOL;
}"

# Security Issues
php -r "\$data = json_decode(file_get_contents('$LATEST'), true);
foreach (\$data['details']['security'] ?? [] as \$issue) {
    echo \$issue['file'] . ':' . \$issue['line'] . ' - ' . \$issue['type'] . PHP_EOL;
}"

# Performance Issues
php -r "\$data = json_decode(file_get_contents('$LATEST'), true);
foreach (\$data['details']['performance'] ?? [] as \$issue) {
    echo \$issue['file'] . ':' . \$issue['line'] . ' - ' . \$issue['type'] . PHP_EOL;
}"
```

### 3. JSON Raporunu Okumak

```bash
# jq ile (eğer yüklüyse)
cat $LATEST | jq '.summary'

# PHP ile
php -r "\$data = json_decode(file_get_contents('$LATEST'), true);
echo json_encode(\$data['summary'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);"
```

---

## 🔧 GELECEKTEKİ İYİLEŞTİRMELER

1. **Kategori Bazlı Tarama**

    ```bash
    php scripts/comprehensive-code-check.php --category=security
    ```

2. **Format Seçenekleri**

    ```bash
    php scripts/comprehensive-code-check.php --format=json
    php scripts/comprehensive-code-check.php --format=markdown
    ```

3. **Exclude Patterns**
    ```bash
    php scripts/comprehensive-code-check.php --exclude="vendor/*,node_modules/*"
    ```

---

**Son Güncelleme:** 2025-11-11  
**Durum:** ✅ GÜNCEL VE ÇALIŞIYOR
