# 🎯 KOLAY KULLANIM REHBERİ

**Yalıhan Emlak - Pre-commit Hooks & Linting**

> **Motto:** Karmaşık komut yok, sadece basit ve otomatik! 🚀

**Son Güncelleme:** 2025-10-30

---

## 🎁 **3 KOMUT İLE HER ŞEY**

### **1️⃣ İLK KURULUM (Sadece Bir Kez)**

```bash
npm run setup
```

**Ne yapar?**

- ✅ Tüm araçları kurar (ESLint, Prettier, Husky)
- ✅ Pre-commit hooks'ları aktif eder
- ✅ Ayarları otomatik yapar
- ✅ Test eder, çalışıyor mu kontrol eder

**Süre:** ~2 dakika

---

### **2️⃣ NORMAL KULLANIM (Her Zaman)**

```bash
# Kod yazıyorsun...
# Değişiklikleri ekle:
git add .

# Commit yap (OTOMATIK KONTROL!):
git commit -m "feat: yeni özellik"
```

**Ne olur?**

```
⏳ Pre-commit kontrolleri çalışıyor...

  ✓ JavaScript kontrolü (ESLint)
  ✓ Kod formatı (Prettier)
  ✓ PHP kontrolü (CS Fixer)
  ✓ Context7 kuralları
  ✓ Yalıhan Bekçi standardları

✅ Her şey tamam! Commit başarılı.
```

**Hiçbir şey yapman gerekmiyor! Otomatik!** 🎉

---

### **3️⃣ HATA VARSA (Otomatik Düzelt)**

```bash
# Eğer commit engellendiyse:
npm run fix

# Tekrar commit:
git commit -m "feat: yeni özellik"
```

**`npm run fix` ne yapar?**

- ✅ Tüm kod formatını düzeltir
- ✅ JavaScript hatalarını düzeltir
- ✅ PHP formatını düzeltir
- ✅ console.log'ları kaldırır (production)
- ✅ Neo classes → Tailwind önerileri gösterir

---

## 📱 **KULLANIM ÖRNEKLERİ**

### **Senaryo 1: Normal Çalışma** ✅

```bash
# Kod yazdın, commit yapıyorsun:
git add .
git commit -m "feat: add user profile"

# Sistem otomatik kontrol ediyor...
✅ Tüm kontroller geçti!
[main abc1234] feat: add user profile
 3 files changed, 45 insertions(+)

# Bittin! Push yapabilirsin.
git push
```

**Senin işin:** Sadece `git commit` 🎯

---

### **Senaryo 2: Küçük Hata Var** ⚠️

```bash
# Commit yapıyorsun:
git commit -m "feat: new feature"

# Sistem kontrol ediyor...
❌ Hatalar bulundu:

  1. resources/js/core.js:45
     → console.log kullanımı (kaldırılmalı)

  2. resources/views/form.blade.php:12
     → Kod formatı bozuk

💡 Otomatik düzeltme için: npm run fix
```

**Çözüm:**

```bash
# Otomatik düzelt:
npm run fix

# Tekrar commit:
git add .
git commit -m "feat: new feature"

# ✅ Şimdi tamam!
```

---

### **Senaryo 3: Ciddi Hata (Context7)** 🚨

```bash
# Commit yapıyorsun:
git commit -m "feat: add migration"

# Sistem kontrol ediyor...
❌ CONTEXT7 İHLALİ!

  database/migrations/2025_10_30_create_users.php
  → 'durum' kullanımı yasak
  → 'status' kullanmalısın

🚫 Commit engellendi! Lütfen düzelt.
```

**Çözüm:**

```bash
# Manuel düzeltme gerekli (otomatik yapılamaz):
# migration dosyasını aç, 'durum' → 'status' yap

git add .
git commit -m "feat: add migration"

# ✅ Şimdi tamam!
```

---

## 🎨 **EK KOMUTLAR (Opsiyonel)**

