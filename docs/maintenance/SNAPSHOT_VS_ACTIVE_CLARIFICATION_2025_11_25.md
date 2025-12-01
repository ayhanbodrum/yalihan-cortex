# 📸 Snapshot Raporları vs Aktif Standartlar - Netleştirme

**Tarih:** 25 Kasım 2025  
**Konu:** MD_AUDIT ve Arşiv Klasörleri Yorum Karışıklığı  
**Durum:** ✅ NETLEŞTİRİLDİ

---

## 🎯 SORUN

Kullanıcı tespit ettiği önemli nokta:

> "Bazı dosyalar yenilenmiş, ihlaller düzenlenmiş ama hâlâ yapılacak gibi algılanıyor"

**Sebep:**

- `MD_AUDIT_SUMMARY.txt` → **SNAPSHOT** (anlık durum)
- `docs/archive/**` → **TARİHSEL KAYIT**
- `.context7/archive/**` → **TARİHSEL KAYIT**
- Yalıhan Bekçi bu klasörleri tarıyor → Eski ihlalleri "yapılacak iş" gibi gösteriyor

---

## ✅ GERÇEK DURUM

### 1. Aktif Standart Kaynakları

| Dosya                                       | Durum    | Rol                          |
| ------------------------------------------- | -------- | ---------------------------- |
| `.context7/authority.json`                  | ✅ AKTIF | TEK OTORİTE                  |
| `.context7/PERMANENT_STANDARDS.md`          | ✅ AKTIF | Geri dönüşü olmayan kurallar |
| `.context7/FORBIDDEN_PATTERNS.md`           | ✅ AKTIF | Yasak desenler referansı     |
| `docs/active/RULES_KONSOLIDE_2025_11_25.md` | ✅ AKTIF | Birleştirilmiş kurallar      |
| `YALIHAN_BEKCI_EGITIM_DOKUMANI.md`          | ✅ AKTIF | Bekçi eğitimi                |

### 2. Arşiv / Tarihsel Kayıt

| Klasör                             | Durum    | Amaç                      |
| ---------------------------------- | -------- | ------------------------- |
| `docs/archive/**`                  | 📦 ARŞİV | Geçmiş raporlar, referans |
| `.context7/archive/**`             | 📦 ARŞİV | Eski compliance raporları |
| `yalihan-bekci/reports/archive/**` | 📦 ARŞİV | Eski günlük raporlar      |

**README'den (.context7/README.md):**

```markdown
## 📁 Arşiv

Eski raporlar ve geçici analizler `.context7/archive/` klasöründe saklanır:

- **Eski compliance raporları**
- **Geçici analiz raporları**
- **Eski log dosyaları**
- **Daily reports arşivi**

**Not:** Arşivlenmiş dosyalar referans amaçlıdır, aktif kullanılmaz.
```

### 3. Snapshot Raporlar (Yanlış Yorum Riski Yüksek)

| Dosya                                                | Tarih        | Durum         | Açıklama                        |
| ---------------------------------------------------- | ------------ | ------------- | ------------------------------- |
| `yalihan-bekci/reports/2025-11/MD_AUDIT_SUMMARY.txt` | Kasım 2025   | 📸 SNAPSHOT   | 441 MD dosyasının o anki durumu |
| `docs/cleanup/MD_CLEANUP_ANALYSIS.md`                | 15 Ekim 2025 | 📸 SNAPSHOT   | Temizlik sprint'i analizi       |
| `docs/cleanup/CLEANUP_SUCCESS_REPORT.md`             | 15 Ekim 2025 | ✅ TAMAMLANDI | "Durum A+" sonuç raporu         |

**Snapshot'ların doğası:**

- Anlık durum fotoğrafı
- Sonraki düzeltmeler yansımaz
- [outdated] / [duplicate_hint] flag'leri o anki durum içindi
- **YAPILACAK İŞ DEĞİL, TARİHSEL KAYIT**

---

## 🔍 TUTARSIZLİK TİPLERİ (Algı vs Gerçek)

### Tip 1: Konsolide Edilmiş Kurallar

**Eski dosyalar:**

- `docs/rules/master-rules.md`
- `docs/rules/STANDARDIZATION_GUIDE.md`
- `docs/rules/instructions/ai-model-kurallari.instructions.md`

