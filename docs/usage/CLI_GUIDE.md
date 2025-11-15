# 🖥️ CLI GUIDE - Komut Satırı Kullanım Rehberi

**Yalıhan Emlak - Standart Sistem Komutları**

> **Amaç:** Standart sistem ile kolay çalışma, otomatik kontrol, hızlı geliştirme  
> **Hedef:** Terminal'den tek komutla her şeyi yap!

**Version:** 1.0.0  
**Last Updated:** 2025-10-30

---

## 🎯 HIZLI BAŞLANGIÇ

### **En Çok Kullanılan Komutlar:**

```bash
# 1. Standartlara uygunluk kontrolü (TÜM SİSTEM)
php artisan standard:check

# 2. Otomatik düzeltme (mümkün olanlar)
php artisan standard:check --fix

# 3. Sadece CSS kontrolü
php artisan standard:check --type=css

# 4. Yeni form component oluştur
php artisan make:form-component input

# 5. Context7 compliance kontrolü
php artisan context7:check

# 6. Tüm linter'ları çalıştır
npm run lint && ./vendor/bin/php-cs-fixer fix
```

---

## 📋 TÜM KOMUTLAR

### **1. STANDARD:CHECK - Standart Kontrol Sistemi** ⭐

#### **Temel Kullanım:**
```bash
# Tüm sistem kontrolü
php artisan standard:check

# Otomatik düzeltme ile
php artisan standard:check --fix

# Detaylı rapor oluştur
php artisan standard:check --report

# Hem düzelt hem rapor oluştur
php artisan standard:check --fix --report
```

#### **Tip Bazlı Kontroller:**
```bash
# Sadece CSS/Tailwind kontrolü
php artisan standard:check --type=css

# Sadece JavaScript kontrolü  
php artisan standard:check --type=js

# Sadece PHP kontrolü
php artisan standard:check --type=php

# Sadece Blade template kontrolü
php artisan standard:check --type=blade

# Sadece Context7 compliance
php artisan standard:check --type=context7
```

#### **Çıktı Örneği:**
```
🔍 Yalıhan Emlak - Standart Kontrol Sistemi

📋 Kontrol ediliyor: css
  ⚠️  css: 5 sorun bulundu
📋 Kontrol ediliyor: js
  ✅ js: Sorun yok
📋 Kontrol ediliyor: php
  ⚠️  php: 2 sorun bulundu

❌ Toplam 7 sorun bulundu.

📋 Bulunan Sorunlar:

▶ css:
  ✗ /resources/views/admin/talepler/index.blade.php:45
    Neo Class Usage: Neo-btn kullanımı bulundu. Tailwind'e geçilmeli.
  ✗ /resources/views/admin/ilanlar/create.blade.php:120
    Inline Style: Inline style kullanımı. Tailwind classes kullanın.

💡 Detaylı rapor için: php artisan standard:check --report
💡 Otomatik düzeltme için: php artisan standard:check --fix
```

#### **Rapor Dosyası:**
```json
// storage/logs/standard-check-2025-10-30-143022.json
{
  "timestamp": "2025-10-30T14:30:22Z",
  "total_issues": 7,
  "total_fixed": 3,
  "issues": {
    "css": [
      {
        "file": "/resources/views/admin/talepler/index.blade.php",
        "type": "Neo Class Usage",
        "message": "Neo-btn kullanımı bulundu. Tailwind'e geçilmeli.",
        "line": 45
      }
    ]
  },
  "fixed": {}
}
```

---

### **2. MAKE:FORM-COMPONENT - Component Oluştur** ⭐

#### **Kullanım:**
```bash
# Input component oluştur
php artisan make:form-component input

# Select component oluştur
php artisan make:form-component select

# Textarea component oluştur
php artisan make:form-component textarea

# Checkbox component oluştur
php artisan make:form-component checkbox

# Üzerine yaz (varsa)
php artisan make:form-component input --force
```

#### **Desteklenen Component'ler:**
```yaml
Mevcut Templates:
  - input (text, email, password, number, etc.)
  - select (dropdown)
  - textarea (multi-line text)
  - checkbox (single checkbox)
  - radio (radio buttons)
  - toggle (switch)
  - file (file upload)
```

#### **Çıktı Örneği:**
```
🎨 Form Component Oluşturuluyor: input

✅ Component oluşturuldu: resources/views/components/form/input.blade.php

📖 Kullanım örneği:
<x-form.input name="title" label="Başlık" required />
```

#### **Oluşturulan Component Özellikleri:**
- ✅ Tailwind CSS (pure, no Neo classes)
- ✅ Dark mode support
- ✅ Accessibility (ARIA labels)
- ✅ Validation support
- ✅ Error message display
- ✅ Help text support
- ✅ Required field indicator
- ✅ Disabled state
- ✅ Alpine.js integration (where needed)

---

### **3. CONTEXT7:CHECK - Context7 Validation**

#### **Kullanım:**
```bash
# Tüm sistem Context7 kontrolü
php artisan context7:check

# Belirli dizin kontrolü
php artisan context7:check app/Models

# Belirli dosya kontrolü
php artisan context7:check app/Models/Talep.php
```

