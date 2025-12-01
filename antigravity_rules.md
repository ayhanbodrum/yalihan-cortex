# Antigravity Rule File

**Versiyon:** 3.0.0  
**Son Güncelleme:** 30 Kasım 2025  
**Kaynak:** `.context7/authority.json` + **Yalıhan‑Bekçi** MCP Sistemi

Bu dosya, **Antigravity** AI asistanının proje içinde uyacağı kuralları `.context7/authority.json` dosyasından türetir. Aşağıdaki bölümler, projenin **Context7** standartları ve **Yalıhan‑Bekçi** kurallarına göre hazırlanmıştır.

---

## 🤖 MCP Entegrasyonu

Antigravity, aşağıdaki MCP sunucuları ile entegre çalışır:

- **Yalıhan Bekçi MCP** (`yalihan-bekci-mcp.js`) - AI öğrenme ve öğretme sistemi
- **Context7 Validator MCP** (`context7-validator-mcp.js`) - Real-time validation ve auto-fix
- **Laravel MCP** (`laravel-mcp.cjs`) - Laravel Artisan komutları ve database erişimi

**MCP Dokümantasyonu:** `mcp-servers/README.md`

---

## 1. CSS Framework & UI Standards

### Zorunlu Kurallar

- ✅ **Pure Tailwind CSS** zorunlu
- ❌ `neo-*` sınıfları ve Neo Design System **TAMAMEN YASAK**
- ❌ Bootstrap classes (`btn-*`, `card-*`, `col-*`) **YASAK**
- ✅ Tüm interaktif elementlerde `transition-all` veya `transition-colors` zorunlu
- ✅ Dark mode zorunlu; her element `dark:` varyantına sahip olmalı

### Dropdown & Form Standartları

```html
<!-- ✅ DOĞRU -->
<select class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white" 
        style="color-scheme: light dark;">
  <option>Seçenek</option>
</select>

<!-- ❌ YANLIŞ -->
<select class="form-select">
  <option>Seçenek</option>
</select>
```

### Hover & Interactive States

```html
<!-- ✅ DOĞRU -->
<button class="bg-blue-500 hover:bg-blue-600 transition-all">
  Kaydet
</button>

<!-- ❌ YANLIŞ - transition eksik -->
<button class="bg-blue-500 hover:bg-blue-600">
  Kaydet
</button>
```

---

## 2. Database Field Naming

### Standart Alan İsimleri

| Kural                 | Yasak                          | Zorunlu          | Açıklama                    |
| --------------------- | ------------------------------ | ---------------- | --------------------------- |
| `order`               | ❌                             | `display_order`  | Sıralama için               |
| `status`              | ✅ (tek `status` kullanılmalı) | -                | Boolean durum için          |
| `is_active` / `aktif` | ❌                             | `status`         | Aktiflik durumu için        |
| `is_published`        | ❌                             | `status`         | Yayın durumu için           |
| `enabled`             | ❌                             | `status`         | Etkinlik durumu için        |
| `mahalle_id`          | ✅                             | -                | Mahalle referansı için      |
| `semt_id`             | ❌                             | `mahalle_id`     | Mahalle yerine kullanılmaz  |
| `il_id`               | ✅                             | -                | İl referansı için           |
| `sehir_id`            | ❌                             | `il_id`          | İl yerine kullanılmaz       |
| `region_id`           | ❌ (tamamen kaldırılmalı)      | -                | Kullanılmaz                 |
| `musteri_id`          | ❌                             | `kisi_id`        | Müşteri yerine kişi         |
| `musteri_segmenti`    | ❌                             | `kisi_segmenti`  | Müşteri yerine kişi         |

### Migration Örnekleri

