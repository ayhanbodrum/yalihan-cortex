# Derin Hata Analizi Script'leri - 2025-11-11

**Tarih:** 2025-11-11 16:10  
**Durum:** ✅ AKTİF

---

## 🎯 EN DERİN HATA ANALİZİ YAPAN SCRİPT

### ⭐ `comprehensive-code-check.php` - EN KAPSAMLI

**Bu script en derinlemesine hata araştırması yapar!**

**Kontrol Ettiği 10 Kategori:**

1. **Lint Hataları** (Syntax, Type)
   - PHP syntax kontrolü
   - Type hataları
   - Parse hataları

2. **Dead Code** (Kullanılmayan kodlar)
   - Kullanılmayan sınıflar
   - Kullanılmayan metodlar
   - Çağrılmayan fonksiyonlar

3. **Orphaned Code** (Yetim kodlar)
   - Route'a bağlı olmayan controller'lar
   - Kullanılmayan controller'lar

4. **Incomplete Implementation** (Yarım kalmış kod)
   - TODO/FIXME/HACK yorumları
   - Boş metodlar
   - Stub metodlar (return null; ile biten)

5. **Disabled Code** (Devre dışı kodlar)
   - Yorum satırına alınmış route'lar
   - TEMPORARILY DISABLED kodlar

6. **Code Duplication** (Kod tekrarı)
   - Benzer metod imzaları
   - Tekrarlanan kod blokları

7. **Security Issues** (Güvenlik)
   - SQL injection riskleri
   - CSRF koruması eksikliği
   - Güvenlik açıkları

8. **Performance Issues** (Performans)
   - N+1 query potansiyeli
   - Loop içinde database query
   - Eager loading eksikliği

9. **Dependency Issues** (Bağımlılıklar)
   - Kullanılmayan paketler
   - Eksik bağımlılıklar

10. **Code Coverage** (Test kapsamı)
    - Test dosyaları sayısı
    - Test sınıfları

---

## 📊 KULLANIM

```bash
# Kapsamlı kod kontrolü çalıştır
php scripts/comprehensive-code-check.php

# Ne yapar:
# ✅ 10 farklı kategori kontrol eder
# ✅ Detaylı JSON rapor oluşturur
# ✅ Yalıhan Bekçi'ye öğretir
# ✅ Öneriler sunar
```

**Rapor Konumu:**
- `.yalihan-bekci/reports/comprehensive-code-check-YYYY-MM-DD-HHMMSS.json`
- `.yalihan-bekci/knowledge/code-check-results-YYYY-MM-DD.json`

---

## 🔍 DİĞER HATA ANALİZİ SCRİPTLERİ

### 2. `find-incomplete-code.php` - Yarım Kalmış Kod

**Kontrol Ettiği:**
- TODO/FIXME/HACK yorumları
- Boş metodlar (stub)
- Devre dışı bırakılmış kodlar
- Kullanılmayan route'lar
- Yorum satırına alınmış kod blokları

**Kullanım:**
```bash
php scripts/find-incomplete-code.php
```

---

### 3. `context7-full-scan.sh` - Context7 Compliance

**Kontrol Ettiği:**
- `order` → `display_order` ihlalleri
- `durum` → `status` ihlalleri
- `aktif` → `status` ihlalleri
- `sehir` → `il` ihlalleri
- `musteri_*` → `kisi_*` ihlalleri
- `neo-*` CSS class ihlalleri
- `layouts.app` ihlalleri
- `crm.*` route ihlalleri

**Kullanım:**
```bash
./scripts/context7-full-scan.sh --report
```

---

### 4. `context7-compliance-scanner.php` - PHP Tabanlı Scanner

**Kontrol Ettiği:**
- Context7 kurallarına aykırı pattern'ler
- Detaylı regex analizi
- Kategorize edilmiş ihlal raporu

**Kullanım:**
```bash
php scripts/context7-compliance-scanner.php --report
```

---

## 📈 KARŞILAŞTIRMA

| Script | Derinlik | Kapsam | Hız | Rapor |
|--------|----------|--------|-----|-------|
| `comprehensive-code-check.php` | ⭐⭐⭐⭐⭐ | 10 kategori | Orta | JSON |
| `find-incomplete-code.php` | ⭐⭐⭐ | 5 kategori | Hızlı | Text |
| `context7-full-scan.sh` | ⭐⭐⭐⭐ | Context7 kuralları | Hızlı | Markdown/JSON |
| `context7-compliance-scanner.php` | ⭐⭐⭐⭐ | Context7 kuralları | Orta | JSON/Markdown |

---

## 🎯 HANGİSİNİ KULLANMALI?

### Derin Hata Analizi İçin:
**`comprehensive-code-check.php`** ⭐ EN İYİSİ
- En kapsamlı analiz
- 10 farklı kategori
- Güvenlik ve performans kontrolü
- Dead code analizi

### Hızlı Kontrol İçin:
**`context7-full-scan.sh`**
- Context7 kuralları kontrolü
- Hızlı tarama
- Markdown rapor

### Yarım Kalmış Kod İçin:
**`find-incomplete-code.php`**
- TODO/FIXME bulma
- Boş metodlar
- Devre dışı kodlar

---

## 📋 ÖNERİLEN KULLANIM

### Günlük Kontrol:
```bash
# Hızlı Context7 kontrolü
./scripts/context7-full-scan.sh
```

### Haftalık Derin Analiz:
```bash
# Kapsamlı kod kontrolü
php scripts/comprehensive-code-check.php
```

### Release Öncesi:
```bash
# Tüm kontrolleri çalıştır
php scripts/comprehensive-code-check.php
./scripts/context7-full-scan.sh --report
php scripts/find-incomplete-code.php
```

---

## ✅ SONUÇ

**En derin hata analizi yapan script:**
**`comprehensive-code-check.php`** ⭐

Bu script:
- ✅ 10 farklı kategori kontrol eder
- ✅ Dead code, security, performance analizi yapar
- ✅ Detaylı JSON rapor oluşturur
- ✅ Yalıhan Bekçi'ye öğretir
- ✅ Öneriler sunar

---

**Son Güncelleme:** 2025-11-11 16:10  
**Durum:** ✅ AKTİF