**Yeni konsoli date:**

- `docs/active/RULES_KONSOLIDE_2025_11_25.md` ← TEK KAYNAK

**MD_AUDIT durumu:**

```
[outdated,duplicate_hint] eski dosyalar için
```

**Gerçek durum:**
✅ Eski dosyalar kasıtlı olarak referans amaçlı tutulmuş  
✅ Konsolide dosya aktif kaynak  
❌ "Hâlâ yapılacak iş var" DEĞİL

---

### Tip 2: .context7/archive/ İçindeki Eski Compliance Raporları

**Örnek:**

```
.context7/archive/2025-11/
├── compliance-report-final-20251111-134607.md
├── compliance-report-final-20251111-134619.md
└── MIGRATION_IHLAL_COZUM_PLANI_2025-11-11.md
```

**MD_AUDIT durumu:**

```
[outdated] işaretli
```

**Gerçek durum:**
✅ `.context7/README.md` açıkça "arşiv" diyor  
✅ Tarihsel kayıt, aktif kullanılmaz  
❌ "Hâlâ yapılacak iş var" DEĞİL

---

### Tip 3: Yalıhan Bekçi Eğitim Dokümanları

**Eski dosyalar:**

```
docs/archive/november-2025/YALIHAN-BEKCI-OGRENME-RAPORU-2025-11-02.md
```

**Aktif dosya:**

```
YALIHAN_BEKCI_EGITIM_DOKUMANI.md (2025-11-12)
```

**MD_AUDIT durumu:**

```
[outdated] eski rapor için
```

**Gerçek durum:**
✅ Eğitim dokümanı aktif ve güncel  
✅ Eski öğrenme raporu tarihsel kayıt  
❌ "Hâlâ yapılacak iş var" DEĞİL

---

## 🛠️ ÇÖZÜM PLANI

### 1. Yalıhan Bekçi Tarama Konfigürasyonu Güncelle

**Hedef:** Archive klasörlerini taramadan dışla

```json
{
    "md_duplicate_detector": {
        "excludePaths": [
            "docs/archive",
            ".context7/archive",
            "yalihan-bekci/reports/archive",
            "vendor",
            "node_modules"
        ]
    },
    "cleanup_analyzer": {
        "excludePaths": ["docs/archive", ".context7/archive", "yalihan-bekci/reports/archive"]
    },
    "context7_validate": {
        "excludePaths": ["docs/archive", ".context7/archive"]
    }
}
```

**Dosya:** `yalihan-bekci/config/scan-config.json` (YENİ)

---

### 2. MD_AUDIT_SUMMARY.txt Arşive Taşı

**Mevcut konum:**

```
yalihan-bekci/reports/2025-11/MD_AUDIT_SUMMARY.txt
```

**Yeni konum:**

```
yalihan-bekci/reports/archive/2025-11/MD_AUDIT_SUMMARY_SNAPSHOT_2025_11.txt
```

**Dosya başına not ekle:**

```markdown
# MD AUDIT SUMMARY - SNAPSHOT (Kasım 2025)

⚠️ BU BİR SNAPSHOT RAPORUDUR ⚠️

Bu rapor Kasım 2025'teki anlık durumu gösterir.
[outdated] ve [duplicate_hint] işaretleri o anki durum içindi.

Arşiv klasörlerindeki işaretler (docs/archive/, .context7/archive/)
"yapılacak iş" DEĞIL, tarihsel kayıttır.

Güncel aktif standartlar:

- .context7/authority.json
- docs/active/RULES_KONSOLIDE_2025_11_25.md
- YALIHAN_BEKCI_EGITIM_DOKUMANI.md
```

---

### 3. Yalıhan Bekçi Eğitim Dokümanına Not Ekle

**Dosya:** `YALIHAN_BEKCI_EGITIM_DOKUMANI.md`

**Eklenecek bölüm:** (Sayfa 2, "Temel Kavramlar" altında)

````markdown
### 📸 Snapshot Raporlar vs Aktif Standartlar

**KRITIK:** Audit raporları SNAPSHOT'tır, yapılacak iş listesi DEĞİL.