```php
// ✅ DOĞRU
Schema::create('ilanlar', function (Blueprint $table) {
    $table->id();
    $table->foreignId('kisi_id')->constrained('kisiler');
    $table->integer('display_order')->default(0);
    $table->boolean('status')->default(true);
    $table->timestamps();
});

// ❌ YANLIŞ
Schema::create('ilanlar', function (Blueprint $table) {
    $table->id();
    $table->foreignId('musteri_id')->constrained('musteriler'); // ❌
    $table->integer('order')->default(0); // ❌
    $table->boolean('is_active')->default(true); // ❌
    $table->timestamps();
});
```

---

## 3. Forbidden Patterns & Code Quality

### Live Search

```javascript
// ✅ DOĞRU - 300ms debounce
let searchTimeout;
searchInput.addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch(e.target.value);
    }, 300);
});

// ❌ YANLIŞ - debounce yok
searchInput.addEventListener('input', (e) => {
    performSearch(e.target.value); // Her tuşta arama!
});
```

### User Retrieval

```php
// ✅ DOĞRU - Rol bazlı filtreleme
$users = User::role('danışman')->get();

// ❌ YANLIŞ - Tüm kullanıcılar
$users = User::all(); // YASAK!
```

### Toast Sistemleri

```javascript
// ✅ DOĞRU
window.toast.success('İşlem başarılı');

// ❌ YANLIŞ
toastr.success('İşlem başarılı'); // Eski sistem
customToast('success', 'İşlem başarılı'); // Deprecated
```

### Blade Layout

```blade
{{-- ✅ DOĞRU --}}
@extends('admin.layouts.neo')

{{-- ❌ YANLIŞ --}}
@extends('layouts.app')
```

### API Response

```php
// ✅ DOĞRU
return ResponseService::success($data, 'İşlem başarılı');
return ResponseService::error('Hata mesajı', 400);

// ❌ YANLIŞ
return response()->json(['success' => true, 'data' => $data]);
```

---

## 4. Map System Standards (Leaflet.js)

### Versiyon ve Konfigürasyon

```javascript
// ✅ DOĞRU Konfigürasyon
const map = L.map('map', {
    center: [41.0082, 28.9784],
    zoom: 13,
    zoomControl: true
});

// Rate limiting için
let lastNominatimRequest = 0;
const NOMINATIM_DELAY = 1000; // 1 saniye

async function geocode(address) {
    const now = Date.now();
    const timeSinceLastRequest = now - lastNominatimRequest;
    
    if (timeSinceLastRequest < NOMINATIM_DELAY) {
        await new Promise(resolve => 
            setTimeout(resolve, NOMINATIM_DELAY - timeSinceLastRequest)
        );
    }
    
    lastNominatimRequest = Date.now();
    // Geocoding işlemi...
}
```

### Silent Update Pattern

```javascript
// ✅ DOĞRU - Silent update flag
let isSilentUpdate = false;

map.on('moveend', (e) => {
    if (isSilentUpdate) {
        isSilentUpdate = false;
        return;
    }
    // Normal update işlemleri...
});

function updateMapSilently(lat, lng) {
    isSilentUpdate = true;
    map.setView([lat, lng], map.getZoom());
}
```

### Debug Mode

```javascript
// ✅ DOĞRU - Debug mode kontrolü
const DEBUG_MODE = false; // Production'da false

if (DEBUG_MODE) {
    console.log('Map initialized:', map);
}
```

---

## 5. General Enforcement

### Pre-commit Hooks

```bash
# Context7 validation
php artisan context7:check

# Migration validation
php artisan context7:validate-migration

# Code style
./vendor/bin/php-cs-fixer fix
```

### CI/CD Pipeline

```yaml
# .gitlab-ci.yml veya .github/workflows/
- php artisan context7:check
- php artisan context7:validate-migration --all
- npm run lint
```

### Şablon Kontrolleri

- ✅ Yeni migration'lar otomatik olarak Context7 kurallarına göre kontrol edilir
- ✅ Blade dosyaları Tailwind CSS kullanımı için kontrol edilir
- ✅ JavaScript dosyaları debounce pattern'leri için kontrol edilir

