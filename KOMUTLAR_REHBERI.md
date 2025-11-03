# 🎯 TÜM KOMUTLAR REHBERİ

**Yalıhan Emlak - Hızlı Komut Referansı**

**Son Güncelleme:** 2025-10-30

---

## 📚 İÇİNDEKİLER

1. [Günlük Kullanım](#-günlük-kullanım) (En çok kullanılanlar)
2. [Dokümantasyon](#-dokümantasyon)
3. [Yalıhan Bekçi](#-yalıhan-bekçi)
4. [Linting & Formatting](#-linting--formatting)
5. [Git & Commit](#-git--commit)
6. [Database](#-database)
7. [Testing](#-testing)
8. [Eski Komutlar](#-eski-komutlar-durumu)

---

## ⭐ GÜNLÜK KULLANIM

### **En Sık Kullanılan 10 Komut:**

```bash
# 1. Commit yap (otomatik kontrol ile)
git commit -m "feat: yeni özellik"

# 2. Hataları otomatik düzelt
npm run fix

# 3. Standart kontrolü
php artisan standard:check

# 4. Development server
php artisan serve

# 5. Vite dev server
npm run dev

# 6. Yeni form component
php artisan make:form-component input

# 7. Bekçi başlat
./scripts/bekci-watch.sh start

# 8. Dokümanda ara
./scripts/search-docs.sh "Context7"

# 9. Cache temizle
php artisan cache:clear && php artisan view:clear

# 10. Database migrate
php artisan migrate
```

---

## 📚 DOKÜMANTASYON

### **Dokümanlara Erişim:**

```bash
# Kritik dosyalara git
cd docs/active/

# Dosyaları listele
ls -lh

# Önemli dokümanlartions:
cat docs/active/CONTEXT7-MASTER-GUIDE.md
cat docs/active/DATABASE-SCHEMA.md
cat docs/active/API-REFERENCE.md
```

### **Dokümanda Arama:**

```bash
# Aktif + archive dosyalarında ara
./scripts/search-docs.sh "Context7"

./scripts/search-docs.sh "AI System"

./scripts/search-docs.sh "database schema"

# Sonuç:
# → Tüm docs/ klasöründe arar
# → active/ ve archive/ dahil
# → Renklendirmeli çıktı
```

### **Index Güncelleme:**

```bash
# Yeni dosya eklediysen index'i güncelle
./scripts/generate-doc-index.sh

# Ne yapar?
# → docs/ klasöründeki tüm MD dosyaları listeler
# → index.md dosyası oluşturur
# → Kategori bazlı organize eder
```

### **Archive İşlemleri:**

```bash
# Archive'den dosya çıkar
cd docs/archive/

# Eski dökümanlar
tar -xzf legacy-docs-2024-2025.tar.gz

# 2024 arşivi
tar -xzf 2024-docs-archive.tar.gz

# Belirli dosya çıkar
tar -xzf legacy-docs-2024-2025.tar.gz specific-file.md
```

---

## 🛡️ YALIHAN BEKÇİ

### **Bekçi Yönetimi:**

```bash
# Bekçi'yi başlat (background)
./scripts/bekci-watch.sh start

# Durum kontrolü
./scripts/bekci-watch.sh status

# Canlı log takibi
./scripts/bekci-watch.sh logs follow

# Son logları göster
./scripts/bekci-watch.sh logs

# Durdur
./scripts/bekci-watch.sh stop

# Yeniden başlat
./scripts/bekci-watch.sh restart
```

### **Günlük Workflow (Önerilen):**

```bash
# Sabah (işe başlarken)
./scripts/bekci-watch.sh start

# Başka terminalde canlı takip
./scripts/bekci-watch.sh logs follow

# Geliştirme yaparken Bekçi arka planda çalışır...
# Dosya değişikliklerini otomatik izler

# Akşam (iş bitimi)
./scripts/bekci-watch.sh stop
```

### **Bekçi Ne İzler?**

```yaml
İzlenen Dosyalar:
  - app/**/*.php (Models, Controllers)
  - database/migrations/*.php
  - resources/views/**/*.blade.php
  - routes/*.php

Kontroller:
  ✓ Context7 compliance
  ✓ Forbidden patterns (durum, aktif, etc.)
  ✓ Turkish field names
  ✓ Code quality issues

Raporlama:
  ✓ storage/logs/bekci-watch.log
  ✓ Real-time console output
  ✓ Violation alerts
```

---

## 🔍 LINTING & FORMATTING

### **Quick Commands:**

```bash
# Hepsini düzelt (EN KULLANIŞLI!)
npm run fix

# Sadece kontrol et (düzeltme)
npm run lint

# Sadece JavaScript
npm run lint:js

# Sadece PHP
npm run lint:php

# Sadece format (prettier)
npm run format

# PHP CS Fixer (manuel)
./vendor/bin/php-cs-fixer fix

# ESLint (manuel)
npx eslint resources/js --fix
```

### **Standart Kontrol:**

```bash
# Tüm standartları kontrol
php artisan standard:check

# Sadece CSS
php artisan standard:check --type=css

# Sadece JavaScript
php artisan standard:check --type=js

# Sadece PHP
php artisan standard:check --type=php

# Sadece Blade
php artisan standard:check --type=blade

# Sadece Context7
php artisan standard:check --type=context7

# Otomatik düzelt
php artisan standard:check --fix

# Detaylı rapor
php artisan standard:check --report
```

---

## 📝 GIT & COMMIT

### **Normal Workflow:**

```bash
# 1. Değişiklikleri gör
git status

# 2. Değişiklikleri ekle
git add .

# 3. Commit yap (OTOMATIK KONTROL!)
git commit -m "feat: yeni özellik"

# → Pre-commit hooks çalışır:
#   ✓ Prettier (formatting)
#   ✓ Context7 (compliance)
#   ✓ Yalıhan Bekçi (standards)

# 4. Eğer hata varsa:
npm run fix
git add .
git commit -m "feat: yeni özellik"

# 5. Push
git push
```

### **Commit Mesaj Formatı:**

```bash
# Yeni özellik
git commit -m "feat: kullanıcı profil sayfası eklendi"

# Bug düzeltme
git commit -m "fix: login hatası düzeltildi"

# Dokümantasyon
git commit -m "docs: API dokümantasyonu güncellendi"

# Stil/Format
git commit -m "style: kod formatı düzenlendi"

# Refactoring
git commit -m "refactor: kod optimize edildi"

# Test
git commit -m "test: unit testler eklendi"

# Bakım
git commit -m "chore: dependencies güncellendi"
```

### **Acil Durum (ÖNERİLMEZ!):**

```bash
# Pre-commit hooks'u atla (sadece acil durumda!)
git commit -m "fix: urgent" --no-verify

# Sonra mutlaka düzelt:
npm run fix
git add .
git commit -m "fix: code cleanup"
```

---

## 💾 DATABASE

### **Migration:**

```bash
# Migrate çalıştır
php artisan migrate

# Migration rollback
php artisan migrate:rollback

# Migration refresh (DİKKAT: Tüm veri silinir!)
php artisan migrate:refresh

# Migration status
php artisan migrate:status

# Yeni migration oluştur
php artisan make:migration create_users_table
```

### **Seeder:**

```bash
# Tüm seeder'ları çalıştır
php artisan db:seed

# Belirli seeder
php artisan db:seed --class=TalepSeeder

# Migration + Seed
php artisan migrate:fresh --seed
```

### **Database Backup:**

```bash
# Backup al
./scripts/database/backup-database.sh

# Schema kontrolü
./scripts/database/check-database-schema.sh

# Tablo schema export
php scripts/database/export-table-schema.php tablename

# Multi-table schema
php scripts/database/export-multi-table-schema.php
```

---

## 🧪 TESTING

### **Laravel Tests:**

```bash
# Tüm testleri çalıştır
php artisan test

# Belirli test
php artisan test --filter=TalepTest

# Coverage ile
php artisan test --coverage
```

### **Browser Testing:**

```bash
# AI page test
node scripts/testing/test-ai-page-simple.mjs

# Full page test
node scripts/testing/test-ai-full-page.mjs

# Admin test
node scripts/testing/admin-otomatik-test.sh
```

---

## 🔄 ESKİ KOMUTLAR DURUMU

### **✅ HÂLÂ GEÇERLİ OLAN:**

```bash
# Dokümantasyon
✅ cd docs/active/
✅ ./scripts/search-docs.sh "Context7"
✅ ./scripts/generate-doc-index.sh
✅ tar -xzf docs/archive/legacy-docs-2024-2025.tar.gz

# Yalıhan Bekçi
✅ ./scripts/bekci-watch.sh start
✅ ./scripts/bekci-watch.sh logs follow
✅ ./scripts/bekci-watch.sh stop

# Linting
✅ npm run lint
✅ npm run lint:js
✅ npm run lint:php
✅ npm run format
✅ php artisan standard:check
```

### **🆕 YENİ EKLENEN KOMUTLAR:**

```bash
# Setup (ilk kurulum)
🆕 npm run setup

# Fix (otomatik düzeltme)
🆕 npm run fix

# Check (tüm kontroller)
🆕 npm run check

# Component oluştur
🆕 php artisan make:form-component input

# Standart kontrol tipleri
🆕 php artisan standard:check --type=css
🆕 php artisan standard:check --type=js
🆕 php artisan standard:check --type=php
🆕 php artisan standard:check --type=blade
🆕 php artisan standard:check --type=context7
```

### **⚠️ DEĞİŞEN KULLANIM:**

```bash
# ESKİ:
php artisan context7:check

# YENİ (daha kapsamlı):
php artisan standard:check --type=context7
```

### **❌ ARTIK GEREKLİ OLMAYAN:**

```bash
# Manuel format (artık otomatik):
❌ prettier --write "**/*.js"
❌ eslint --fix "**/*.js"

# Manuel check (pre-commit yapar):
❌ php context7_final_compliance_checker.php

# Git commit öncesi manuel kontrol (artık otomatik):
❌ npm run lint before commit
```

---

## 🎯 KULLANIM SENARYOLARI

### **Senaryo 1: Sabah İşe Başlama**

```bash
# 1. Son değişiklikleri al
git pull

# 2. Dependencies güncelle (gerekirse)
composer install
npm install

# 3. Database migrate (gerekirse)
php artisan migrate

# 4. Cache temizle
php artisan cache:clear
php artisan view:clear

# 5. Bekçi başlat
./scripts/bekci-watch.sh start

# 6. Dev server başlat
php artisan serve &
npm run dev &

# 7. Canlı log takibi (opsiyonel)
./scripts/bekci-watch.sh logs follow
```

---

### **Senaryo 2: Yeni Özellik Geliştirme**

```bash
# 1. Bekçi çalışıyor mu kontrol
./scripts/bekci-watch.sh status

# 2. Kod yaz, test et...

# 3. Commit öncesi kontrol
npm run fix
php artisan standard:check

# 4. Commit
git add .
git commit -m "feat: yeni özellik"

# 5. Push
git push
```

---

### **Senaryo 3: Bug Düzeltme**

```bash
# 1. Sorunu tespit et

# 2. Dokümanda ara (gerekirse)
./scripts/search-docs.sh "bug keyword"

# 3. Düzelt

# 4. Test et
php artisan test

# 5. Lint & format
npm run fix

# 6. Commit
git commit -m "fix: bug açıklaması"
```

---

### **Senaryo 4: Akşam İş Bitimi**

```bash
# 1. Değişiklikleri commit et
git add .
git commit -m "chore: günlük çalışma"
git push

# 2. Bekçi durdur
./scripts/bekci-watch.sh stop

# 3. Server'ları durdur
# Ctrl+C (php artisan serve)
# Ctrl+C (npm run dev)

# 4. Log kontrolü (opsiyonel)
./scripts/bekci-watch.sh logs
```

---

## 💡 PRO TİPLER

### **Bash Aliases (Hızlı Erişim):**

```bash
# .bashrc veya .zshrc dosyana ekle:

# Yalıhan Emlak Aliases
alias ye-start='./scripts/bekci-watch.sh start'
alias ye-stop='./scripts/bekci-watch.sh stop'
alias ye-logs='./scripts/bekci-watch.sh logs follow'
alias ye-fix='npm run fix'
alias ye-check='php artisan standard:check'
alias ye-commit='git add . && git commit'
alias ye-docs='cd docs/active && ls -lh'
alias ye-search='./scripts/search-docs.sh'
alias ye-serve='php artisan serve'
alias ye-dev='npm run dev'

# Kullanım:
# ye-start → Bekçi başlat
# ye-fix → Otomatik düzelt
# ye-commit -m "message" → Commit yap
```

### **Klavye Kısayolları (VS Code):**

```json
// settings.json
{
  "terminal.integrated.commandsToSkipShell": [
    "workbench.action.terminal.sendSequence"
  ],
  "keybindings": [
    {
      "key": "ctrl+shift+f",
      "command": "workbench.action.terminal.sendSequence",
      "args": { "text": "npm run fix\u000D" }
    }
  ]
}
```

---

## 📊 KOMUT ÖNCELİK SIRASI

### **Günlük Kullanım (Sıklık Sırasına Göre):**

```
🥇 git commit -m "..."         (Her gün, sık sık)
🥈 npm run fix                 (Günde 3-5 kez)
🥉 php artisan standard:check  (Günde 2-3 kez)

4️⃣ ./scripts/bekci-watch.sh   (Günde 1-2 kez: start/stop)
5️⃣ ./scripts/search-docs.sh   (Haftada 5-10 kez)
6️⃣ php artisan serve          (Günde 1 kez)
7️⃣ npm run dev                (Günde 1 kez)
8️⃣ git push                   (Günde 1-3 kez)
9️⃣ php artisan cache:clear    (Haftada 2-3 kez)
🔟 php artisan migrate         (Haftada 1-2 kez)
```

---

## 🆘 SORUN GİDERME

### **Komut Çalışmıyor:**

```bash
# Permission hatası
chmod +x ./scripts/bekci-watch.sh
chmod +x ./scripts/search-docs.sh

# Composer autoload
composer dump-autoload

# NPM cache
npm cache clean --force
npm install

# Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### **Pre-commit Hooks Çalışmıyor:**

```bash
# Husky yeniden kur
npm install husky --save-dev
npx husky init
chmod +x .husky/pre-commit
```

---

**📌 Hızlı Referans:** Bu dosyayı `KOMUTLAR_REHBERI.md` olarak kaydet!

**Son Güncelleme:** 2025-10-30  
**Version:** 2.0  
**Status:** ACTIVE

---

**💡 İpucu:** Sık kullandığın komutları bash alias yap, çok hızlanırsın! 🚀