Çoğu zaman ihtiyacın olmayacak, ama bilmekte fayda var:

```bash
# Sadece JavaScript kontrol et:
npm run lint:js

# Sadece PHP kontrol et:
npm run lint:php

# Sadece format düzelt (lint yapmadan):
npm run format

# Yalıhan Bekçi full kontrol:
php artisan standard:check

# Context7 kontrol:
php artisan context7:check
```

---

## ❓ **SIKÇA SORULAN SORULAR**

### **"npm run setup ne zaman çalıştırmalıyım?"**

Sadece:

- ✅ İlk kez projeyi klonladığında
- ✅ Yeni bir bilgisayara geçtiğinde
- ✅ node_modules silindiyse

**Normal çalışmada hiç gerek yok!**

---

### **"Her commit'te ne kontrol ediliyor?"**

```yaml
Otomatik Kontroller: ✓ JavaScript syntax hatası
    ✓ console.log kullanımı
    ✓ Kod formatı
    ✓ PHP standardı (PSR-12)
    ✓ Context7 kuralları
    ✓ Türkçe field isimleri
    ✓ Neo class kullanımı
    ✓ CSRF token eksikliği
    ✓ Label eksikliği (accessibility)
```

**Hepsi otomatik! Sen sadece `git commit` yap.**

---

### **"Çok yavaş olur mu?"**

Hayır! Çok hızlı:

- ⚡ Sadece değiştirdiğin dosyalar kontrol edilir
- ⚡ Paralel çalışır (hepsi aynı anda)
- ⚡ Ortalama süre: **2-5 saniye**

---

### **"Acil commit yapmam gerekirse?"**

```bash
# Kontrolleri atla (ÖNERİLMEZ!):
git commit -m "feat: urgent fix" --no-verify

# Ama sonra düzelt:
npm run fix
git add .
git commit -m "fix: code cleanup"
```

**Not:** `--no-verify` sadece acil durumlarda kullan!

---

### **"Eski commit'lere ne olacak?"**

Hiçbir şey! Sadece **yeni commit'ler** kontrol edilir.

Eski kodlar olduğu gibi kalır. Yeni düzenlemeler standartlara uyar.

---

## 🎯 **WORKFLOW ŞEMASI**

```
┌─────────────────┐
│  Kod Yaz        │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  git add .      │
└────────┬────────┘
         │
         ▼
┌─────────────────────────────┐
│  git commit -m "message"    │
└────────┬────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│  Pre-commit Hooks Çalışıyor │
│  (Otomatik!)                │
└────────┬────────────────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌───────┐  ┌──────────┐
│ ✅ OK │  │ ❌ HATA  │
└───┬───┘  └────┬─────┘
    │           │
    │           ▼
    │      ┌──────────────┐
    │      │ npm run fix  │
    │      └──────┬───────┘
    │             │
    │             ▼
    │      ┌──────────────┐
    │      │ git add .    │
    │      │ git commit   │
    │      └──────┬───────┘
    │             │
    └─────────────┘
         │
         ▼
┌─────────────────┐
│  Commit Başarılı│
│  git push       │
└─────────────────┘
```

---

## 🔑 **ÖZET: 3 KOMUT**

```bash
# 1. İlk kurulum (bir kez):
npm run setup

# 2. Normal kullanım (her zaman):
git commit -m "mesaj"

# 3. Hata varsa (otomatik düzelt):
npm run fix
```

**Bu kadar basit!** 🎉

---

## 💡 **BONUS: BASH ALIASES**

Terminal'de daha da kolay kullanım için:

```bash
# .bashrc veya .zshrc dosyana ekle:
alias gc='git commit'
alias gf='npm run fix && git add .'
alias gs='npm run setup'

# Kullanım:
gc -m "feat: new feature"  # git commit
gf                         # fix + add
gs                         # setup
```

---

**Hazırsın!** Artık her commit otomatik kontrollü! 🚀

**Sorun olursa:** `npm run fix` yap, halleder! 💪
