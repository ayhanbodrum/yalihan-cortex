# Cursor Kuralları – Context7 Uyumlu Geliştirme

Kaynak otorite: `.context7/authority.json`, `docs/context7-rules.md`, `.context7/CONTEXT7_MEMORY_SYSTEM.md`

## Zorunlu Standartlar
- Tek layout: `admin.layouts.neo` kullanılacak. Başka layout eklenmeyecek.
- UI: Sadece Neo Design System sınıfları (`neo-`) ve mevcut Blade component’leri.
- Alan adları: `status|is_active|aktif` yasak → `status`. `il`/`il_id` yasak → `il`/`il_id`.
- Blade güvenliği: Null-fallback zorunlu, escape edilmiş çıktı kullanılacak.
- Rota isimleri: `admin.*` şeması, çakışan prefix yasak.
- Performans: N+1 yasak, gerekli yerlerde `with(...)` ve uygun index.
- AI: Öneri/analiz gösterecek, otomatik veri değiştirmeyecek.

## Yasak Pattern’lar (otomatik engelle)
```
status
is_active
aktif
il
il_id
ad_soyad
full_name
```

## Otomatik Yerine Koymalar
```
status      → status
is_active  → status
aktif      → status
il      → il
il_id   → il_id
ad_soyad   → tam_ad (yalnızca gösterim accessor’larında)
full_name  → name
```

## Dosya/Dizin Kuralları
- Sidebar tek kaynaktan: `resources/views/admin/layouts/sidebar.blade.php`.
- Admin sayfaları: `resources/views/admin/**` altında `@extends('admin.layouts.neo')` ile.
- `.context7/`, `docs/` içeriği referans kabul edilecek, değiştirilmez.

## Form Kuralları
- Form grid: `neo-form-group`, `neo-input`, `neo-select`, `neo-switch` kullanılacak.
- Hata/valid durumları görsel olarak belirtilecek.
- CSRF zorunlu; tüm fetch/AJAX isteklerinde `X-CSRF-TOKEN`.

## Liste/Tablo Kuralları
- Tablo: `neo-table`, responsive kapsayıcı: `neo-table-responsive`.
- Hızlı filtre butonları (status/rol gibi) ve CSV dışa aktarma üst aksiyonlarda.
- Sıralama bağlantıları: İsim ve tarih sütunları en az.
- Boş status kartı ve satır hover vurgusu zorunlu.

## Kod Örüntü Denetimi (Cursor içinde)
- Öneri üretirken yukarıdaki yasak kalıpları otomatik reddet ve onaylı karşılıkları öner.
- Layout, alan adları veya UI kit ihlali içeren snippet’leri düzeltme önerisiyle değiştir.

## İnşa Adımları
1) Yeni view: `@extends('admin.layouts.neo')` ile başla.
2) Header/banner: kısa başlık ve Context7 durumu.
3) İçerik: Neo grid ve form/tablolar.
4) Güvenlik ve null-fallback kontrolü.

## Onaylı Yardımcılar
- `.githooks` / `git_context7_hook.sh` pre-commit çalıştırır.
- `scripts/context7_final_compliance_checker.php` manuel kontrol için.

## Dil ve Yazım
- Arayüz metinleri Türkçe, veritabanı alan adları Context7 standardına göre İngilizce.

# Context7 Compliance Rules for Cursor IDE

## Database Fields - CRITICAL VIOLATIONS
- NEVER use `status` → Always use `status`
- NEVER use `is_active` → Always use `status`
- NEVER use `aktif` → Always use `status`
- NEVER use `il` or `il_id` → Always use `il` or `il_id`
- NEVER use `region_id` → Remove completely
- NEVER use `ad_soyad` → Use separate `ad` and `soyad`
- NEVER use `full_name` → Use `name`

## Model Relationships
- Use `il()` instead of `il()`
- Remove `bolge()` relationships completely

## CSS Classes
- Use Neo Design System: `neo-btn`, `neo-card`, `neo-form`
- NEVER use Bootstrap classes: `btn-`, `card-`, `form-`

## Authority Reference
Always check `.context7/authority.json` for the latest compliance rules.

## Auto-suggestions
When suggesting code, automatically apply Context7 rules without asking.
