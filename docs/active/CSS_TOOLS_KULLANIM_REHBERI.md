# 🎨 CSS Araçları Kullanım Rehberi

**Son Güncelleme:** Kasım 2025  
**Araçlar:** Prettier + Stylelint + Tailwind CSS Plugin

---

## 📋 İçindekiler

1. [Nasıl Tespit Edilir?](#nasıl-tespit-edilir)
2. [Kullanıcı Nasıl Görür?](#kullanıcı-nasıl-görür)
3. [IDE Entegrasyonu](#ide-entegrasyonu)
4. [Otomatik Düzeltme](#otomatik-düzeltme)
5. [Manuel Kullanım](#manuel-kullanım)

---

## 🔍 Nasıl Tespit Edilir?

### 1. **Terminal'den Kontrol**

```bash
# CSS hatalarını tespit et
npm run lint:css

# Çıktı örneği:
# resources/css/app.css
#   12:5  ✖  Unexpected duplicate "color" property
#   25:3  ✖  Expected empty line before rule
```

### 2. **Pre-commit Hook (Otomatik)**

Git commit yaparken otomatik çalışır:

```bash
git commit -m "feat: yeni özellik"

# Otomatik çalışır:
# ✅ Running stylelint...
# ✅ Running prettier...
# ✅ All checks passed!
```

### 3. **GitHub Actions (CI/CD)**

Her push'ta otomatik kontrol edilir:

```yaml
# .github/workflows/code-quality.yml
- name: Run Stylelint
  run: npm run lint:css
```

---

## 👁️ Kullanıcı Nasıl Görür?

### **VS Code / Cursor IDE'de**

#### 1. **Hata Göstergeleri (Kırmızı Çizgiler)**

```css
/* ❌ Hata: Duplicate property */
.example {
  color: red;
  color: blue; /* ← Kırmızı çizgi ile işaretlenir */
}
```

**Görünüm:**
- Kırmızı çizgi altında hata
- Hover ile hata mesajı
- Problems panel'de liste

#### 2. **Uyarı Göstergeleri (Sarı Çizgiler)**

```css
/* ⚠️ Uyarı: Empty rule */
.empty-rule {
  /* ← Sarı çizgi ile işaretlenir */
}
```

#### 3. **Format Uyarıları**

```html
<!-- ❌ Prettier: Tailwind sınıfları sırasız -->
<div class="text-red-500 bg-white p-4 rounded-lg">
  <!-- ← Format uyarısı -->
</div>
```

**Düzeltilmiş:**
```html
<!-- ✅ Prettier: Tailwind sınıfları sıralı -->
<div class="rounded-lg bg-white p-4 text-red-500">
  <!-- ← Otomatik sıralandı -->
</div>
```

---

## 🛠️ IDE Entegrasyonu

### **VS Code / Cursor Ayarları**

#### 1. **Extensions (Uzantılar)**

```json
{
  "recommendations": [
    "esbenp.prettier-vscode",      // Prettier
    "stylelint.vscode-stylelint"    // Stylelint
  ]
}
```

#### 2. **Settings (Ayarlar)**

```json
{
  // Prettier otomatik format
  "editor.formatOnSave": true,
  "editor.defaultFormatter": "esbenp.prettier-vscode",
  
  // Stylelint otomatik düzelt
  "stylelint.validate": ["css", "scss"],
  "css.validate": false,  // VS Code'un kendi CSS validator'ını kapat
  
  // Tailwind CSS IntelliSense
  "tailwindCSS.experimental.classRegex": [
    ["class:\\s*?[\"'`]([^\"'`]*).*?[\"'`]", "([^\"'`]*)"]
  ]
}
```

#### 3. **Görsel Göstergeler**

**Problems Panel:**
```
Problems (3)
  ✖ resources/css/app.css
    Line 12: Unexpected duplicate "color" property
    Line 25: Expected empty line before rule
    Line 45: Unknown property "backgroun-color"
```

**Status Bar:**
```
Stylelint: 3 errors | Prettier: Ready
```

---

## ⚡ Otomatik Düzeltme

### **1. Format on Save (Kaydetme Sırasında)**

Dosyayı kaydettiğinde otomatik formatlanır:

```html
<!-- Önce -->
<div class="text-red-500 bg-white p-4 rounded-lg hover:bg-gray-100">

<!-- Sonra (Otomatik) -->
<div class="rounded-lg bg-white p-4 text-red-500 hover:bg-gray-100">
```

### **2. Quick Fix (Hızlı Düzeltme)**

**Mac:** `Cmd + .`  
**Windows/Linux:** `Ctrl + .`

```css
/* Hata üzerinde Cmd + . */
.example {
  color: red;
  color: blue; /* ← Cmd + . → "Remove duplicate property" */
}
```

### **3. Command Palette**

**Mac:** `Cmd + Shift + P`  
**Windows/Linux:** `Ctrl + Shift + P`

```
> Format Document          → Prettier ile formatla
> Stylelint: Fix all auto-fixable problems
> Prettier: Format Document
```

---

## 🖥️ Manuel Kullanım

### **1. CSS Hatalarını Kontrol Et**

```bash
# Tüm CSS dosyalarını kontrol et
npm run lint:css

# Belirli dosyayı kontrol et
npx stylelint "resources/css/app.css"

# Sadece hataları göster (uyarıları gizle)
npx stylelint "resources/css/app.css" --quiet
```

### **2. CSS Hatalarını Otomatik Düzelt**

```bash
# Tüm hataları otomatik düzelt
npx stylelint "resources/**/*.css" --fix

# veya
npm run lint:fix
```

### **3. Prettier ile Formatla**

```bash
# Tüm dosyaları formatla
npm run format

# Belirli dosyayı formatla
npx prettier --write "resources/css/app.css"

# Sadece kontrol et (düzeltme)
npx prettier --check "resources/css/app.css"
```

### **4. Tailwind Sınıflarını Sırala**

```bash
# Blade dosyalarındaki Tailwind sınıflarını sırala
npx prettier --write "resources/views/**/*.blade.php"
```

---

## 📊 Örnek Senaryolar

### **Senaryo 1: Yeni CSS Dosyası Yazdın**

```bash
# 1. Dosyayı kaydet
# 2. Otomatik formatlanır (Format on Save)
# 3. Hatalar görünür (Stylelint)
# 4. Cmd + . ile hızlı düzelt
```

### **Senaryo 2: Git Commit Yapıyorsun**

```bash
git add .
git commit -m "feat: yeni stil"

# Otomatik çalışır:
# ✅ stylelint --fix
# ✅ prettier --write
# ✅ Commit başarılı!
```

### **Senaryo 3: Pull Request Açıyorsun**

```bash
git push origin feature/new-styles

# GitHub Actions otomatik çalışır:
# ✅ npm run lint:css
# ✅ npm run format
# ✅ Checks passed!
```

---

## 🎯 Tailwind CSS Sınıf Sıralaması

### **Önce (Manuel):**
```html
<div class="text-red-500 bg-white p-4 rounded-lg hover:bg-gray-100">
```

### **Sonra (Prettier Tailwind Plugin):**
```html
<div class="rounded-lg bg-white p-4 text-red-500 hover:bg-gray-100">
```

**Sıralama Mantığı:**
1. Layout (rounded-lg, border)
2. Spacing (p-4, m-2)
3. Colors (bg-white, text-red-500)
4. Typography (font-bold, text-sm)
5. Effects (hover:bg-gray-100, shadow)

---

## 🔧 Sorun Giderme

### **Problem: Stylelint çalışmıyor**

```bash
# 1. Uzantıyı kontrol et
code --list-extensions | grep stylelint

# 2. Ayarları kontrol et
cat .stylelintrc.json

# 3. Manuel çalıştır
npx stylelint "resources/css/app.css"
```

### **Problem: Prettier Tailwind sınıfları sıralamıyor**

```bash
# 1. Plugin kurulu mu?
npm list prettier-plugin-tailwindcss

# 2. Config kontrol et
cat .prettierrc.json | grep plugins

# 3. Manuel formatla
npx prettier --write "resources/views/**/*.blade.php"
```

---

## 📚 Daha Fazla Bilgi

- **Prettier:** https://prettier.io/
- **Stylelint:** https://stylelint.io/
- **Prettier Tailwind Plugin:** https://github.com/tailwindlabs/prettier-plugin-tailwindcss

---

**Son Güncelleme:** Kasım 2025  
**Durum:** ✅ Aktif ve Çalışıyor

