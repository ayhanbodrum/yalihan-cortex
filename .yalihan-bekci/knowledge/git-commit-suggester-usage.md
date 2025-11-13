# 🧠 Yalıhan Bekçi - Git Commit Suggester Kullanım Kılavuzu

**Tarih:** 2025-11-13  
**Durum:** ACTIVE  
**Öncelik:** HIGH

---

## 📋 ÖZET

Yalıhan Bekçi artık **git commit sıklığı** konusunda öğrenim yapıyor ve commit önerileri sunuyor!

### ✨ Özellikler

- ✅ Git değişikliklerini otomatik analiz eder
- ✅ Mantıksal gruplara ayırır
- ✅ Commit mesajı önerileri sunar
- ✅ Uyarılar verir (debug kodları, yarım kalmış özellikler)
- ✅ Öğrenme sistemi ile sürekli gelişir
- ✅ Pre-commit hook'a entegre

---

## 🚀 KULLANIM

### Manuel Kullanım

```bash
# Tüm kontroller ve öneriler
./yalihan-bekci/tools/git-commit-suggester.sh

# Sadece commit önerileri
./yalihan-bekci/tools/git-commit-suggester.sh --suggest

# Sadece kontroller
./yalihan-bekci/tools/git-commit-suggester.sh --check

# Sadece uyarılar
./yalihan-bekci/tools/git-commit-suggester.sh --warn
```

### Otomatik Kullanım (Pre-Commit Hook)

Her `git commit` yaptığınızda otomatik çalışır:

```bash
git commit -m "feat: yeni özellik"
```

**Çıktı:**

```
🔍 Context7 Pre-Commit Kontrolü Başlatılıyor...
...
🧠 Yalıhan Bekçi - Commit Önerisi...

💡 Commit Önerileri:

✅ Tek mantıksal grup:
   git commit -m "fix(context7): improve compliance"
```

---

## 📊 ANALİZ ÖZELLİKLERİ

### 1. Değişiklik Analizi

- Değişiklik yapılmış dosya sayısı
- Staged/Unstaged dosyalar
- Çok fazla değişiklik uyarısı (20+ dosya)

### 2. Mantıksal Gruplama

Değişiklikler şu gruplara ayrılır:

- **context7**: Context7 compliance düzeltmeleri
- **models**: Model değişiklikleri (`app/Models/*.php`)
- **controllers**: Controller değişiklikleri (`app/Http/Controllers/**/*.php`)
- **scripts**: Script değişiklikleri (`scripts/*.sh`, `scripts/*.php`)
- **docs**: Dokümantasyon değişiklikleri (`*.md`, `*.txt`)

### 3. Commit Mesajı Önerileri

Her grup için özel commit mesajı:

```bash
# Context7 düzeltmeleri
fix(context7): improve compliance

# Model değişiklikleri
fix(models): update field mappings

# Controller değişiklikleri
fix(controllers): update logic

# Script geliştirmeleri
enhance(scripts): improve functionality

# Dokümantasyon
docs: update documentation
```

### 4. Uyarılar

#### ⚠️ Uzun Süredir Commit Yapılmadı

```
⚠️  Uzun süredir commit yapılmadı (136 saat)
   💡 Mantıksal birimler tamamlandıysa commit yap!
```

**Kural:** 2+ saat commit yoksa uyarı verir

#### ⚠️ Çok Fazla Değişiklik

```
⚠️  Çok fazla değişiklik var (1401 dosya)
   💡 Mantıksal gruplara böl ve ayrı commit'ler yap!
```

**Kural:** 20+ dosya değiştiyse uyarı verir

#### ⚠️ Debug Kodları

```
⚠️  Debug kodları bulundu!
   💡 Commit yapmadan önce temizle: console.log, dd(), var_dump()
```

**Kural:** `console.log`, `dd()`, `var_dump()`, `print_r()`, `dump()` varsa uyarı verir

#### ⚠️ Yarım Kalmış Özellik

```
⚠️  Yarım kalmış özellik işaretleri bulundu (TODO, FIXME, HACK)
   💡 Özelliği tamamla, sonra commit yap!
```

**Kural:** `TODO`, `FIXME`, `HACK`, `XXX`, `BUG` varsa uyarı verir

---

## 🧠 ÖĞRENME SİSTEMİ

### Öğrenilen Pattern'ler

Her analiz `.yalihan-bekci/learned/` klasörüne kaydedilir:

```json
{
    "timestamp": "2025-11-13T17:36:01Z",
    "analysis": {
        "changed_files": 5,
        "staged_files": 3,
        "groups": ["context7", "models"],
        "suggestions": ["fix(context7): improve compliance", "fix(models): update field mappings"],
        "has_issues": false
    },
    "learned_patterns": {
        "commit_frequency": "optimal",
        "grouping_strategy": "by_logical_unit"
    }
}
```

### Öğrenme Süreci