---

## 6. Naming Conventions

### Türkçe - İngilizce Karşılıklar

| Türkçe          | İngilizce (Kullanılacak) | Yasak Alternatifler    |
| --------------- | ------------------------ | ---------------------- |
| Müşteri         | `kisi` (Kişi)            | `musteri`, `customer`  |
| Müşteri Segmenti| `kisi_segmenti`          | `musteri_segmenti`     |
| Semt            | `mahalle`                | `semt`, `neighborhood` |
| Şehir           | `il`                     | `sehir`, `city`        |
| Aktif           | `status` (boolean)       | `aktif`, `is_active`   |
| Sıra            | `display_order`          | `order`, `sira`        |

---

## 7. MCP Validation Tools

### Otomatik Kontrol

```bash
# Context7 Validator MCP kullanımı
# Dosya validasyonu
mcp-tool validate_file --file="app/Models/Ilan.php" --auto-fix=true

# Proje geneli validasyon
mcp-tool validate_project --scope="migrations" --auto-fix=false

# Compliance kontrolü
mcp-tool check_compliance --detailed=true
```

### Yalıhan Bekçi MCP Öğrenme

```bash
# İşlemden öğrenme
mcp-tool learn_from_action \
  --action-type="context7_fix" \
  --context="Migration order → display_order düzeltmesi"

# Pattern analizi
mcp-tool analyze_pattern \
  --pattern-type="context7_violations" \
  --time-range="last_week"
```

---

## 8. Referans Dosyalar

### Context7 Standartları
- `.context7/authority.json` - Master otorite dosyası
- `docs/FORM_STANDARDS.md` - Form standartları
- `YALIHAN_BEKCI_EGITIM_DOKUMANI.md` - Yalıhan Bekçi eğitim dokümanı

### MCP Dokümantasyonu
- `mcp-servers/README.md` - MCP sunucuları dokümantasyonu
- `.yalihan-bekci/README.md` - Yalıhan Bekçi sistemi

### Proje Dokümantasyonu
- `docs/index.md` - Ana dokümantasyon
- `docs/ai-training/` - AI eğitim dokümanları

---

## 9. Hata Ayıklama

### Context7 İhlalleri

```bash
# Tüm ihlalleri listele
php artisan context7:check --verbose

# Belirli bir dosyayı kontrol et
php artisan context7:validate-migration database/migrations/2025_11_30_*.php

# Otomatik düzeltme
php artisan context7:check --fix
```

### MCP Sunucu Sorunları

```bash
# MCP sunucularını kontrol et
cd mcp-servers
npm run start:all

# Tek tek başlat
npm run start:bekci
npm run start:validator
npm run start:laravel
```

---

## 10. Önemli Notlar

> [!IMPORTANT]
> - Tüm yeni kod **Context7** kurallarına tam uyumlu olmalıdır
> - Mevcut kod sadece düzenlendiğinde Context7'ye uyarlanır
> - MCP sunucuları sürekli öğrenir ve pattern'leri günceller

> [!WARNING]
> - `neo-*` sınıfları kullanımı **KESİNLİKLE YASAK**
> - `User::all()` kullanımı **KESİNLİKLE YASAK**
> - Bootstrap classes kullanımı **KESİNLİKLE YASAK**

> [!CAUTION]
> - Migration'larda `order` yerine `display_order` kullanılmalı
> - Boolean durum için `is_active` yerine `status` kullanılmalı
> - `musteri_*` yerine `kisi_*` kullanılmalı

---

**Son Güncelleme:** 30 Kasım 2025  
**Versiyon:** 3.0.0  
**Durum:** ✅ Aktif ve Güncel

_Bu dosya, Antigravity'nin proje içinde otomatik kural kontrolü ve öneri üretimi için referans kaynağıdır. MCP sunucuları ile entegre çalışır ve sürekli güncellenir._
