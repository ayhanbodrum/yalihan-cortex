# 🎯 Commit Stratejisi - 22 Kasım 2025

## 📊 Mevcut Durum
- **327 yeni dosya** (untracked)
- **Çok sayıda silinen dosya** (temizlik)
- **Çok sayıda değiştirilmiş dosya** (özellikler)

## ✅ Önerilen Commit Sırası

### 1️⃣ **Temizlik Commit'i** (İlk)
```bash
# Gereksiz dosyaları .gitignore'a ekle
echo "storage/framework/sessions/*" >> .gitignore
echo "storage/framework/views/*" >> .gitignore
echo ".DS_Store" >> .gitignore
echo "screenshots/*" >> .gitignore

# Temizlik commit'i
git add .gitignore
git commit -m "chore: Add temporary files to .gitignore

- Session files
- Compiled views
- Screenshots
- System files"
```

### 2️⃣ **Status Field Standardization** (Kritik)
```bash
git add app/Enums/IlanStatus.php
git add app/Traits/Filterable.php
git add app/Http/Controllers/Admin/IlanController.php
git add app/Http/Controllers/Admin/MyListingsController.php
git add database/migrations/2025_11_22_*.php
git add .context7/STATUS_COLUMN_GLOBAL_STANDARD.md
git commit -m "feat: Global status field standardization

- Convert status columns to TINYINT(1) across 16 tables
- Update IlanStatus enum with consistent values
- Fix Filterable trait to handle string statuses correctly
- Update all controllers to use IlanStatus enum
- Add migration for global status standardization

BREAKING CHANGE: Status fields now use boolean instead of varchar"
```

### 3️⃣ **AI Integration Improvements** (Özellik)
```bash
git add app/Http/Controllers/Admin/AI/IlanAIController.php
git add app/Http/Controllers/Admin/AISettingsController.php
git add app/Services/AI/OllamaService.php
git add resources/views/admin/ai-settings/index.blade.php
git add resources/js/admin/ai-register.js
git add vite.config.js
git commit -m "feat: Dynamic AI model selection and real AI integration

- Replace mock AI methods with real OllamaService calls
- Add dynamic model selection from settings
- Update AI settings page with provider configuration
- Add API key and URL fields for AI providers
- Fix Vite manifest for ai-register.js
- Add helper methods for location/category name resolution"
```

### 4️⃣ **UX Improvements - Listing Forms** (Özellik)
```bash
git add resources/views/admin/ilanlar/create.blade.php
git add resources/views/admin/ilanlar/edit.blade.php
git add resources/views/admin/ilanlar/components/*.blade.php
git add resources/views/admin/ilanlar/partials/stable/_kisi-secimi.blade.php
git commit -m "feat: UX improvements for listing forms

- Reorder sections for AI-optimized flow
- Add sticky navigation with active section highlighting
- Standardize form action buttons
- Improve dark mode readability for form fields
- Reduce excessive bottom padding
- Add smooth scroll to navigation
- Fix progress bar calculations"
```

### 5️⃣ **Map Controls Simplification** (UI)
```bash
git add resources/views/components/context7/map-picker.blade.php
git commit -m "refactor: Simplify map controls

- Remove redundant zoom buttons
- Keep only Standard/Satellite toggle
- Improve UI consistency"
```

### 6️⃣ **Documentation Updates** (Dokümantasyon)
```bash
git add docs/active/*.md
git add .context7/authority.json
git commit -m "docs: Update documentation for recent changes

- Update AI Assistant data sources documentation
- Update listing form analysis
- Update comprehensive audit reports
- Update Context7 authority.json"
```

### 7️⃣ **Cleanup - Remove Dead Code** (Temizlik)
```bash
# Sadece silinen dosyaları commit et
git add -u
git commit -m "chore: Remove dead code and outdated documentation

- Remove archived documentation
- Remove unused controllers
- Remove old backup files
- Clean up .context7 and .yalihan-bekci archives"
```

### 8️⃣ **New Features and Files** (Yeni Özellikler)
```bash
# Yeni dosyaları ekle (dikkatli seç)
git add app/Http/Controllers/Admin/SystemMonitorController.php
git add app/Models/*.php
git add database/migrations/*.php
git add database/seeders/*.php
git add resources/views/errors/*.blade.php
git commit -m "feat: Add new features and models

- Add SystemMonitorController for AI monitoring
- Add new models (AIContractDraft, AIMessage, etc.)
- Add new migrations for owner private features
- Add custom error pages
- Add new seeders"
```

## ⚠️ DİKKAT

### Commit YAPMADAN ÖNCE:
1. ✅ Test et: `php artisan test`
2. ✅ Linter kontrolü: `./vendor/bin/phpstan analyse`
3. ✅ Migration kontrolü: `php artisan migrate:status`
4. ✅ Cache temizle: `php artisan cache:clear`

### Commit YAPARKEN:
- Her commit **tek bir özellik/işlevsellik** içermeli
- Commit mesajları **açıklayıcı** olmalı
- **BREAKING CHANGE** varsa belirtilmeli

### Commit YAPTIKTAN SONRA:
- Push yapmadan önce **bir kez daha test et**
- Eğer hata varsa `git commit --amend` ile düzelt

## 🚀 Hızlı Başlangıç

Eğer tüm değişiklikleri tek seferde commit etmek istersen:

```bash
# 1. Önce .gitignore'ı güncelle
echo -e "\n# Temporary files\nstorage/framework/sessions/*\nstorage/framework/views/*\n.DS_Store\nscreenshots/*" >> .gitignore

# 2. Tüm değişiklikleri ekle
git add -A

# 3. Commit et
git commit -m "feat: Major update - Status standardization, AI integration, UX improvements

- Global status field standardization (boolean)
- Real AI integration with dynamic model selection
- UX improvements for listing forms
- Map controls simplification
- Documentation updates
- Dead code cleanup

BREAKING CHANGE: Status fields now use boolean instead of varchar"
```

## 📝 Commit Mesaj Formatı

```
<type>: <subject>

<body>

<footer>
```

**Type'lar:**
- `feat`: Yeni özellik
- `fix`: Hata düzeltmesi
- `refactor`: Kod refactoring
- `chore`: Temizlik, build, config
- `docs`: Dokümantasyon
- `style`: Formatting
- `test`: Test ekleme

**Örnek:**
```
feat: Add dynamic AI model selection

- Replace hardcoded model with settings-based selection
- Add API key and URL configuration
- Update OllamaService to use dynamic model

Closes #123
```