#### **Kontrol Edilen Kurallar:**
```yaml
Forbidden Patterns (Yasaklı):
  - durum → use 'status'
  - aktif → use 'enabled'
  - is_active → use 'enabled'
  - sehir → use 'city'
  - sehir_id → use 'city_id'
  - musteriler → use 'kisiler'
  - ad_soyad → separate or 'full_name'

Required Patterns (Zorunlu):
  - Database migrations: English field names
  - Controllers: Type hints + return types
  - Models: Relationships properly defined
  - Blade: CSRF tokens in forms
```

---

### **4. NPM KOMUTLARI - Frontend**

#### **Development:**
```bash
# Vite dev server (hot reload)
npm run dev

# Build (production)
npm run build

# ESLint (JavaScript linting)
npm run lint

# ESLint with auto-fix
npm run lint:fix

# Prettier (formatting)
npm run format

# All checks
npm run check
```

#### **Package.json Önerilen Scriptler:**
```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "lint": "eslint resources/js --ext .js,.ts",
    "lint:fix": "eslint resources/js --ext .js,.ts --fix",
    "format": "prettier --write \"resources/**/*.{js,ts,css,blade.php}\"",
    "check": "npm run lint && npm run format"
  }
}
```

---

### **5. COMPOSER KOMUTLARI - Backend**

#### **PHP CS Fixer:**
```bash
# Tüm PHP dosyalarını düzelt
./vendor/bin/php-cs-fixer fix

# Sadece kontrol et (düzeltme)
./vendor/bin/php-cs-fixer fix --dry-run

# Belirli dizin
./vendor/bin/php-cs-fixer fix app/Models

# Belirli dosya
./vendor/bin/php-cs-fixer fix app/Http/Controllers/Admin/TalepController.php
```

#### **PHPStan (Static Analysis):**
```bash
# Full analysis
./vendor/bin/phpstan analyse

# Specific level (0-9)
./vendor/bin/phpstan analyse --level=6

# Specific directory
./vendor/bin/phpstan analyse app/Models
```

#### **Tests:**
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=TalepTest

# With coverage
php artisan test --coverage
```

---

## 🔄 WORKFLOW EXAMPLES

### **Scenario 1: Yeni Feature Geliştirme** 🚀

```bash
# 1. Standartları kontrol et (başlamadan önce)
php artisan standard:check

# 2. Yeni form component oluştur
php artisan make:form-component input

# 3. Development başlat
npm run dev

# 4. Kod yaz...

# 5. Commit öncesi kontroller
npm run lint:fix
./vendor/bin/php-cs-fixer fix
php artisan standard:check --fix

# 6. Son kontrol
php artisan standard:check
php artisan context7:check

# 7. Commit
git add .
git commit -m "feat(forms): add new input component"
```

---

### **Scenario 2: Bug Fix** 🐛

```bash
# 1. Standart kontrolü (hangi dosyalarda sorun var?)
php artisan standard:check --report

# 2. Hatayı düzelt...

# 3. Otomatik düzeltmeleri çalıştır
php artisan standard:check --fix
npm run lint:fix
./vendor/bin/php-cs-fixer fix

# 4. Manuel düzeltmeleri yap

# 5. Test et
php artisan test

# 6. Final check
php artisan standard:check

# 7. Commit
git commit -m "fix(validation): fix email regex pattern"
```

---

### **Scenario 3: Code Review** 👀

```bash
# 1. PR'ı fetch et
git fetch origin pull/123/head:pr-123
git checkout pr-123

# 2. Dependencies güncelle
composer install
npm install

# 3. Standart kontrolleri çalıştır
php artisan standard:check --report

# 4. Linter'ları çalıştır
npm run lint
./vendor/bin/php-cs-fixer fix --dry-run

# 5. Tests çalıştır
php artisan test

# 6. Yorumlarını yaz ve approve/request changes
```

---

### **Scenario 4: Daily Start** 🌅

```bash
# Morning ritual
git pull origin main
composer install
npm install
php artisan migrate
php artisan cache:clear
php artisan view:clear

# Standart kontrolü (clean slate?)
php artisan standard:check

# Dev server başlat
npm run dev
```

---

## 🤖 SİSTEM NASIL PROJEYİ TANIYOR?

### **1. Yalıhan Bekçi (MCP Server)**

Yalıhan Bekçi projenizi şu şekilde tanıyor:

```yaml
Knowledge Base (.yalihan-bekci/knowledge/):
  ✅ context7-compliance-report-2025-10-22.md
  ✅ css-migration-strategy.md
  ✅ PHASE1-COMPLETED.md
  ✅ STANDARD-TOOLS-GUIDE.md
  ✅ arsa-yazlik-migrations-2025-10-22.json

Resources (MCP Resources):
  ✅ context7://rules/forbidden (Yasaklı pattern'ler)
  ✅ context7://rules/required (Zorunlu pattern'ler)
  ✅ context7://system/structure (Sistem yapısı)
  ✅ context7://patterns/common (Sık hatalar)

