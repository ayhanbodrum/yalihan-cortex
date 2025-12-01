# 🛡️ Yalıhan Bekçi - AI Guardian System

**Son Güncelleme:** 30 Kasım 2025  
**Durum:** ✅ Aktif  
**Amaç:** Proje kod kalitesi, standartlar ve best practices takibi

---

## 🎯 Amaç

Yalıhan Bekçi, Yalıhan Emlak projesinin **AI Guardian System**'idir. Bu sistem:

- ✅ Kod kalitesi analizi yapar
- ✅ Dead code tespiti yapar
- ✅ Code duplication analizi yapar
- ✅ Performance sorunlarını tespit eder
- ✅ Security açıklarını analiz eder
- ✅ Test coverage takibi yapar
- ✅ Context7 compliance kontrolü yapar
- ✅ Öğrenilen pattern'leri saklar

---

## 📂 Klasör Yapısı

```
.yalihan-bekci/
├── README.md                          # Bu dosya
│
├── 📚 knowledge/                      # Bilgi Tabanı (Tek Kaynak)
│   ├── *.json                        # Öğrenilen pattern'ler
│   ├── *.md                          # Teknik dokümantasyonlar
│   └── analysis/                     # Sistem analizleri (konsolide edildi)
│
└── 📊 reports/                        # Güncel Analiz Raporları
    ├── comprehensive-code-check-*.json
    ├── dead-code-analysis-*.json
    └── *.md                          # Detaylı raporlar
```

---

## 🎯 Organizasyon Prensibi (Temiz Yapı)

**ÖNEMLİ:** Sistemin kuralları `.context7/authority.json` dosyasında tanımlıdır. Bu klasörde **SADECE** aktif ve gerekli veriler tutulur.

- ✅ **Knowledge:** Tüm bilgi, pattern ve analizler tek bir yerde (`knowledge/`)
- ✅ **Reports:** Sadece güncel ve aktif raporlar (`reports/`)
- ✅ **Clean:** Eski, tamamlanmış veya arşivlenmiş dosyalar silinir

---

## 📚 Ana Klasörler

### 1. `knowledge/` - Bilgi Tabanı

**Amaç:** Öğrenilen pattern'ler, standartlar, analizler ve best practices.

**İçerik:**
- Context7 standartları ve uyumluluk pattern'leri
- Database schema pattern'leri
- API entegrasyon pattern'leri
- Code quality pattern'leri
- Sistem analizleri (eski `analysis/` ve `learned/` klasörleri buraya birleştirildi)
- Standart dokümanlar (örn: `FILTERABLE_TRAIT_USAGE.md`)

**Format:** JSON ve Markdown

### 2. `reports/` - Analiz Raporları

**Amaç:** Güncel kod analizi ve kalite raporları.

**İçerik:**
- Comprehensive code check raporları
- Dead code analysis raporları
- Performance & Security raporları

**Not:** Sadece son 1 haftanın raporları tutulur. Eskiler otomatik temizlenir.

---

## 🔄 Rapor Yaşam Döngüsü

### 1. Oluşturma
- Raporlar otomatik olarak oluşturulur
- Tarih damgası ile kaydedilir

### 2. Kullanım
- Aktif raporlar `reports/` klasöründe tutulur
- Pattern'ler `knowledge/` klasörüne eklenir

### 3. Temizlik
- Eski raporlar ve tamamlanmış işlemler sistemden temizlenir
- "Temiz Yapı" prensibi gereği gereksiz dosya tutulmaz

---

## 🎯 Kullanım Senaryoları

### Kod Kalitesi Analizi

```bash
# Comprehensive code check
php scripts/comprehensive-code-check.php

# Rapor: .yalihan-bekci/reports/comprehensive-code-check-*.json
```

### Dead Code Temizliği

```bash
# Dead code analysis
php scripts/dead-code-analysis.php

# Rapor: .yalihan-bekci/reports/dead-code-analysis-*.json
```

---

## 🔗 İlgili Dokümantasyon

- **Context7 Standartları:** `.context7/authority.json`
- **Form Standartları:** `docs/active/FORM_STANDARDS.md`
- **Ana Dokümantasyon:** `docs/README.md`

---

## 📝 Notlar

- **Knowledge Base:** Sürekli güncellenir, pattern'ler öğrenilir
- **Reports:** Sadece güncel veriler tutulur
- **Temizlik:** Düzenli aralıklarla gereksiz dosyalar silinir

---

**Son Güncelleme:** 30 Kasım 2025  
**Versiyon:** 2.0 (Clean Structure)  
**Durum:** ✅ Aktif ve Güncel
