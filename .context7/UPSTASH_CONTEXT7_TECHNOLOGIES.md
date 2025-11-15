# Upstash Context7 MCP - Teknoloji Desteği ve Çalışma Prensibi

## 🎯 Upstash Context7 MCP Nasıl Çalışır?

Upstash Context7 MCP, **genel kütüphane dokümantasyonu** çeker. Resmi kaynaklardan (GitHub, npm, packagist vb.) güncel dokümantasyonu alır.

### Desteklenen Teknolojiler:

Upstash Context7 MCP şu teknolojileri destekler:

#### Backend Framework'ler:
- ✅ **Laravel** (PHP framework)
- ✅ **Symfony** (PHP framework)
- ✅ **Express.js** (Node.js)
- ✅ **FastAPI** (Python)
- ✅ **Django** (Python)
- ✅ **Rails** (Ruby)

#### Frontend Framework'ler:
- ✅ **React**
- ✅ **Vue.js**
- ✅ **Angular**
- ✅ **Svelte**

#### Veritabanları:
- ✅ **MySQL**
- ✅ **PostgreSQL**
- ✅ **MongoDB**
- ✅ **Redis**

#### Diğer Popüler Kütüphaneler:
- ✅ **Tailwind CSS**
- ✅ **Alpine.js**
- ✅ **TypeScript**
- ✅ **Node.js**
- ✅ **npm packages**

## 🔍 Bizim Kullandığımız Teknolojiler ve Upstash Context7 MCP

### ✅ Upstash Context7 MCP'nin Bildiği Teknolojilerimiz:

1. **Laravel 10.x** ✅
   - Upstash Context7 MCP Laravel dokümantasyonunu çeker
   - Eloquent ORM, Migrations, Controllers vb. konularında bilgi sahibi

2. **MySQL 8.0+** ✅
   - MySQL dokümantasyonunu çeker
   - SQL syntax, indexes, constraints vb. konularında bilgi sahibi

3. **Tailwind CSS** ✅
   - Tailwind CSS dokümantasyonunu çeker
   - Utility classes, responsive design vb. konularında bilgi sahibi

4. **Alpine.js** ✅
   - Alpine.js dokümantasyonunu çeker
   - Reactive components, directives vb. konularında bilgi sahibi

5. **PHP 8.2+** ✅
   - PHP dokümantasyonunu çeker
   - Modern PHP features, type hints vb. konularında bilgi sahibi

### ⚠️ Upstash Context7 MCP'nin BİLMEDİĞİ (Proje-Spesifik):

1. **Neo Design System** ❌
   - Bu bizim özel tasarım sistemimiz
   - Upstash Context7 MCP bunu bilmez
   - **Çözüm**: Yalıhan Bekçi Context7 kullanılır

2. **Context7 Standartları** ❌
   - `status` field (NOT `enabled`)
   - `display_order` (NOT `order`)
   - `il_id` (NOT `sehir_id`)
   - **Çözüm**: Yalıhan Bekçi Context7 kullanılır

3. **Proje-Spesifik Pattern'ler** ❌
   - Bizim migration standartlarımız
   - Bizim controller yapımız
   - Bizim route naming convention'larımız
   - **Çözüm**: Yalıhan Bekçi Context7 kullanılır

## 🔄 İki Sistemin Birlikte Çalışması

### Senaryo: "Context7 kullan, Laravel migration oluştur"

```
1. Upstash Context7 MCP:
   → resolve-library-id("Laravel") → "/laravel/laravel"
   → get-library-docs("/laravel/laravel", topic: "migrations")
   → Laravel'in resmi migration dokümantasyonunu çeker
   → Güncel Laravel 10.x syntax'ını sağlar

2. Yalıhan Bekçi Context7:
   → get_context7_rules() → Proje kurallarını getirir
   → check_pattern("migration") → Migration pattern'lerini kontrol eder
   → validate(code) → Context7 standartlarına uygunluğu kontrol eder
   → Proje-spesifik standartları uygular:
     - DB::statement() kullanımı zorunlu
     - Index kontrolü zorunlu
     - Kolon tipi korunması zorunlu
     - status field (NOT enabled)
     - display_order (NOT order)

3. Birleştirme:
   → Upstash Context7 MCP: Güncel Laravel syntax'ı
   → Yalıhan Bekçi Context7: Proje standartları
   → Sonuç: Context7 uyumlu, güncel Laravel migration kodu
```

## 💡 Örnekler

### Örnek 1: Laravel Eloquent Relationships

**Upstash Context7 MCP Bilgisi:**
```php
// Laravel'in resmi dokümantasyonundan
$user->posts()->hasMany(Post::class);
```

**Yalıhan Bekçi Context7 Bilgisi:**
```php
// Proje standartlarımıza göre
// ✅ status field kullan (NOT enabled)
// ✅ display_order kullan (NOT order)
// ✅ il_id kullan (NOT sehir_id)
```

**Birleşik Sonuç:**
```php
// Context7 uyumlu kod
class Ilan extends Model {
    public function il() {
        return $this->belongsTo(Il::class); // ✅ il_id kullanıyor
    }
    
    protected $casts = [
        'status' => 'boolean', // ✅ enabled DEĞİL
        'display_order' => 'integer', // ✅ order DEĞİL
    ];
}
```

### Örnek 2: Tailwind CSS Kullanımı

**Upstash Context7 MCP Bilgisi:**
```html
<!-- Tailwind CSS'in resmi dokümantasyonundan -->
<button class="px-4 py-2 bg-blue-500 text-white rounded">
    Button
</button>
```

**Yalıhan Bekçi Context7 Bilgisi:**
```html
<!-- Proje standartlarımıza göre -->
<!-- ✅ transition-all duration-200 ZORUNLU -->
<!-- ✅ dark: variant ZORUNLU -->
<!-- ✅ neo-* classları YASAK -->
```

**Birleşik Sonuç:**
```html
<!-- Context7 uyumlu kod -->
<button class="px-4 py-2 bg-blue-500 dark:bg-blue-600 
               text-white rounded
               transition-all duration-200
               hover:bg-blue-600 dark:hover:bg-blue-700
               focus:ring-2 focus:ring-blue-500">
    Button
</button>
```

## 🎯 Sonuç

### Upstash Context7 MCP:
- ✅ **Genel kütüphane dokümantasyonu** çeker
- ✅ **Güncel syntax** sağlar
- ✅ **Resmi dokümantasyon** kaynaklarından bilgi alır
- ❌ **Proje-spesifik kurallar** bilmez

### Yalıhan Bekçi Context7:
- ✅ **Proje kuralları** yönetir
- ✅ **Context7 standartları** uygular
- ✅ **Proje-spesifik pattern'ler** kontrol eder
- ❌ **Genel kütüphane dokümantasyonu** çekmez

### İkisi Birlikte:
- ✅ **Güncel dokümantasyon** + **Proje standartları** = **Mükemmel kod**
- ✅ Upstash Context7 MCP: "Nasıl yapılır?" sorusunu cevaplar
- ✅ Yalıhan Bekçi Context7: "Proje standartlarına uygun mu?" sorusunu cevaplar

## 📚 Referanslar

- [Upstash Context7 MCP Documentation](https://github.com/upstash/context7)
- `.context7/authority.json` - Proje standartları
- `.cursorrules` - Context7 Dual System Integration

---

**Son Güncelleme**: 2025-11-12
**Durum**: ✅ Her iki sistem birlikte çalışıyor

