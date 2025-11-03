# 📁 Docs Klasörü Temizlik ve Organizasyon Önerileri

## 📊 Mevcut Durum Analizi

### **Karmaşık Dosyalar (Öncelikli)**

| Dosya                                        | Satır Sayısı | Karmaşıklık | Öneri                               |
| -------------------------------------------- | ------------ | ----------- | ----------------------------------- |
| `context7-rules.md`                          | 1,000+       | ⭐⭐⭐⭐⭐  | Böl ve yeniden organize et          |
| `next-steps-roadmap-2025.md`                 | 491          | ⭐⭐⭐⭐    | Güncel bilgileri ana roadmap'e taşı |
| `tkgm-parsel-entegrasyonu-implementation.md` | 615          | ⭐⭐⭐⭐    | Teknik detayları ayrı dosyalara böl |
| `tkgm-php-class-entegrasyonu-2025.md`        | 400+         | ⭐⭐⭐      | API dokümantasyonuna taşı           |
| `users-schema.md`                            | 300+         | ⭐⭐⭐      | Database schema klasörüne taşı      |
| `ilanlar-schema.md`                          | 250+         | ⭐⭐⭐      | Database schema klasörüne taşı      |

### **Benzer İçerikli Dosyalar**

#### **TKGM Entegrasyonu Grubu**

-   `tkgm-parsel-entegrasyonu-implementation.md`
-   `tkgm-php-class-entegrasyonu-2025.md`
-   `live-search-tkgm-entegrasyonu-2025.md`

**Öneri**: `docs/integrations/tkgm/` klasörü oluştur ve birleştir.

#### **Roadmap Grubu**

-   `next-steps-roadmap-2025.md`
-   `ilan-sistemi-roadmap-2025.md`
-   `enterprise-ilan-sistemi-roadmap-2025.md`
-   `context7-roadmap-2025.md`

**Öneri**: `docs/roadmaps/` klasörü oluştur ve organize et.

#### **Schema Grubu**

-   `users-schema.md`
-   `ilanlar-schema.md`
-   `kisiler-schema.md`
-   `neo-design-schema.md`

**Öneri**: `docs/database/schemas/` klasörü oluştur.

## 🗂️ Önerilen Yeni Klasör Yapısı

```
docs/
├── 📋 README.md (Ana dokümantasyon haritası)
├── 🏠 modules/ (Modül dokümantasyonları)
│   ├── 01-modul-auth.md
│   ├── 02-modul-emlaklar.md
│   └── ...
├── 🛠️ technical/ (Teknik dokümantasyonlar)
│   ├── database/
│   │   ├── schemas/
│   │   │   ├── users-schema.md
│   │   │   ├── ilanlar-schema.md
│   │   │   └── kisiler-schema.md
│   │   └── migrations/
│   ├── api/
│   │   ├── hybrid-search-api.md
│   │   └── tkgm-api.md
│   └── performance/
├── 🚀 roadmaps/ (Geliştirme planları)
│   ├── 2025/
│   │   ├── main-roadmap.md
│   │   ├── ilan-sistemi-roadmap.md
│   │   └── enterprise-roadmap.md
│   └── archive/
├── 🔗 integrations/ (Entegrasyon dokümantasyonları)
│   ├── tkgm/
│   │   ├── parsel-entegrasyonu.md
│   │   ├── php-class-entegrasyonu.md
│   │   └── live-search-entegrasyonu.md
│   ├── ai/
│   └── maps/
├── 📊 context7/ (Context7 raporları)
│   ├── rules/
│   │   ├── core-rules.md
│   │   ├── database-rules.md
│   │   └── ui-rules.md
│   ├── reports/
│   └── compliance/
└── 📚 archive/ (Eski dokümantasyonlar)
    ├── 2024/
    └── legacy/
```

## 🎯 Temizlik Önerileri

### **1. Acil Öncelikli (Hemen Yapılacak)**

#### **A. Context7 Rules Bölünmesi**

```bash
# Mevcut dosyayı böl
docs/context7/rules/core-rules.md          # Temel kurallar
docs/context7/rules/database-rules.md      # Veritabanı kuralları
docs/context7/rules/ui-rules.md            # UI/UX kuralları
docs/context7/rules/forbidden-patterns.md  # Yasaklı pattern'ler
```