Tools (8 adet):
  - context7_validate: Kod Context7'ye uygun mu?
  - get_context7_rules: Kuralları getir
  - check_pattern: Pattern kontrolü
  - get_system_structure: Sistem yapısını göster
  - get_learned_errors: Öğrenilmiş hataları göster
  - md_duplicate_detector: Duplicate MD dosyaları bul
  - knowledge_consolidator: Knowledge'ı birleştir
  - ai_prompt_manager: AI prompt'ları yönet
```

### **2. Context7 Authority (.context7/authority.json)**

```json
{
  "forbidden_patterns": [
    "durum",
    "aktif",
    "is_active",
    "sehir",
    "sehir_id",
    "ad_soyad",
    "full_name",
    "btn-",
    "card-",
    "form-control"
  ],
  "required_patterns": [
    "status",
    "enabled",
    "city",
    "city_id",
    "neo-btn (transition)",
    "Tailwind classes"
  ],
  "compliance_percentage": 98.82,
  "last_updated": "2025-10-30"
}
```

### **3. Tailwind Config (tailwind.config.js)**

```javascript
// Sistem Neo classes'ı Tailwind plugin olarak tanıyor
module.exports = {
  plugins: [
    function ({ addComponents }) {
      addComponents({
        '.neo-btn': { /* ... */ },
        '.neo-card': { /* ... */ },
        '.neo-input': { /* ... */ },
        // ... diğer Neo components
      });
    },
  ],
};
```

### **4. Vite Config (vite.config.js)**

```javascript
// Sistem dosya yapısını tanıyor
export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        // ... diğer entry points
      ],
    }),
  ],
});
```

### **5. Composer & Package.json**

```yaml
Dependencies:
  Laravel: 10.x
  Tailwind CSS: 3.4.18
  Alpine.js: 3.15.0
  
Dev Dependencies:
  ESLint: Code quality
  Prettier: Formatting
  PHP CS Fixer: PSR-12
  PHPStan: Static analysis
```

---

## 💡 PROTİPLER

### **Günlük Kullanım:**

```bash
# 1. Sabah (çalışmaya başlarken)
alias morning='git pull && composer install && npm install && php artisan migrate && php artisan standard:check'

# 2. Commit öncesi (her seferinde)
alias precommit='npm run lint:fix && ./vendor/bin/php-cs-fixer fix && php artisan standard:check --fix'

# 3. Akşam (iş bitiminde)
alias evening='php artisan standard:check --report && git log --oneline -5'
```

**Bash/Zsh Profile'a Ekle (.bashrc veya .zshrc):**

```bash
# Yalıhan Emlak - Development Aliases
alias ye-check='php artisan standard:check'
alias ye-fix='php artisan standard:check --fix'
alias ye-lint='npm run lint:fix && ./vendor/bin/php-cs-fixer fix'
alias ye-test='php artisan test'
alias ye-build='npm run build'
alias ye-morning='git pull && composer install && npm install && php artisan migrate && php artisan cache:clear'
alias ye-component='php artisan make:form-component'
```

---

## 📊 CHECKLIST

### **Commit Öncesi (ZORUNLU):**
```bash
✅ php artisan standard:check --fix
✅ npm run lint:fix
✅ ./vendor/bin/php-cs-fixer fix
✅ php artisan test
✅ php artisan context7:check
✅ Console errors temizle (F12)
✅ Conventional commit message
```

### **PR Öncesi (ZORUNLU):**
```bash
✅ Tüm testler geçti
✅ php artisan standard:check (0 error)
✅ npm run build (başarılı)
✅ Documentation güncellendi
✅ CHANGELOG güncellendi
✅ Screenshots eklendi (UI changes)
✅ Review checklist dolduruldu
```

---

## 🆘 TROUBLESHOOTING

### **"Command not found" Hatası:**
```bash
# Composer autoload'u güncelle
composer dump-autoload

# Artisan cache temizle
php artisan cache:clear
php artisan config:clear
```

### **"Pre-commit hooks çalışmıyor":**
```bash
# Husky'yi yeniden kur
npm install husky --save-dev
npx husky install
```

### **"PHP CS Fixer hatası":**
```bash
# Vendor'ı yeniden yükle
rm -rf vendor
composer install
```

### **"ESLint config bulunamadı":**
```bash
# ESLint config oluştur
npm init @eslint/config
```

---

## 📞 YARDIM

### **Sıralama:**
1. **CLI_GUIDE.md** (bu dosya)
2. **STANDARDIZATION_GUIDE.md**
3. **MODERNIZATION_PLAN.md**
4. **php artisan help <command>**
5. **Team Lead**

---

**🎯 Hedef:** Terminal'den tek komutla mükemmel kod!

**Last Updated:** 2025-10-30  
**Version:** 1.0.0  
**Status:** ACTIVE

---

**💡 Unutma:** Bu komutlar zamanını kurtarmak, hataları önlemek ve kod kalitesini yükseltmek için var. Her gün kullan! 🚀