| Klasör                             | Durum    | Yorum                           |
| ---------------------------------- | -------- | ------------------------------- |
| `docs/archive/**`                  | 📦 ARŞİV | Tarihsel kayıt, tarama dışı tut |
| `.context7/archive/**`             | 📦 ARŞİV | Tarihsel kayıt, tarama dışı tut |
| `yalihan-bekci/reports/archive/**` | 📦 ARŞİV | Snapshot raporlar, referans     |

**Örnek:**

- MD_AUDIT_SUMMARY.txt içinde [outdated] var
- Ama docs/archive/ klasöründe
- → Bu "yapılacak iş" DEĞİL, tarihsel kayıt

**Tarama yapılırken:**

```bash
# ✅ DOĞRU
grep -r "forbidden_pattern" --exclude-dir="archive" app/

# ❌ YANLIŞ
grep -r "forbidden_pattern" docs/  # archive dahil
```
````

````

---

### 4. .context7/README.md Güncelle (İyileştirme)

**Mevcut:**
```markdown
## 📁 Arşiv

**Not:** Arşivlenmiş dosyalar referans amaçlıdır, aktif kullanılmaz.
````

**Güncel (daha net):**

```markdown
## 📁 Arşiv

Eski raporlar ve geçici analizler `.context7/archive/` klasöründe saklanır:

- **Eski compliance raporları** - Tarihsel kayıt
- **Geçici analiz raporları** - Referans
- **Eski log dosyaları** - Audit trail
- **Daily reports arşivi** - Snapshot'lar

**⚠️ ÖNEMLİ:**

- Arşivlenmiş dosyalar **referans amaçlıdır**, aktif kullanılmaz
- [outdated] / [duplicate_hint] flag'leri **o anki durum** içindi
- **"Yapılacak iş" değil, tarihsel kayıttır**
- Yalıhan Bekçi taraması bu klasörleri **dışarıda bırakmalı**

**Aktif standart kaynakları:**

- `.context7/authority.json`
- `.context7/PERMANENT_STANDARDS.md`
- `.context7/FORBIDDEN_PATTERNS.md`
- `docs/active/RULES_KONSOLIDE_2025_11_25.md`
```

---

## 📊 ÖNCESI & SONRASI

### ÖNCE (Mevcut Durum)

```
Yalıhan Bekçi:
  ├─ docs/archive/ taranıyor
  ├─ .context7/archive/ taranıyor
  ├─ MD_AUDIT_SUMMARY.txt snapshot
  └─ [outdated] flag'leri "yapılacak iş" gibi algılanıyor

AI Ajanları:
  ├─ Arşivdeki [outdated] görüyor
  ├─ "Hâlâ düzeltilmemiş" zannediyor
  └─ Gereksiz düzeltme önerileri yapıyor
```

### SONRA (Hedef Durum)

```
Yalıhan Bekçi:
  ├─ docs/archive/ ✅ EXCLUDE
  ├─ .context7/archive/ ✅ EXCLUDE
  ├─ MD_AUDIT arşive taşınmış ✅
  └─ Snapshot raporlar NET açıklanmış ✅

AI Ajanları:
  ├─ Sadece aktif standartları okuyor ✅
  ├─ Arşiv = tarihsel kayıt olarak anlıyor ✅
  └─ Gereksiz öneri yok ✅
```

---

## ✅ DOĞRULAMA CHECKLİSTİ

- [ ] `yalihan-bekci/config/scan-config.json` oluşturuldu
- [ ] Archive klasörleri exclude edildi
- [ ] MD_AUDIT_SUMMARY arşive taşındı + başlık notu eklendi
- [ ] YALIHAN_BEKCI_EGITIM_DOKUMANI.md güncellendi
- [ ] .context7/README.md iyileştirildi
- [ ] Yeni tarama testi yapıldı (archive dışında)
- [ ] AI ajanlarına snapshot kavramı öğretildi

---

## 📖 REFERANSLAR

- `.context7/README.md` - Archive klasörü açıklaması
- `.context7/authority.json` - Tek yetkili kaynak
- `docs/active/RULES_KONSOLIDE_2025_11_25.md` - Konsolide kurallar
- `YALIHAN_BEKCI_EGITIM_DOKUMANI.md` - Bekçi eğitimi

---

**Sonuç:** ✅ Snapshot raporlar ≠ Yapılacak işler. Arşiv = Tarihsel kayıt.
