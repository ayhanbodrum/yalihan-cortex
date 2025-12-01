# Alıhan Bekçi Kişisel Kurallar

## Temel İlke
- Tüm üretim `.context7/authority.json` standartlarına %100 uyumlu olmalı
- Yasak patternler asla kullanılmamalı; ihlal tespitlerinde otomatik düzeltme önerileri uygulanmalı

## Yasak Patternler
- Status: `enabled`, `aktif`, `durum`, `is_active` yerine `status`
- Sıralama: `order` yerine `display_order`
- Lokasyon: `sehir_id`, `semt_id` yerine `il_id`, `mahalle_id`
- Terminoloji: `musteri` yerine `kisi`
- CSS: `neo-*`, `btn-*` yasak; sadece Tailwind utilities
- Route: `crm.*` ve double prefix yasak; `admin.*` ve tek prefix zorunlu

## Zorunlu Standartlar
- Tailwind CSS: Pure Tailwind + zorunlu transition/animation
- JavaScript: Vanilla JS ONLY (ağır kütüphaneler yasak)
- Migration: `DB::statement()` kullanımı, index kontrolü, kolon tipi korunması
- Form: Tailwind utilities, dark mode desteği, erişilebilirlik

## Modüler Yapı
- Her modül kendi migration/seeder dosyalarını içerir ve senkronize çalışır
- Modüller arası iletişim sadece Service sınıfları ve Event'lerle yapılır
- Modül bağımlılıkları ilgili ServiceProvider içinde tanımlanır

## Güvenlik
- `.env` asla commitlenmez; API keyler `.env.local` içinde tutulur
- Hassas alanlar (örn. `tc_kimlik`, `iban`) encrypt edilir
- Loglarda kişisel veri saklanmaz; maskleme zorunlu

## Route ve API
- Yeni route eklerken: `routes/api/v1/{module}.php` yapısı
- ResponseService kullanımı zorunlu; hata mesajları standart
- Context7 ile uyumluluk: authority ve forbidden kontrolleri

## Çalışma Akışı
- Pre-commit: Context7 denetimleri; ihlal varsa commit engellenir
- CI/CD: `context7:check` ve coverage ≥ %70; seeder otomasyon
- Kod üretiminde: `authority.json` + `FORBIDDEN_PATTERNS.md` referansı

## Entegrasyon
- Analytics Dashboard: kişisel kurallar seçilebilir kaynak olarak kullanılır
- Admin Panel: Context7 ekranlarında kişisel kurallar bağlantısı görünür