1. **Analiz:** Git değişikliklerini analiz et
2. **Gruplama:** Mantıksal gruplara ayır
3. **Öneri:** Commit mesajı öner
4. **Kaydet:** Sonuçları `.yalihan-bekci/learned/` klasörüne kaydet
5. **Geliş:** Gelecek analizlerde daha iyi öneriler sun

---

## 📚 COMMIT SIKLIĞI KURALLARI

### Altın Kural

> **"Her mantıksal değişiklik = 1 commit"**
>
> Küçük ve sık commit yap, büyük ve nadir commit yapma!

### Ne Zaman Commit Yapılmalı?

#### ✅ Hemen Commit Yap

- Küçük bug düzeltmesi
- Typo düzeltmesi
- Tek dosya değişikliği
- Context7 compliance düzeltmesi
- Test geçiyor

#### ✅ Tamamlandığında Commit Yap

- Feature tamamlandı
- Refactoring bitti
- Migration hazır
- Component tamamlandı

#### ✅ Gün Sonunda Commit Yap

- Günlük temizlik
- Lint düzeltmeleri
- Dokümantasyon güncellemeleri

### Ne Zaman Commit Yapılmamalı?

#### ❌ Commit Yapma

- Kod çalışmıyor
- Test geçmiyor
- Yarım kalmış özellik
- Debug kodları var
- Geçici çözümler (TODO, FIXME, HACK)

---

## 🎯 ÖRNEK KULLANIM SENARYOLARI

### Senaryo 1: Küçük Düzeltmeler

```bash
# 09:00 - Bug düzeltildi
git add app/Models/Feature.php
./yalihan-bekci/tools/git-commit-suggester.sh --suggest
# Öneri: fix(models): update field mappings
git commit -m "fix(models): update field mappings"

# 10:30 - Başka bir düzeltme
git add app/Http/Controllers/Api/BookingRequestController.php
./yalihan-bekci/tools/git-commit-suggester.sh --suggest
# Öneri: fix(controllers): update logic
git commit -m "fix(controllers): update logic"
```

### Senaryo 2: Büyük Feature

```bash
# Feature başlangıcı
git checkout -b feature/user-profile

# 09:00 - Model oluşturuldu
git add app/Models/UserProfile.php
./yalihan-bekci/tools/git-commit-suggester.sh --suggest
# Öneri: feat(models): add UserProfile model
git commit -m "feat(models): add UserProfile model"

# 11:00 - Controller eklendi
git add app/Http/Controllers/UserProfileController.php
./yalihan-bekci/tools/git-commit-suggester.sh --suggest
# Öneri: feat(controllers): add UserProfileController
git commit -m "feat(controllers): add UserProfileController"
```

### Senaryo 3: Karışık Değişiklikler

```bash
# Çok fazla değişiklik var
git add .
./yalihan-bekci/tools/git-commit-suggester.sh --suggest

# Çıktı:
# 📦 3 mantıksal grup bulundu. Ayrı commit'ler önerilir:
#   1. git commit -m "fix(context7): improve compliance"
#   2. git commit -m "fix(models): update field mappings"
#   3. git commit -m "enhance(scripts): improve functionality"

# Ayrı commit'ler yap
git reset
git add app/Models/*.php
git commit -m "fix(context7): improve compliance"

git add app/Http/Controllers/**/*.php
git commit -m "fix(models): update field mappings"

git add scripts/*.sh
git commit -m "enhance(scripts): improve functionality"
```

---

## 🔧 ENTEGRASYON

### Pre-Commit Hook

`.githooks/pre-commit` dosyasına entegre edildi:

```bash
# 8. Yalıhan Bekçi - Commit Önerisi (Uyarı modunda)
echo ""
echo "🧠 Yalıhan Bekçi - Commit Önerisi..."
if [ -f ".yalihan-bekci/tools/git-commit-suggester.sh" ]; then
    bash .yalihan-bekci/tools/git-commit-suggester.sh --suggest --warn 2>/dev/null || true
fi
```

### Günlük Kontrol (Gelecek)

```bash
# .yalihan-bekci/tools/daily-commit-check.sh
# Uzun süredir commit yoksa uyar
```

---

## 📖 REFERANSLAR

- **Knowledge Base:** `.yalihan-bekci/knowledge/git-commit-frequency-standards-2025-11-13.json`
- **Tool:** `.yalihan-bekci/tools/git-commit-suggester.sh`
- **Pre-Commit Hook:** `.githooks/pre-commit`
- **Standards Guide:** `docs/rules/STANDARDIZATION_GUIDE.md`

---

## 🎯 SONUÇ

Yalıhan Bekçi artık:

- ✅ Git değişikliklerini izliyor
- ✅ Commit sıklığı kurallarını öğreniyor
- ✅ Commit önerileri sunuyor
- ✅ Uyarılar veriyor
- ✅ Sürekli gelişiyor

**Kullanım:** Her `git commit` öncesi otomatik çalışır ve öneriler sunar!

---

**Son Güncelleme:** 2025-11-13  
**Versiyon:** 1.0.0  
**Durum:** ACTIVE ✅
