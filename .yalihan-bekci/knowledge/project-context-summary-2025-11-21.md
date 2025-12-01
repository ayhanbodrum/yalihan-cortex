# 🧠 Project Context Memory - 2025-11-21

Bu dosya, AI asistanı tarafından okunan ve hafızaya alınan kritik proje dokümanlarının özetini içerir.

## 📅 Kayıt Tarihi: 21 Kasım 2025

### 1. 📂 Dokümantasyon Yapısı (`docs/README.md`)
- **Ana Giriş:** `docs/index.md` ve `docs/README.md`.
- **Kategoriler:**
  - `active/`: Güncel durum ve planlar.
  - `technical/`: Teknik detaylar (DB, API).
  - `ai/`: AI entegrasyonları.
  - `modules/`: Modül dokümanları.
- **Durum:** Dokümantasyon Kasım 2025 itibarıyla güncel ve sadeleştirilmiş.

### 2. 📊 Proje Durumu (`docs/active/PROJE_DURUM_VE_TAMAMLAMA_OZETI_2025-11.md`)
- **Tamamlanan Fazlar:**
  - **Phase 1:** Temel AI altyapısı.
  - **Phase 2:** İlan Taslak Asistanı.
  - **Phase 3:** Mesaj Taslak Asistanı.
- **Standartlar:** `.context7` ve `.yalihan-bekci` kuralları aktif.

### 3. 🎨 Frontend Redesign Planı (`docs/frontend-global-redesign-plan.md`)
- **Hedef:** Context7 uyumlu, uluslararası odaklı modern arayüz.
- **Teknolojiler:** Tailwind CSS (saf), Blade Components.
- **Yasaklılar:** `neo-*` sınıfları.
- **Yeni Bileşenler:** `header-switcher`, `property-card-global`, `ai-guide-card`.
- **Sayfalar:** `international.blade.php` (yeni), mevcut sayfaların dönüşümü.

### 4. 📋 Form Standartları (`docs/FORM_STANDARDS.md`)
- **Zorunlu Kullanım:** `App\Helpers\FormStandards` sınıfı.
  - `FormStandards::input()`
  - `FormStandards::select()`
  - `FormStandards::checkbox()`
  - `FormStandards::button()`
- **Gereksinimler:**
  - Dark Mode desteği.
  - WCAG AAA kontrast oranı.
  - Tutarlı padding ve border radius.

### 5. 🔧 Araçlar (`docs/migration-auto-fixer.md`)
- **Migration Fixer:** `./scripts/fix-migrations.sh`
  - Syntax hatalarını düzeltir.
  - PHPDoc temizliği yapar.
  - Context7 uyumluluğunu sağlar.

### 6. 👑 AUTHORITY RULES (`.context7/authority.json`) - EKLENDİ
**Bu kurallar projenin tartışılmaz anayasasıdır:**

- **CSS Framework:**
  - ✅ **Pure Tailwind CSS** zorunlu.
  - ❌ `neo-*` sınıfları ve Neo Design System **YASAK**.
  - ❌ Inline style kullanımı yasak.
  - ⚠️ Tüm interaktif elementlerde `transition-all` zorunlu.

- **Veritabanı Standartları:**
  - ✅ `display_order` (❌ `order` yasak).
  - ✅ `status` (❌ `is_active`, `aktif`, `is_published` yasak).
  - ✅ `mahalle_id` (❌ `semt_id` yasak).
  - ✅ `il_id` (❌ `sehir_id` yasak).

- **Kod Kalitesi & Performans:**
  - ❌ `User::all()` kullanımı yasak (Rol bazlı filtreleme zorunlu).
  - ✅ Live search işlemlerinde **debounce (300ms)** zorunlu.
  - ✅ Dropdown'larda `style="color-scheme: light dark;"` zorunlu.

- **Harita Sistemi:**
  - ✅ Leaflet.js 1.9.4 kullanımı.
  - ✅ Rate limiting (1 req/sec) zorunlu.

---
**Not:** Bu bilgiler, sonraki geliştirmelerde referans olarak kullanılacaktır.