#### **B. TKGM Entegrasyonları Birleştirme**

```bash
# Yeni klasör oluştur
mkdir -p docs/integrations/tkgm/

# Dosyaları taşı ve birleştir
mv tkgm-parsel-entegrasyonu-implementation.md integrations/tkgm/
mv tkgm-php-class-entegrasyonu-2025.md integrations/tkgm/
mv live-search-tkgm-entegrasyonu-2025.md integrations/tkgm/
```

### **2. Orta Öncelikli (1-2 Hafta)**

#### **A. Roadmap Konsolidasyonu**

-   Tüm roadmap'leri `docs/roadmaps/2025/` altında topla
-   Ana roadmap'i güncelle ve diğerlerini referans olarak işaretle
-   Eski roadmap'leri archive'e taşı

#### **B. Schema Dokümantasyonları**

-   Tüm schema dosyalarını `docs/technical/database/schemas/` altında topla
-   Standart format oluştur
-   Cross-reference'ları güncelle

### **3. Uzun Vadeli (1 Ay)**

#### **A. Archive Sistemi**

-   2024 ve önceki dokümantasyonları archive'e taşı
-   Legacy dokümantasyonları işaretle
-   Eski versiyonları temizle

#### **B. Otomatik Dokümantasyon**

-   Context7 sistemi ile otomatik güncelleme
-   Cross-reference validation
-   Dead link detection

## 📋 Temizlik Komutları

### **Klasör Oluşturma**

```bash
cd docs/

# Ana klasörleri oluştur
mkdir -p integrations/tkgm
mkdir -p roadmaps/2025
mkdir -p technical/database/schemas
mkdir -p context7/rules
mkdir -p archive/2024
```

### **Dosya Taşıma**

```bash
# TKGM dosyalarını taşı
mv tkgm-*.md integrations/tkgm/
mv live-search-tkgm-*.md integrations/tkgm/

# Schema dosyalarını taşı
mv *-schema.md technical/database/schemas/

# Roadmap dosyalarını taşı
mv *roadmap*.md roadmaps/2025/

# Context7 dosyalarını taşı
mv context7-*.md context7/reports/
```

### **Cross-Reference Güncelleme**

```bash
# README-detailed.md güncelle
sed -i 's|docs/|docs/roadmaps/2025/|g' README-detailed.md

# Authority.json güncelle
sed -i 's|docs/ilan-sistemi-roadmap-2025.md|docs/roadmaps/2025/ilan-sistemi-roadmap.md|g' .context7/authority.json
```

## 🎯 Başarı Metrikleri

### **Öncesi**

-   **Toplam Dosya**: 54+
-   **Karmaşık Dosya**: 6 dosya (1000+ satır)
-   **Benzer İçerik**: 12 dosya
-   **Organizasyon Skoru**: 3/10

### **Sonrası Hedef**

-   **Toplam Dosya**: 60+ (daha iyi organize)
-   **Karmaşık Dosya**: 0 dosya (hepsi bölündü)
-   **Benzer İçerik**: 0 dosya (birleştirildi)
-   **Organizasyon Skoru**: 9/10

## 🚀 Uygulama Planı

### **Hafta 1: Acil Temizlik**

-   [ ] Context7 rules bölünmesi
-   [ ] TKGM entegrasyonları birleştirme
-   [ ] README güncellemeleri

### **Hafta 2: Orta Öncelik**

-   [ ] Roadmap konsolidasyonu
-   [ ] Schema dokümantasyonları
-   [ ] Cross-reference güncellemeleri

### **Hafta 3-4: Uzun Vadeli**

-   [ ] Archive sistemi
-   [ ] Otomatik dokümantasyon
-   [ ] Final validation

## 📊 Beklenen Faydalar

### **Geliştirici Deneyimi**

-   ⚡ **%60 daha hızlı** dokümantasyon bulma
-   🎯 **%80 daha az** karmaşıklık
-   📚 **%90 daha iyi** organizasyon

### **Bakım Kolaylığı**

-   🔄 **Otomatik güncelleme** sistemi
-   🧹 **Temiz klasör yapısı**
-   📋 **Standardize edilmiş format**

---

**Tarih**: 30 Ocak 2025  
**Durum**: Öneriler hazır  
**Sonraki Adım**: Temizlik planının uygulanması
